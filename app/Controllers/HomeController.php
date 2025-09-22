<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UnitKerjaModel;
use App\Models\MagangModel;
use App\Models\JurusanModel;
use Config\Services;

class HomeController extends BaseController
{
    protected $magangModel;
    protected $jurusanModel;
    protected $unitKerjaModel;

    public function __construct()
    {
        $this->magangModel = new MagangModel();
        $this->jurusanModel = new JurusanModel();
        $this->unitKerjaModel = new UnitKerjaModel();

    }

    public function testemail()
    {
        $email = Services::email();

        $email->setFrom('magang.sp@sig.id', 'Admin SIG');
        $email->setTo('virayukia1234@gmail.com'); // Ganti dengan alamat tujuanmu

        $email->setSubject('Tes Email dari CodeIgniter');
        $email->setMessage('Ini adalah email percobaan yang dikirim dari aplikasi CodeIgniter 4 menggunakan SMTP Office365 + App Password.');

        if ($email->send()) {
            return 'Email berhasil dikirim ✅';
        } else {
            // Jika gagal, tampilkan debugging info
            return $email->printDebugger(['headers']);
        }
    }

    //menampilkan halaman index+filter tingkat pendidikan dan jurusan saat login
    public function index()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->where('tanggal_tutup >=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->get()
            ->getRow();

        $unitDipilih       = $this->request->getGet('unit_kerja') ?? [];
        $pendidikanDipilih = $this->request->getGet('pendidikan') ?? [];
        $jurusanDipilih    = $this->request->getGet('jurusan') ?? [];
        $page              = (int) ($this->request->getGet('page') ?? 1);
        $perPage           = 9;

        $kuotaData = $this->magangModel->getSisaKuota();

        $allJurusanUnit = $db->table('jurusan_unit ju')
            ->select('ju.kuota_unit_id, j.nama_jurusan')
            ->join('jurusan j', 'ju.jurusan_id = j.jurusan_id')
            ->get()
            ->getResult();

        $jurusanMap = [];
        foreach ($allJurusanUnit as $row) {
            $jurusanMap[$row->kuota_unit_id][] = $row->nama_jurusan;
        }

        $filteredData = [];
        $filterByUserJurusan = false;
        $userJurusan = null;
        $userKategoriPendidikan = null;

        if (logged_in()) {
            $user = $db->table('users')->where('id', user_id())->get()->getRow();
            if ($user) {
                $userJurusan = $user->jurusan_id;
                $userKategoriPendidikan = match($user->tingkat_pendidikan) {
                    'SMK' => ['SMK'],
                    'D3', 'D4/S1', 'S2' => ['Perguruan Tinggi'],
                    default => null
                };
            }
        }

        if (!empty($kuotaData)) {
            foreach ($kuotaData as $item) {
                $match = true;

                if (!empty($unitDipilih) && !in_array($item->unit_kerja, $unitDipilih)) $match = false;
                if (!empty($pendidikanDipilih) && !in_array($item->tingkat_pendidikan, $pendidikanDipilih)) $match = false;
                elseif ($userKategoriPendidikan && !in_array($item->tingkat_pendidikan, $userKategoriPendidikan)) $match = false;

                $jurusanList = $jurusanMap[$item->kuota_unit_id] ?? [];
                if (!empty($jurusanDipilih) && empty(array_intersect($jurusanDipilih, $jurusanList))) $match = false;
                elseif ($userJurusan) {
                    $userJurusanNama = $this->jurusanModel->find($userJurusan)['nama_jurusan'] ?? '';
                    if (!empty($jurusanList) && !in_array($userJurusanNama, $jurusanList)) $match = false;
                    $filterByUserJurusan = true;
                }

                if ($match && $item->sisa_kuota > 0) {
                    $item->jurusan = implode(', ', $jurusanList) ?: null;
                    $filteredData[] = $item;
                }
            }
        }

        $total = count($filteredData);
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $pagedData = array_slice($filteredData, $offset, $perPage);
       
        return view('index', [
            'periode'                => $periode,
            'data_unit'              => $pagedData,
            'currentPage'            => $page,
            'totalPages'             => $totalPages,
            'isProfilComplite'       => $this->isProfilComplite(),
            'list_unit_kerja'        => $this->unitKerjaModel->findAll(),
            'list_jurusan'           => $this->jurusanModel->findAll(),
            'unitDipilih'            => $unitDipilih,
            'pendidikanDipilih'      => $pendidikanDipilih,
            'jurusanDipilih'         => $jurusanDipilih,
            'filterByUserJurusan'    => $filterByUserJurusan,
            'userKategoriPendidikan' => $userKategoriPendidikan,
        ]);
    }
    //menampilkan halaman lowongan+filter tingkat pendidikan dan jurusan saat login
    public function lowongan()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->where('tanggal_tutup >=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->get()
            ->getRow();

        $unitDipilih       = $this->request->getGet('unit_kerja') ?? [];
        $pendidikanDipilih = $this->request->getGet('pendidikan') ?? [];
        $jurusanDipilih    = $this->request->getGet('jurusan') ?? [];
        $page              = (int) ($this->request->getGet('page') ?? 1);
        $perPage           = 9;

        $kuotaData = $this->magangModel->getSisaKuota();

        $allJurusanUnit = $db->table('jurusan_unit ju')
            ->select('ju.kuota_unit_id, j.nama_jurusan')
            ->join('jurusan j', 'ju.jurusan_id = j.jurusan_id')
            ->get()
            ->getResult();

        $jurusanMap = [];
        foreach ($allJurusanUnit as $row) {
            $jurusanMap[$row->kuota_unit_id][] = $row->nama_jurusan;
        }

        $filteredData = [];
        $filterByUserJurusan = false;
        $userJurusan = null;
        $userKategoriPendidikan = null;

        if (logged_in()) {
            $user = $db->table('users')->where('id', user_id())->get()->getRow();
            if ($user) {
                $userJurusan = $user->jurusan_id;
                $userKategoriPendidikan = match($user->tingkat_pendidikan) {
                    'SMK' => ['SMK'],
                    'D3', 'D4/S1', 'S2' => ['Perguruan Tinggi'],
                    default => null
                };
            }
        }

        if (!empty($kuotaData)) {
            foreach ($kuotaData as $item) {
                $match = true;

                if (!empty($unitDipilih) && !in_array($item->unit_kerja, $unitDipilih)) $match = false;
                if (!empty($pendidikanDipilih) && !in_array($item->tingkat_pendidikan, $pendidikanDipilih)) $match = false;
                elseif ($userKategoriPendidikan && !in_array($item->tingkat_pendidikan, $userKategoriPendidikan)) $match = false;

                $jurusanList = $jurusanMap[$item->kuota_unit_id] ?? [];
                if (!empty($jurusanDipilih) && empty(array_intersect($jurusanDipilih, $jurusanList))) $match = false;
                elseif ($userJurusan) {
                    $userJurusanNama = $this->jurusanModel->find($userJurusan)['nama_jurusan'] ?? '';
                    if (!empty($jurusanList) && !in_array($userJurusanNama, $jurusanList)) $match = false;
                    $filterByUserJurusan = true;
                }

                if ($match && $item->sisa_kuota > 0) {
                    $item->jurusan = implode(', ', $jurusanList) ?: null;
                    $filteredData[] = $item;
                }
            }
        }

        $total = count($filteredData);
        $totalPages = ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        $pagedData = array_slice($filteredData, $offset, $perPage);
       
        return view('lowongan', [
            'periode'                => $periode,
            'data_unit'              => $pagedData,
            'currentPage'            => $page,
            'totalPages'             => $totalPages,
            'isProfilComplite'       => $this->isProfilComplite(),
            'list_unit_kerja'        => $this->unitKerjaModel->findAll(),
            'list_jurusan'           => $this->jurusanModel->findAll(),
            'unitDipilih'            => $unitDipilih,
            'pendidikanDipilih'      => $pendidikanDipilih,
            'jurusanDipilih'         => $jurusanDipilih,
            'filterByUserJurusan'    => $filterByUserJurusan,
            'userKategoriPendidikan' => $userKategoriPendidikan,
        ]);
    }

    //menampilkan halaman tentang-kami
    public function tentang_kami(): string
    {
        return view('tentang_kami');
    }

    //untuk cek apakah profilnya sudah complite dan siap mendaftar
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

    // Untuk cek field yang incomplete
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

    public function getImages()
    {
        $folderPath = FCPATH . 'assets/img/masthead/';
        $urlBase = base_url('assets/img/masthead/');
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        $imageFiles = [];

        foreach (scandir($folderPath) as $file) {
            if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowed)) {
                $imageFiles[] = $urlBase . $file;
            }
        }

        return $this->response->setJSON($imageFiles);
    }


}
