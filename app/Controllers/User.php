<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\InstansiModel;
use App\Models\MagangModel;
use App\Models\JurusanModel;
use App\Models\ProvinceModel;
use App\Models\RegenciesModel;


class User extends BaseController
{
    protected $pendaftaranModel;
    protected $userModel;
    protected $instansiModel;
    protected $magangModel;
    protected $jurusanModel;
    protected $countriesModel;
    protected $provincesModel;
    protected $regenciesModel;

    public function __construct()
    {
        // Inisialisasi model
        $this->userModel = new UserModel();
        $this->instansiModel = new InstansiModel();
        $this->magangModel = new MagangModel();
        $this->jurusanModel = new JurusanModel();
        $this->provincesModel = new ProvinceModel();
        $this->regenciesModel = new RegenciesModel();

    }
    public function profil()
    {
        $userId = user_id();
        $data['user_data'] = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id','left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id','left')
            ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
            ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
            ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
            ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
            ->select('users.*, 
                        instansi.nama_instansi, 
                        jurusan.nama_jurusan, 
                        province_ktp.province AS provinsi_ktp,
                        province_dom.province AS provinsi_domisili,
                        city_ktp.regency AS kota_ktp, 
                        city_ktp.type AS tipe_kota_ktp,
                        city_dom.regency AS kota_domisili,
                        city_dom.type AS tipe_kota_domisili')
            ->where('users.id', $userId)
            ->first();
        return view('user/profile', $data);
    }

    public function dataPribadi()
    {
        $userId = user()?->id; ; 
        $data['user_data'] = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id','left')
            ->select('users.*, instansi.nama_instansi')
            ->where('users.id', $userId)
            ->first();
        
        $data['instansi'] =$this->instansiModel->findAll();

        $data['jurusan'] =$this->jurusanModel->findAll();

        $data['listState'] =$this->provincesModel->select('id,province')
                                                ->orderBy('id')
                                                ->findAll();
        $data['listCity'] =[];
        if(!empty($data['user_data']->province_id)){
        $data['listCity'] = $this->regenciesModel->where('province_id', $data['user_data']->province_id)->findAll();
        }
        $data['listCityDom'] = [];

        if (!empty($data['user_data']->provinceDom_id)) {
            $data['listCityDom'] = $this->regenciesModel->where('province_id', $data['user_data']->provinceDom_id)->findAll();
        }
        return view('user/profile-edit', $data);
    }

    public function getCities()
    {
        $state_id = $this->request->getVar('state_id');
        $searchTerm = $this->request->getVar('searchTerm');

        $query = $this->regenciesModel->select('id, regency')
                                ->where('province_id', $state_id);

        if ($searchTerm) {
            $query->like('regency', $searchTerm);
        }

        $listCities = $query->orderBy('id')->findAll();

        $data = [];
        foreach ($listCities as $value) {
            $data[] = [
                'id' => $value['id'],
                'text' => $value['regency'],
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function getCitiesDom()
    {
        $stateDom_id = $this->request->getVar('stateDom_id');
        $searchTerm = $this->request->getVar('searchTerm');

        $query = $this->regenciesModel->select('id, regency')
                                ->where('province_id', $stateDom_id);

        if ($searchTerm) {
            $query->like('regency', $searchTerm);
        }

        $listCities = $query->orderBy('id')->findAll();

        $data = [];
        foreach ($listCities as $value) {
            $data[] = [
                'id' => $value['id'],
                'text' => $value['regency'],
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function saveDataPribadi()
    {
        $userId = user_id();
        $this->userModel->setValidationRule('email', "required|valid_email|is_unique[users.email,id,{$userId}]");

        $data = [
            'fullname'      => $this->request->getPost('fullname'),
            'nisn_nim'      => $this->request->getPost('nisn_nim'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'email'         => $this->request->getPost('email'),
            'no_hp'         => $this->request->getPost('no_hp'),
            'alamat'        => $this->request->getPost('alamat'),
            'province_id'   => $this->request->getPost('state_id'),
            'city_id'       => $this->request->getPost('city_id'),
            'domisili'      => $this->request->getPost('domisili'),
            'provinceDom_id'=> $this->request->getPost('stateDom_id'),
            'cityDom_id'    => $this->request->getPost('cityDom_id'),
        ];

        // Tangani upload foto
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Validasi ekstensi dan ukuran
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($foto->getMimeType(), $allowedTypes)) {
                return redirect()->back()->with('error', 'Format gambar tidak valid. Hanya JPG, JPEG, PNG.')->withInput();
            }
            if ($foto->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran gambar maksimal 2MB.')->withInput();
            }

            // Rename nama file
            $fullname = strtolower(preg_replace('/\s+/', '', $this->request->getPost('fullname')));
            $newName = $fullname . '-profile.' . $foto->getExtension();

            // Pindahkan file ke folder uploads/profile/
            $foto->move(ROOTPATH . 'public/uploads/profile', $newName);

            // Masukkan nama file ke dalam array data
            $data['user_image'] = $newName;
        }

        if (!$this->userModel->validate($data)) {
            return redirect()->back()->with('error', implode('<br>', $this->userModel->errors()))->withInput();
        }

        
        $result = $this->userModel->update($userId, $data);

        if (!$result) {
            return redirect()->back()->with('error', 'Gagal memperbarui data.');
        }

        return redirect()->to('/profile')->with('success', 'Data berhasil diperbarui.');
    }

    public function saveDataAkademik()
    {
        $userId = user_id();

        $pendidikan = $this->request->getPost('pendidikan');
        $isSMA = $pendidikan === 'SMA/SMK';

        $data = [
            'pendidikan'    => $pendidikan,
            'instansi_id'   => $this->request->getPost('instansi'),
            'jurusan'       => $this->request->getPost('jurusan'),
            'nilai_ipk'     => $this->request->getPost('nilai_ipk'),
        ];

        // Jika bukan SMA, tambahkan semester
        if (!$isSMA) {
            $data['semester'] = $this->request->getPost('semester');
        } else {
            $data['semester'] = null;
        }

        // Validasi manual tambahan jika dibutuhkan
        if (!is_numeric($data['nilai_ipk']) || $data['nilai_ipk'] < 0) {
            return redirect()->back()->with('error', 'Nilai/IPK harus berupa angka positif.')->withInput();
        }

        if (!$this->userModel->validate($data)) {
            return redirect()->back()->with('error', implode('<br>', $this->userModel->errors()))->withInput();
        }

        $result = $this->userModel->update($userId, $data);

        if (!$result) {
            return redirect()->back()->with('error', 'Gagal memperbarui data.');
        }

        return redirect()->to('/profile')->with('success', 'Data berhasil diperbarui.');
    }

    public function statusLamaran()
    {
        $userId = user_id(); // Ambil ID pengguna dari session

        // Ambil data profil pengguna
         $data['user_data'] = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id','left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id','left')
            ->select('users.*, instansi.nama_instansi, jurusan.nama_jurusan')
            ->where('users.id', $userId)
            ->first();

        // Ambil data pendaftaran terkait pengguna
        $data['pendaftaran'] = $this->magangModel->where('user_id', $userId)
                                                ->join('unit_kerja','unit_kerja.unit_id = magang.unit_id')
                                                ->select('magang.magang_id as magang_id, magang.*, unit_kerja.*')
                                                ->first();

        // Muat tampilan profil
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        //  dd($data);
        // Ambil periode aktif
        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today) // sudah dibuka
            ->orderBy('tanggal_buka', 'DESC')  // paling baru
            ->limit(1)
            ->get()
            ->getRow();

            return view('user/status-lamaran', [
            'periode' => $periode,
            'pendaftaran' => $data['pendaftaran'],
            'user_data' => $data['user_data'], 
            
        ]);
    }

    public function pelaksanaan()
    {
        $userId = user_id();

        // Ambil data profil
        $userData = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->select('users.*, instansi.nama_instansi, jurusan.nama_jurusan')
            ->where('users.id', $userId)
            ->first();

        // Ambil data pendaftaran user (pastikan sesuai nama tabel kamu)
        $pendaftaran = $this->magangModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->orderBy('tanggal_daftar', 'desc')
            ->first();

        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/pelaksanaan', [
            'user_data' => $userData,
            'pendaftaran' => $pendaftaran
        ]);
    }

}
