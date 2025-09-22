<?php

namespace App\Controllers;
use App\Models\UnitKerjaModel;
use App\Models\MagangModel;
use App\Models\JurusanModel;

class Home extends BaseController
{
    protected $pendaftaranModel;
    protected $userModel;
    protected $instansiModel;
    protected $magangModel;
    protected $jurusanModel;
    protected $unitKerjaModel;

    public function __construct()
    {
        // Inisialisasi model
        $this->magangModel = new MagangModel();
        $this->jurusanModel = new JurusanModel();
        $this->unitKerjaModel = new UnitKerjaModel();

    }

    public function index(): string
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Ambil periode aktif
        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->where('tanggal_tutup >=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->get()
            ->getRow();

        $unitDipilih      = $this->request->getGet('unit_kerja') ?? [];
        $pendidikanDipilih = $this->request->getGet('pendidikan') ?? [];
        $jurusanDipilih   = $this->request->getGet('jurusan') ?? [];

        // Kuota data (pastikan sudah tambahkan kuota_unit_id di SELECT)
        $kuotaData = $this->magangModel->getSisaKuota();

        // Ambil semua jurusan_unit sekaligus
        $allJurusanUnit = $db->table('jurusan_unit ju')
            ->select('ju.kuota_unit_id, j.nama_jurusan')
            ->join('jurusan j', 'ju.jurusan_id = j.jurusan_id')
            ->get()
            ->getResult();

        // Kelompokkan jurusan per kuota_unit_id
        $jurusanMap = [];
        foreach ($allJurusanUnit as $row) {
            $jurusanMap[$row->kuota_unit_id][] = $row->nama_jurusan;
        }

        $filteredData = [];
        $filterByUserJurusan = false;
        $userJurusan = null;

        if (logged_in()) {
            $user = $db->table('users')
                ->where('id', user_id())
                ->get()
                ->getRow();
            if ($user) {
                $userJurusan = $user->jurusan_id;
            }
        }

        if (!empty($kuotaData)) {
            foreach ($kuotaData as $item) {
                $match = true;

                // Filter unit kerja
                if (!empty($unitDipilih) && !in_array($item->unit_kerja, $unitDipilih)) {
                    $match = false;
                }

                // Filter pendidikan
                if (!empty($pendidikanDipilih) && !in_array($item->tingkat_pendidikan, $pendidikanDipilih)) {
                    $match = false;
                }

                // Jurusan list dari map
                $jurusanList = $jurusanMap[$item->kuota_unit_id] ?? [];

                if (!empty($jurusanDipilih)) {
                    if (empty(array_intersect($jurusanDipilih, $jurusanList))) {
                        $match = false;
                    }
                } elseif ($userJurusan) {
                    $userJurusanNama = $this->jurusanModel->find($userJurusan)['nama_jurusan'] ?? '';
                    if (!in_array($userJurusanNama, $jurusanList)) {
                        $match = false;
                    }
                    $filterByUserJurusan = true;
                }

                if ($match) {
                    // Tambahkan jurusan ke item
                    $item->jurusan = implode(', ', $jurusanList) ?: null;
                    $filteredData[] = $item;
                }
            }
        }

        $list_unitKerja = $this->unitKerjaModel->findAll();
        $jurusan        = $this->jurusanModel->findAll();

        $isProfilComplite = $this->isProfilComplite();

        return view('index', [
            'periode'          => $periode,
            'data_unit'        => $filteredData,
            'isProfilComplite' => $isProfilComplite,
            'list_unit_kerja'  => $list_unitKerja,
            'list_jurusan'     => $jurusan,
            'unitDipilih'      => $unitDipilih,
            'pendidikanDipilih'=> $pendidikanDipilih,
            'jurusanDipilih'   => $jurusanDipilih,
            'filterByUserJurusan' => $filterByUserJurusan,
        ]);
    }
    
    public function tentang_kami(): string
    {
        return view('tentang_kami');
    }
    public function login(): string
    {
        return view('auth/login');
    }
    public function register(): string
    {
        return view('auth/register');
    }

    // public function lowongan(): string
    // {
    //     $db = \Config\Database::connect();
    //     $today = date('Y-m-d');

    //     // Ambil periode aktif
    //     $periode = $db->table('periode_magang')
    //         ->where('tanggal_buka <=', $today)
    //         ->where('tanggal_tutup >=', $today)
    //         ->orderBy('tanggal_buka', 'DESC')
    //         ->get()
    //         ->getRow();

    //     $unitDipilih      = $this->request->getGet('unit_kerja') ?? [];
    //     $pendidikanDipilih = $this->request->getGet('pendidikan') ?? [];
    //     $jurusanDipilih   = $this->request->getGet('jurusan') ?? [];

