<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan membuat request ini.
     * Hanya admin/superuser yang terautentikasi yang boleh mendaftarkan tenant baru.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if (!$user) {
            return false;
        }

        return in_array($user->role, ['superuser', 'admin']);
    }

    /**
     * Rule validasi yang diterapkan ke request.
     */
    public function rules(): array
    {
        return [
            'tenant_id' => [
                'required',
                'string',
                'min:3',
                'max:50',      // Batasi panjang agar tidak melanggar limit URL / panjang subdomain

                // Hanya izinkan huruf kecil, angka, dan tanda hubung (-).
                // Mencegah karakter aneh yang bisa merusak config Nginx/Apache atau DNS.
                // Catatan: tanda hubung TIDAK boleh di awal/akhir (RFC 1123).
                'regex:/^[a-z0-9]+([a-z0-9\-]*[a-z0-9])?$/',

                // Pastikan belum dipakai oleh tenant lain
                'unique:tenants,id',

                // BLACKLIST: Jangan biarkan user memakai subdomain penting milik sistem/VPS.
                // Tambahkan entri lain sesuai kebutuhan VPS/hosting kamu.
                'not_in:www,ww,mail,smtp,pop,pop3,imap,ftp,sftp,ssh,ns,ns1,ns2,dns,
                        admin,api,app,portal,dashboard,panel,cp,cpanel,whm,webmail,
                        billing,pay,payment,invoice,shop,store,
                        dev,staging,test,demo,sandbox,beta,alpha,preview,
                        static,assets,cdn,media,images,files,uploads,
                        v1,v2,v3,v4,v5,central,system,server,host,cloud,
                        help,support,docs,status,monitor,health,
                        blog,news,forum,community,
                        root,localhost,local,intranet,internal',
            ],

            'nama_perusahaan' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email:rfc,dns',  // validasi lebih ketat: RFC + pengecekan DNS MX record
                'max:255',
            ],
        ];
    }

    /**
     * Kustomisasi pesan error agar lebih ramah untuk user Indonesia.
     */
    public function messages(): array
    {
        return [
            'tenant_id.required'  => 'ID Tenant (Subdomain) wajib diisi.',
            'tenant_id.min'       => 'ID Tenant minimal :min karakter.',
            'tenant_id.max'       => 'ID Tenant maksimal :max karakter.',
            'tenant_id.regex'     => 'ID Tenant hanya boleh berisi huruf kecil (a-z), angka (0-9), dan tanda hubung (-) di tengah. Tidak boleh diawali atau diakhiri tanda hubung, dan tidak boleh mengandung spasi atau simbol lain.',
            'tenant_id.unique'    => 'Subdomain ":input" sudah digunakan oleh perusahaan lain. Silakan pilih yang berbeda.',
            'tenant_id.not_in'    => 'Subdomain ":input" adalah milik sistem dan tidak bisa digunakan. Silakan pilih nama lain.',

            'nama_perusahaan.required' => 'Nama Perusahaan wajib diisi.',
            'nama_perusahaan.max'      => 'Nama Perusahaan maksimal :max karakter.',

            'email.email' => 'Format email tidak valid atau domain email tidak ditemukan.',
            'email.max'   => 'Email maksimal :max karakter.',
        ];
    }

    /**
     * Label field yang lebih ramah untuk pesan error default Laravel.
     */
    public function attributes(): array
    {
        return [
            'tenant_id'       => 'ID Tenant / Subdomain',
            'nama_perusahaan' => 'Nama Perusahaan',
            'email'           => 'Email',
        ];
    }

    /**
     * Hook: manipulasi data SEBELUM divalidasi.
     *
     * - Paksa tenant_id menjadi huruf kecil dan hapus spasi di awal/akhir.
     *   Ini membantu UX agar user tidak gagal hanya karena typo huruf kapital.
     * - Trim spasi pada field teks lainnya juga.
     */
    protected function prepareForValidation(): void
    {
        $merges = [];

        if ($this->has('tenant_id')) {
            // 1. Lowercase + trim spasi
            // 2. Ganti spasi internal dengan tanda hubung (opsional UX helper)
            // 3. Hilangkan karakter selain a-z, 0-9, dan tanda hubung
            $tenantId = strtolower(trim((string) $this->tenant_id));
            $tenantId = preg_replace('/\s+/', '-', $tenantId);          // spasi → strip
            $tenantId = preg_replace('/[^a-z0-9\-]/', '', $tenantId);   // hapus karakter terlarang
            $tenantId = preg_replace('/-+/', '-', $tenantId);           // strip berulang → satu strip
            $tenantId = trim($tenantId, '-');                            // hapus strip di awal/akhir

            $merges['tenant_id'] = $tenantId;
        }

        if ($this->has('nama_perusahaan')) {
            $merges['nama_perusahaan'] = trim((string) $this->nama_perusahaan);
        }

        if ($this->has('email')) {
            $merges['email'] = trim(strtolower((string) $this->email));
        }

        if (!empty($merges)) {
            $this->merge($merges);
        }
    }
}
