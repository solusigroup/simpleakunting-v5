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
            'columns' => ['no_anggota', 'nik', 'nama_lengkap', 'jenis_kelamin', 'alamat', 'telepon', 'email', 'tanggal_daftar', 'status']
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
            'columns' => ['nama_pelanggan', 'alamat', 'telepon', 'email', 'saldo_awal_piutang']
        ],
        'pemasok' => [
            'label' => 'Pemasok',
            'table' => 'pemasok',
            'columns' => ['nama_pemasok', 'alamat', 'telepon', 'email', 'saldo_awal_hutang']
        ],
        'persediaan' => [
            'label' => 'Persediaan',
            'table' => 'master_persediaan',
            'columns' => ['kode_barang', 'nama_barang', 'jenis_barang', 'satuan', 'harga_beli', 'harga_jual', 'stok_awal', 'stok_minimum', 'akun_persediaan', 'akun_hpp', 'akun_penjualan']
        ],
        'akun' => [
            'label' => 'Chart of Accounts (COA)',
            'table' => 'akun',
            'columns' => ['kode_akun', 'nama_akun', 'tipe_akun', 'saldo_normal', 'saldo_awal']
        ],
    ];

    /**
     * Show import/export page
     */
    public function index()
    {
        $modules = $this->modules;
        
        // Hide no_anggota from display columns as it's auto-generated
        if (isset($modules['anggota'])) {
            $modules['anggota']['columns'] = array_values(array_filter($modules['anggota']['columns'], fn($col) => $col !== 'no_anggota'));
        }
        
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
            fputcsv($file, $config['columns'], ';');
            
            // Write data
            foreach ($data as $row) {
                $rowData = [];
                foreach ($config['columns'] as $col) {
                    $rowData[] = $row->$col ?? '';
                }
                fputcsv($file, $rowData, ';');
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

        $callback = function() use ($config, $module) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            $columns = $config['columns'];
            if ($module === 'anggota') {
                $columns = array_values(array_filter($columns, fn($col) => $col !== 'no_anggota'));
            }

            // Write header only
            fputcsv($file, $columns, ';');
            
            // Add sample row for reference
            $sample = [];
            foreach ($columns as $col) {
                $sample[] = 'contoh_' . $col;
            }
            fputcsv($file, $sample, ';');
            
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
        
        // Define truly required columns to prevent total failure on optional ones
        $requiredColumns = [
            'anggota' => ['nik', 'nama_lengkap'],
            'pelanggan' => ['nama_pelanggan'],
            'pemasok' => ['nama_pemasok'],
            'persediaan' => ['kode_barang', 'nama_barang'],
            'akun' => ['kode_akun', 'nama_akun', 'tipe_akun'],
        ];

        try {
            $pathname = $file->getPathname();
            $handle = fopen($pathname, 'r');
            $firstLine = fgets($handle);
            $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
            
            $commaCount = substr_count($firstLine, ',');
            $semicolonCount = substr_count($firstLine, ';');
            $delimiter = ($semicolonCount > $commaCount) ? ';' : ',';
            
            $header = str_getcsv($firstLine, $delimiter);
            $header = array_map(function($h) {
                return trim(preg_replace('/[[:^print:]]/', '', $h));
            }, $header);

            $columnAliases = [
                'stok_akhir' => 'stok_saat_ini',
                'kode_pelanggan' => 'nama_pelanggan', // Mapping old code to name if applicable
                'jenis' => 'jenis_barang',
                'kode' => 'kode_barang',
                'nama' => 'nama_barang',
            ];

            $header = array_map(function($h) use ($columnAliases) {
                return $columnAliases[$h] ?? $h;
            }, $header);
            
            $expectedColumns = $config['columns'];
            $missingRequired = array_diff($requiredColumns[$module] ?? [], $header);
            
            if (!empty($missingRequired)) {
                fclose($handle);
                return back()->with('error', 'Gagal Impor: Kolom wajib berikut tidak ditemukan: ' . implode(', ', $missingRequired));
            }

            DB::beginTransaction();

            if ($request->mode === 'replace') {
                DB::table($config['table'])->delete();
            }

            $imported = 0;
            $errors = [];
            $lineNumber = 1;

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $lineNumber++;
                if (empty(array_filter($row))) continue;

                try {
                    $data = [];
                    foreach ($header as $i => $col) {
                        if (!in_array($col, $expectedColumns)) continue;
                        
                        $value = trim($row[$i] ?? '');
                        if ($value === '' || strpos($value, 'contoh_') === 0) {
                            $value = preg_match('/stok|harga|jumlah|saldo|total|bunga|tenor|provisi|biaya/i', $col) ? 0 : null;
                        }
                        
                        if ($value !== null && preg_match('/tanggal|date/i', $col)) {
                            if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $value, $matches)) {
                                $value = $matches[3] . '-' . $matches[2] . '-' . $matches[1];
                            }
                        }
                        
                        $data[$col] = $value;
                    }

                    // Auto-sync current balances/stock with initial ones if current is missing or zero
                    if ($module === 'pelanggan') {
                        $data['saldo_terkini_piutang'] = $data['saldo_awal_piutang'] ?? 0;
                    } elseif ($module === 'pemasok') {
                        $data['saldo_terkini_hutang'] = $data['saldo_awal_hutang'] ?? 0;
                    } elseif ($module === 'persediaan') {
                        $data['stok_saat_ini'] = $data['stok_awal'] ?? 0;
                    }

                    $data['created_at'] = now();
                    $data['updated_at'] = now();

                    $this->insertRecord($module, $config['table'], $data);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$lineNumber}: " . $e->getMessage();
                }
            }

            fclose($handle);

            if (count($errors) > 0 && $imported === 0) {
                DB::rollBack();
                return back()->with('error', 'Import gagal total. Kesalahan pertama: ' . $errors[0]);
            }

            DB::commit();

            $status = count($errors) > 0 ? 'warning' : 'success';
            $message = "Berhasil impor {$imported} data {$config['label']}.";
            if (count($errors) > 0) {
                $message .= " Namun, " . count($errors) . " baris gagal (Cek limit 5 error pertama: " . implode('; ', array_slice($errors, 0, 5)) . ")";
            }

            return back()->with($status, $message);

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            return back()->with('error', 'Sistem error saat membaca file: ' . $e->getMessage());
        }
    }

    /**
     * Insert record with module-specific handling
     */
    protected function insertRecord(string $module, string $table, array $data)
    {
        // Remove sample data markers (already handled in loop, but here for safety)
        foreach ($data as $key => $value) {
            if (is_string($value) && strpos($value, 'contoh_') === 0) {
                $data[$key] = null;
            }
        }

        // Module-specific handling & Defaults for non-nullable fields
        switch ($module) {
            case 'anggota':
                if (!empty($data['jenis_kelamin'])) {
                    $jk = strtoupper(trim($data['jenis_kelamin']));
                    if (in_array($jk, ['L', 'P'])) {
                        $data['jenis_kelamin'] = $jk;
                    } elseif (in_array($jk, ['LAKI-LAKI', 'LAKI - LAKI', 'LAKI LAKI', 'LAKI', 'PRIA', 'MALE'])) {
                        $data['jenis_kelamin'] = 'L';
                    } elseif (in_array($jk, ['PEREMPUAN', 'WANITA', 'FEMALE'])) {
                        $data['jenis_kelamin'] = 'P';
                    } else {
                        $data['jenis_kelamin'] = 'L';
                    }
                } else {
                    $data['jenis_kelamin'] = 'L';
                }

                $data['tanggal_daftar'] = $data['tanggal_daftar'] ?? date('Y-m-d');

                if (!empty($data['status'])) {
                    $status = strtolower(trim($data['status']));
                    $status = str_replace([' ', '-'], '_', $status);
                    if (in_array($status, ['aktif', 'active'])) {
                        $data['status'] = 'aktif';
                    } elseif (in_array($status, ['non_aktif', 'nonaktif', 'inactive', 'non_active'])) {
                        $data['status'] = 'non_aktif';
                    } elseif (in_array($status, ['keluar', 'exit'])) {
                        $data['status'] = 'keluar';
                    } else {
                        $data['status'] = 'aktif';
                    }
                } else {
                    $data['status'] = 'aktif';
                }

                if (!empty($data['nik'])) {
                    if (DB::table($table)->where('nik', $data['nik'])->exists()) {
                        throw new \Exception("NIK {$data['nik']} sudah terdaftar");
                    }
                }
                if (!empty($data['no_anggota'])) {
                    if (DB::table($table)->where('no_anggota', $data['no_anggota'])->exists()) {
                        throw new \Exception("No Anggota {$data['no_anggota']} sudah terdaftar");
                    }
                } else {
                    $data['no_anggota'] = \App\Models\Anggota::generateNoAnggota();
                }
                break;

            case 'simpanan':
                $data['jenis_transaksi'] = strtolower(trim($data['jenis_transaksi'] ?? 'setor'));
                if (!in_array($data['jenis_transaksi'], ['setor', 'tarik'])) {
                    $data['jenis_transaksi'] = 'setor';
                }
                
                // Set default akun_kas_bank if not set or invalid
                if (empty($data['akun_kas_bank']) || !DB::table('akun')->where('kode_akun', $data['akun_kas_bank'])->exists()) {
                    $defaultKas = DB::table('akun')
                        ->where('tipe_akun', 'like', '%Kas%')
                        ->orWhere('tipe_akun', 'like', '%Bank%')
                        ->orWhere('nama_akun', 'like', '%Kas%')
                        ->orWhere('nama_akun', 'like', '%Bank%')
                        ->value('kode_akun');
                    $data['akun_kas_bank'] = $defaultKas ?? '11101'; // Fallback to common cash account code
                }
                
                $data['created_by'] = auth()->id() ?? 1;
                
                if (!empty($data['no_transaksi'])) {
                    if (DB::table($table)->where('no_transaksi', $data['no_transaksi'])->exists()) {
                        throw new \Exception("No Transaksi {$data['no_transaksi']} sudah ada");
                    }
                }
                break;

            case 'pinjaman':
                $data['metode_bunga'] = strtolower(trim($data['metode_bunga'] ?? 'flat'));
                if (!in_array($data['metode_bunga'], ['flat', 'anuitas', 'efektif'])) {
                    $data['metode_bunga'] = 'flat';
                }
                
                $data['status'] = strtolower(trim($data['status'] ?? 'draft'));
                $validStatuses = ['draft', 'pending_approval', 'approved', 'rejected', 'disbursed', 'active', 'lunas', 'macet'];
                if (!in_array($data['status'], $validStatuses)) {
                    $data['status'] = 'draft';
                }
                
                // Set default akun_kas_bank if not set or invalid
                if (empty($data['akun_kas_bank']) || !DB::table('akun')->where('kode_akun', $data['akun_kas_bank'])->exists()) {
                    $defaultKas = DB::table('akun')
                        ->where('tipe_akun', 'like', '%Kas%')
                        ->orWhere('tipe_akun', 'like', '%Bank%')
                        ->orWhere('nama_akun', 'like', '%Kas%')
                        ->orWhere('nama_akun', 'like', '%Bank%')
                        ->value('kode_akun');
                    $data['akun_kas_bank'] = $defaultKas ?? '11101'; // Fallback to common cash account code
                }
                
                $data['created_by'] = auth()->id() ?? 1;
                
                if (!empty($data['no_pinjaman'])) {
                    if (DB::table($table)->where('no_pinjaman', $data['no_pinjaman'])->exists()) {
                        throw new \Exception("No Pinjaman {$data['no_pinjaman']} sudah ada");
                    }
                }
                break;

            case 'pelanggan':
                // Check if already exists by name (optional, but good for gaps)
                break;

            case 'pemasok':
                break;

            case 'persediaan':
                $data['satuan'] = $data['satuan'] ?? 'Pcs';
                
                $validJenis = ['barang_dagang', 'bahan_baku', 'barang_jadi', 'barang_dalam_proses', 'aset_biologis', 'jasa'];
                $jenisInput = strtolower(str_replace(' ', '_', $data['jenis_barang'] ?? ''));
                if (in_array($jenisInput, $validJenis)) {
                    $data['jenis_barang'] = $jenisInput;
                } else {
                    $data['jenis_barang'] = 'barang_dagang';
                }

                if (!empty($data['kode_barang'])) {
                    if (DB::table($table)->where('kode_barang', $data['kode_barang'])->exists()) {
                        throw new \Exception("Kode barang {$data['kode_barang']} sudah ada");
                    }
                }
                break;

            case 'akun':
                if (!empty($data['kode_akun'])) {
                    if (DB::table($table)->where('kode_akun', $data['kode_akun'])->exists()) {
                        // Update instead of insert for COA
                        $updateData = $data;
                        unset($updateData['created_at']);
                        DB::table($table)->where('kode_akun', $data['kode_akun'])->update($updateData);
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
                fputcsv($file, ['=== ' . strtoupper($config['label']) . ' ==='], ';');
                fputcsv($file, $config['columns'], ';');
                
                $data = DB::table($config['table'])->get();
                
                foreach ($data as $row) {
                    $rowData = [];
                    foreach ($config['columns'] as $col) {
                        $rowData[] = $row->$col ?? '';
                    }
                    fputcsv($file, $rowData, ';');
                }
                
                // Empty line between modules
                fputcsv($file, [], ';');
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