    //     // Kuota data (pastikan sudah tambahkan kuota_unit_id di SELECT)
    //     $kuotaData = $this->magangModel->getSisaKuota();

    //     // Ambil semua jurusan_unit sekaligus
    //     $allJurusanUnit = $db->table('jurusan_unit ju')
    //         ->select('ju.kuota_unit_id, j.nama_jurusan')
    //         ->join('jurusan j', 'ju.jurusan_id = j.jurusan_id')
    //         ->get()
    //         ->getResult();

    //     // Kelompokkan jurusan per kuota_unit_id
    //     $jurusanMap = [];
    //     foreach ($allJurusanUnit as $row) {
    //         $jurusanMap[$row->kuota_unit_id][] = $row->nama_jurusan;
    //     }

    //     $filteredData = [];
    //     $filterByUserJurusan = false;
    //     $userJurusan = null;

    //     if (logged_in()) {
    //         $user = $db->table('users')
    //             ->where('id', user_id())
    //             ->get()
    //             ->getRow();
    //         if ($user) {
    //             $userJurusan = $user->jurusan_id;
    //         }
    //     }

    //     if (!empty($kuotaData)) {
    //         foreach ($kuotaData as $item) {
    //             $match = true;

    //             // Filter unit kerja
    //             if (!empty($unitDipilih) && !in_array($item->unit_kerja, $unitDipilih)) {
    //                 $match = false;
    //             }

    //             // Filter pendidikan
    //             if (!empty($pendidikanDipilih) && !in_array($item->tingkat_pendidikan, $pendidikanDipilih)) {
    //                 $match = false;
    //             }

    //             // Jurusan list dari map
    //             $jurusanList = $jurusanMap[$item->kuota_unit_id] ?? [];

    //             if (!empty($jurusanDipilih)) {
    //                 if (empty(array_intersect($jurusanDipilih, $jurusanList))) {
    //                     $match = false;
    //                 }
    //             } elseif ($userJurusan) {
    //                 $userJurusanNama = $this->jurusanModel->find($userJurusan)['nama_jurusan'] ?? '';
    //                 if (!in_array($userJurusanNama, $jurusanList)) {
    //                     $match = false;
    //                 }
    //                 $filterByUserJurusan = true;
    //             }

    //             if ($match) {
    //                 // Tambahkan jurusan ke item
    //                 $item->jurusan = implode(', ', $jurusanList) ?: null;
    //                 $filteredData[] = $item;
    //             }
    //         }
    //     }

    //     $list_unitKerja = $this->unitKerjaModel->findAll();
    //     $jurusan        = $this->jurusanModel->findAll();

    //     $isProfilComplite = $this->isProfilComplite();

    //     return view('lowongan', [
    //         'periode'          => $periode,
    //         'data_unit'        => $filteredData,
    //         'isProfilComplite' => $isProfilComplite,
    //         'list_unit_kerja'  => $list_unitKerja,
    //         'list_jurusan'     => $jurusan,
    //         'unitDipilih'      => $unitDipilih,
    //         'pendidikanDipilih'=> $pendidikanDipilih,
    //         'jurusanDipilih'   => $jurusanDipilih,
    //         'filterByUserJurusan' => $filterByUserJurusan,
    //     ]);
    // }

