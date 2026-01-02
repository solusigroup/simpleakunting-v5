<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bom;
use App\Models\BomDetail;
use App\Models\Persediaan;
use App\Models\Produksi;
use App\Models\ProduksiDetail;
use App\Models\AsetBiologis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ManufacturingTest extends TestCase
{
    // Note: Use RefreshDatabase to migrate the in-memory sqlite db
    // use RefreshDatabase; 

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Assuming we have a superhero/admin user or creating a dummy one
        // Since we are running on existing DB, let's try to find or create one safely if auth is needed
        // For testing, we can simulate auth
        $this->user = User::first() ?? User::factory()->create();
        $this->user->role = 'superuser';
        // Disable CSRF for testing (and other middleware to simplify)
        $this->withoutMiddleware();
    }

    public function test_bom_index_page_is_accessible()
    {
        $response = $this->actingAs($this->user)->get(route('manufacturing.bom.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_bom()
    {
         // Require products: One Finished good, One Raw Material
        $finishedGood = Persediaan::first() ?? Persediaan::create([
            'kode_barang' => 'TEST-FG',
            'nama_barang' => 'Test Finished Good',
            'jenis_barang' => 'barang_jadi',
            'satuan' => 'pcs',
            'stok_saat_ini' => 0,
            'harga_beli' => 0,
            'harga_jual' => 10000,
        ]);
        
        $rawMaterial = Persediaan::where('id_barang', '!=', $finishedGood->id_barang)->first() ?? Persediaan::create([
            'kode_barang' => 'TEST-RM',
            'nama_barang' => 'Test Raw Material',
            'jenis_barang' => 'bahan_baku',
            'satuan' => 'kg',
            'stok_saat_ini' => 100,
            'harga_beli' => 1000,
            'harga_jual' => 0,
        ]);

        $response = $this->actingAs($this->user)->post(route('manufacturing.bom.store'), [
            'nama_bom' => 'Test BOM',
            'barang_jadi_id' => $finishedGood->id_barang,
            'kuantitas_hasil' => 1,
            'details' => [
                [
                    'material_id' => $rawMaterial->id_barang,
                    'kuantitas' => 2
                ]
            ]
        ]);

        $response->assertRedirect(route('manufacturing.bom.index'));
        
        $this->assertDatabaseHas('bom', [
            'nama_bom' => 'Test BOM',
            'barang_jadi_id' => $finishedGood->id_barang
        ]);
    }

    public function test_agriculture_page_accessible()
    {
        $response = $this->actingAs($this->user)->get(route('agriculture.index'));
        $response->assertStatus(200);
    }

    public function test_can_create_biological_asset()
    {
        $response = $this->actingAs($this->user)->post(route('agriculture.store'), [
            'nama_aset' => 'Sapi Test',
            'jenis' => 'hewan',
            'tanggal_perolehan' => date('Y-m-d'),
            'umur_bulan' => 12,
            'nilai_perolehan' => 15000000,
            'nilai_wajar' => 15000000,
        ]);

        $response->assertRedirect(route('agriculture.index'));
        $this->assertDatabaseHas('aset_biologis', ['nama_aset' => 'Sapi Test']);
    }

    public function test_closing_page_accessible()
    {
        $response = $this->actingAs($this->user)->get(route('accounting.closing.index'));
        $response->assertStatus(200);
    }
}
