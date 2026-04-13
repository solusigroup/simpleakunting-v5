<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\MasterPersediaan;
use App\Models\Akun;

class ImportExportController extends Controller
{
    /**
     * Available modules for import/export
     */
    protected $modules = [
        'anggota' => [
            'label' => 'Anggota Koperasi',
            'table' => 'anggota',
            'columns' => ['no_anggota', 'nik', 'nama_lengkap', 'alamat', 'telepon', 'email', 'tanggal_daftar', 'status']
        ],
        'simpanan' => [
            'label' => 'Simpanan',
            'table' => 'simpanan',
            'columns' => ['no_transaksi', 'tanggal', 'id_anggota', 'id_jenis_simpanan', 'jenis_transaksi', 'jumlah', 'keterangan']
        ],
        'pinjaman' => [
            'label' => 'Pinjaman',
            'table' => 'pinjaman',
            'columns' => ['no_pinjaman', 'id_anggota', 'id_jenis_pinjaman', 'tanggal_pengajuan', 'jumlah_pinjaman', 'bunga_pertahun', 'metode_bunga', 'tenor', 'provisi', 'biaya_admin', 'status']
        ],
        'pelanggan' => [
            'label' => 'Pelanggan',
            'table' => 'pelanggan',
            'columns' => ['kode_pelanggan', 'nama_pelanggan', 'alamat', 'telepon', 'email']
        ],
        'pemasok' => [
            'label' => 'Pemasok',
            'table' => 'pemasok',
            'columns' => ['kode_pemasok', 'nama_pemasok', 'alamat', 'telepon', 'email']
        ],
        'persediaan' => [
            'label' => 'Persediaan',
            'table' => 'master_persediaan',
            'columns' => ['kode_barang', 'nama_barang', 'satuan', 'harga_beli', 'harga_jual', 'stok_awal', 'stok_saat_ini']
        ],
        'akun' => [
            'label' => 'Chart of Accounts (COA)',
            'table' => 'akun',
            'columns' => ['kode_akun', 'nama_akun', 'tipe_akun', 'saldo_normal', 'kategori']
        ],
    ];

    /**
     * Show import/export page
     */
    public function index()
    {
        $modules = $this->modules;
        
        // Get count for each module
        $counts = [];
        foreach ($modules as $key => $module) {
            $counts[$key] = DB::table($module['table'])->count();
        }

        return view('import-export.index', compact('modules', 'counts'));
    }

    /**
     * Export data to CSV
     */
    public function export(Request $request, string $module)
    {
        if (!isset($this->modules[$module])) {
            return back()->with('error', 'Modul tidak valid.');
        }

        $config = $this->modules[$module];
        $data = DB::table($config['table'])->get();

        $filename = $module . '_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $config) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write header
            fputcsv($file, $config['columns']);
            
            // Write data
            foreach ($data as $row) {
                $rowData = [];
                foreach ($config['columns'] as $col) {
                    $rowData[] = $row->$col ?? '';
                }
                fputcsv($file, $rowData);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Download template for import
     */
    public function template(string $module)
    {
        if (!isset($this->modules[$module])) {
            return back()->with('error', 'Modul tidak valid.');
        }

        $config = $this->modules[$module];
        $filename = $module . '_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($config) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write header only
            fputcsv($file, $config['columns']);
            
            // Add sample row for reference
            $sample = [];
            foreach ($config['columns'] as $col) {
                $sample[] = 'contoh_' . $col;
            }
            fputcsv($file, $sample);
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Import data from CSV
     */
    public function import(Request $request, string $module)
    {
        if (!isset($this->modules[$module])) {
            return back()->with('error', 'Modul tidak valid.');
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'mode' => 'required|in:append,replace',
        ]);

        $config = $this->modules[$module];
        $file = $request->file('file');
        
        try {
            $pathname = $file->getPathname();
            $handle = fopen($pathname, 'r');
            
            // Read first line to detect delimiter and handle BOM
            $firstLine = fgets($handle);
            
            // Skip BOM if present
            $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
            
            // Simple delimiter detection: comma vs semicolon
            $commaCount = substr_count($firstLine, ',');
            $semicolonCount = substr_count($firstLine, ';');
            $delimiter = ($semicolonCount > $commaCount) ? ';' : ',';
            
            // Parse header from first line
            $header = str_getcsv($firstLine, $delimiter);
            
            // Clean headers: trim and remove any remaining invisible characters
            $header = array_map(function($h) {
                // Remove non-printable characters and trim
                return trim(preg_replace('/[[:^print:]]/', '', $h));
            }, $header);

            // Column aliases: map old/alternative column names to current DB column names
            $columnAliases = [
                'stok_akhir' => 'stok_saat_ini',
                'kode_pelanggan' => 'kode_pelanggan',
            ];

            // Remap headers using aliases
            $header = array_map(function($h) use ($columnAliases) {
                return $columnAliases[$h] ?? $h;
            }, $header);
            
            // Validate header
            $expectedColumns = $config['columns'];
            
            // Check if all expected columns are present
            $missingColumns = array_diff($expectedColumns, $header);
            $extraColumns = array_diff($header, $expectedColumns);
            
            if (!empty($missingColumns)) {
                fclose($handle);
                $errorMsg = 'Kolom CSV tidak sesuai.';
                $errorMsg .= ' Kolom yang kurang: ' . implode(', ', $missingColumns) . '.';
                if (!empty($extraColumns)) {
                    $errorMsg .= ' Kolom tidak dikenal: ' . implode(', ', $extraColumns) . '.';
                }
                $errorMsg .= ' Kolom yang diharapkan: ' . implode(', ', $expectedColumns) . '.';
                return back()->with('error', $errorMsg);
            }

            DB::beginTransaction();

            // Clear table if replace mode
            if ($request->mode === 'replace') {
                DB::table($config['table'])->truncate();
            }

            $imported = 0;
            $errors = [];
            $lineNumber = 1;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $lineNumber++;
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                try {
                    $data = [];
                    foreach ($header as $i => $col) {
                        // Only include columns that are in the expected list
                        if (!in_array($col, $expectedColumns)) {
                            continue;
                        }
                        $value = trim($row[$i] ?? '');
                        
                        // Handle empty values
                        if ($value === '' || $value === 'contoh_' . $col) {
                            // Default numeric columns to 0 instead of null
                            if (preg_match('/stok|harga|jumlah|saldo|total|bunga|tenor|provisi|biaya/i', $col)) {
                                $value = 0;
                            } else {
                                $value = null;
                            }
                        }
                        
                        // Convert date formats (DD-MM-YYYY or DD/MM/YYYY) to MySQL format (YYYY-MM-DD)
                        if ($value !== null && preg_match('/tanggal|date/i', $col)) {
                            if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $value, $matches)) {
                                // DD-MM-YYYY or DD/MM/YYYY
                                $value = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                            } elseif (preg_match('/^(\d{4})[\/\-](\d{2})[\/\-](\d{2})$/', $value)) {
                                // Already in YYYY-MM-DD format, keep as is
                            }
                        }
                        
                        $data[$col] = $value;
                    }


                    // Add timestamps
                    $data['created_at'] = now();
                    $data['updated_at'] = now();

                    // Insert based on module specific handling
                    $this->insertRecord($module, $config['table'], $data);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$lineNumber}: " . $e->getMessage();
                }
            }