    public function lowongan(): string
{
    $db = \Config\Database::connect();
    $today = date('Y-m-d');

    // Ambil periode aktif
    $periode = $db->table('periode_magang')
        ->where('tanggal_buka <=', $today)
        ->where('tanggal_tutup >=', $today)
        ->orderBy('tanggal_buka', 'DESC')
        ->get()
        ->getRow();

    $unitDipilih       = $this->request->getGet('unit_kerja') ?? [];
    $pendidikanDipilih = $this->request->getGet('pendidikan') ?? [];
    $jurusanDipilih    = $this->request->getGet('jurusan') ?? [];

    // Kuota data (pastikan sudah tambahkan kuota_unit_id di SELECT)
    $kuotaData = $this->magangModel->getSisaKuota();

    // Ambil semua jurusan_unit sekaligus
    $allJurusanUnit = $db->table('jurusan_unit ju')
        ->select('ju.kuota_unit_id, j.nama_jurusan')
        ->join('jurusan j', 'ju.jurusan_id = j.jurusan_id')
        ->get()
        ->getResult();

    // Kelompokkan jurusan per kuota_unit_id
    $jurusanMap = [];
    foreach ($allJurusanUnit as $row) {
        $jurusanMap[$row->kuota_unit_id][] = $row->nama_jurusan;
    }

    $filteredData = [];
    $filterByUserJurusan = false;
    $userJurusan = null;
    $userKategoriPendidikan = null;

    if (logged_in()) {
        $user = $db->table('users')
            ->where('id', user_id())
            ->get()
            ->getRow();

        if ($user) {
            $userJurusan = $user->jurusan_id;

            // Kelompokkan pendidikan user
            if ($user->tingkat_pendidikan === 'SMK') {
                $userKategoriPendidikan = ['SMK'];
            } elseif (in_array($user->tingkat_pendidikan, ['D3', 'D4/S1', 'S2'])) {
                $userKategoriPendidikan = ['Perguruan Tinggi'];
            }
        }
    }

    if (!empty($kuotaData)) {
        foreach ($kuotaData as $item) {
            $match = true;

            // Filter unit kerja
            if (!empty($unitDipilih) && !in_array($item->unit_kerja, $unitDipilih)) {
                $match = false;
            }

            // Filter pendidikan
            if (!empty($pendidikanDipilih)) {
                if (!in_array($item->tingkat_pendidikan, $pendidikanDipilih)) {
                    $match = false;
                }
            } elseif ($userKategoriPendidikan) {
                if (!in_array($item->tingkat_pendidikan, $userKategoriPendidikan)) {
                    $match = false;
                }
            }

            // Jurusan list dari map
            $jurusanList = $jurusanMap[$item->kuota_unit_id] ?? [];

            if (!empty($jurusanDipilih)) {
                if (empty(array_intersect($jurusanDipilih, $jurusanList))) {
                    $match = false;
                }
            } elseif ($userJurusan) {
                $userJurusanNama = $this->jurusanModel->find($userJurusan)['nama_jurusan'] ?? '';
                if (!in_array($userJurusanNama, $jurusanList)) {
                    $match = false;
                }
                $filterByUserJurusan = true;
            }

            if ($match) {
                // Tambahkan jurusan ke item
                $item->jurusan = implode(', ', $jurusanList) ?: null;
                $filteredData[] = $item;
            }
        }
    }

    $list_unitKerja = $this->unitKerjaModel->findAll();
    $jurusan        = $this->jurusanModel->findAll();

    $isProfilComplite = $this->isProfilComplite();

    return view('lowongan', [
        'periode'              => $periode,
        'data_unit'            => $filteredData,
        'isProfilComplite'     => $isProfilComplite,
        'list_unit_kerja'      => $list_unitKerja,
        'list_jurusan'         => $jurusan,
        'unitDipilih'          => $unitDipilih,
        'pendidikanDipilih'    => $pendidikanDipilih,
        'jurusanDipilih'       => $jurusanDipilih,
        'filterByUserJurusan'  => $filterByUserJurusan,
        'userKategoriPendidikan' => $userKategoriPendidikan,
    ]);
}




    private function isProfilComplite(): bool
    {
        $userId = user_id();
        $db = \Config\Database::connect();

        $user = $db->table('users')->where('id', $userId)->get()->getRow();

        // Cek kelengkapan field, sesuaikan dengan field pada tabel kamu
        if (!$user) return false;

        $requiredFields = ['fullname', 'nisn_nim', 'email','jenis_kelamin','alamat','no_hp', 'province_id','city_id',
        'tingkat_pendidikan', 'instansi_id','jurusan_id','semester', 
        'surat_permohonan', 'tanggal_surat','no_surat','nama_pimpinan','jabatan','email_instansi']; // Tambahkan sesuai kebutuhan
       if ($user->tingkat_pendidikan !== 'SMK') {
            $requiredFields = array_merge($requiredFields, ['proposal', 'cv','nilai_ipk']);
        }
        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }

        return true;
    }

    private function getIncompleteFields(): array
    {
        $userId = user_id();
        $db = \Config\Database::connect();

        $user = $db->table('users')->where('id', $userId)->get()->getRow();

        if (!$user) {
            return ['user_not_found'];
        }

        $requiredFields = [
            'fullname', 'nisn_nim', 'email', 'jenis_kelamin', 'alamat', 'no_hp',
            'province_id', 'city_id', 'tingkat_pendidikan', 'instansi_id', 'jurusan_id',
            'semester', 'surat_permohonan', 'tanggal_surat', 'no_surat',
            'nama_pimpinan', 'jabatan', 'email_instansi'
        ];

        if ($user->tingkat_pendidikan !== 'SMK') {
            $requiredFields = array_merge($requiredFields, ['proposal', 'cv', 'nilai_ipk']);
        }

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                $missingFields[] = $field;
            }
        }

        return $missingFields;
    }

}