            fclose($handle);

            if (count($errors) > 0 && $imported === 0) {
                DB::rollBack();
                return back()->with('error', 'Import gagal. Errors: ' . implode('; ', array_slice($errors, 0, 5)));
            }

            DB::commit();

            $message = "Berhasil impor {$imported} data {$config['label']}.";
            if (count($errors) > 0) {
                $message .= " Ada " . count($errors) . " baris yang gagal diimpor.";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Insert record with module-specific handling
     */
    protected function insertRecord(string $module, string $table, array $data)
    {
        // Remove sample data markers
        foreach ($data as $key => $value) {
            if (is_string($value) && strpos($value, 'contoh_') === 0) {
                $data[$key] = null;
            }
        }

        // Module-specific handling
        switch ($module) {
            case 'anggota':
                // Check for duplicate NIK or no_anggota
                if (!empty($data['nik'])) {
                    $exists = DB::table($table)->where('nik', $data['nik'])->exists();
                    if ($exists) {
                        throw new \Exception("NIK {$data['nik']} sudah ada");
                    }
                }
                if (!empty($data['no_anggota'])) {
                    $exists = DB::table($table)->where('no_anggota', $data['no_anggota'])->exists();
                    if ($exists) {
                        throw new \Exception("No Anggota {$data['no_anggota']} sudah ada");
                    }
                }
                break;

            case 'pelanggan':
                if (!empty($data['kode_pelanggan'])) {
                    $exists = DB::table($table)->where('kode_pelanggan', $data['kode_pelanggan'])->exists();
                    if ($exists) {
                        throw new \Exception("Kode pelanggan {$data['kode_pelanggan']} sudah ada");
                    }
                }
                break;

            case 'pemasok':
                if (!empty($data['kode_pemasok'])) {
                    $exists = DB::table($table)->where('kode_pemasok', $data['kode_pemasok'])->exists();
                    if ($exists) {
                        throw new \Exception("Kode pemasok {$data['kode_pemasok']} sudah ada");
                    }
                }
                break;

            case 'persediaan':
                if (!empty($data['kode_barang'])) {
                    $exists = DB::table($table)->where('kode_barang', $data['kode_barang'])->exists();
                    if ($exists) {
                        throw new \Exception("Kode barang {$data['kode_barang']} sudah ada");
                    }
                }
                break;

            case 'akun':
                if (!empty($data['kode_akun'])) {
                    $exists = DB::table($table)->where('kode_akun', $data['kode_akun'])->exists();
                    if ($exists) {
                        // Update instead of insert
                        DB::table($table)->where('kode_akun', $data['kode_akun'])->update($data);
                        return;
                    }
                }
                break;
        }

        DB::table($table)->insert($data);
    }

    /**
     * Export all data to single Excel-compatible file
     */
    public function exportAll()
    {
        $filename = 'all_data_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($this->modules as $key => $config) {
                // Write module header
                fputcsv($file, ['=== ' . strtoupper($config['label']) . ' ===']);
                fputcsv($file, $config['columns']);
                
                $data = DB::table($config['table'])->get();
                
                foreach ($data as $row) {
                    $rowData = [];
                    foreach ($config['columns'] as $col) {
                        $rowData[] = $row->$col ?? '';
                    }
                    fputcsv($file, $rowData);
                }
                
                // Empty line between modules
                fputcsv($file, []);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
