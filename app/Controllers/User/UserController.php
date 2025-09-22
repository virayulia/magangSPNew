<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\InstansiModel;
use App\Models\MagangModel;
use App\Models\JurusanModel;
use App\Models\ProvinceModel;
use App\Models\RegenciesModel;


class UserController extends BaseController
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
            ->select('users.id, users.fullname, users.email, users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
            users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
            users.instagram, users.instagram_followers, users.tiktok, users.tiktok_followers,
            users.tingkat_pendidikan, users.instansi_id, users.jurusan_id, users.semester, 
            users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
            users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
            users.buktibpjs_tk, users.ktp_kk, users.status,
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

        $data['pendaftaran'] = $this->magangModel
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->select('magang.magang_id as magang_id, magang.*, unit_kerja.*')
            ->where('user_id', $userId)
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang'])
            ->orderBy('tanggal_daftar', 'DESC')
            ->first();

        // Ambil periode aktif
        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

        return view('user/profile', [
            'pendaftaran'         => $data['pendaftaran'],
            'user_data'           => $data['user_data'],
            'periode'             => $periode,
        ]);
    }

    public function dataPribadi()
    {
        $userId = user()?->id; ; 
        $data['user_data'] = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id','left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id','left')
            ->select('users.fullname,users.email, users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
            users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
            users.instagram, users.instagram_followers, users.tiktok, users.tiktok_followers,
            users.tingkat_pendidikan, users.instansi_id, users.jurusan_id, users.semester, 
            users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
            users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
            users.buktibpjs_tk, users.ktp_kk, users.status, instansi.nama_instansi, jurusan.nama_jurusan')
            ->where('users.id', $userId)
            ->first();

        $data['pendaftaran'] = $this->magangModel
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->select('magang.magang_id as magang_id, magang.*, unit_kerja.*')
            ->where('user_id', $userId)
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang'])
            ->orderBy('tanggal_daftar', 'DESC')
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

    public function dataAkademik()
    {
        $userId = user()?->id; ; 
        $data['user_data'] = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id','left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id','left')
            ->select('users.fullname,users.email, users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
            users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
            users.tingkat_pendidikan, users.instansi_id, users.jurusan_id,  users.semester, 
            users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
            users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
            users.buktibpjs_tk, users.ktp_kk, users.status, instansi.nama_instansi, jurusan.nama_jurusan')
            ->where('users.id', $userId)
            ->first();
        
        $data['instansi'] =$this->instansiModel->findAll();

        $data['jurusan'] =$this->jurusanModel->findAll();
        $data['pendaftaran'] = $this->magangModel
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->select('magang.magang_id as magang_id, magang.*, unit_kerja.*')
            ->where('user_id', $userId)
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang'])
            ->orderBy('tanggal_daftar', 'DESC')
            ->first();


        return view('user/akademik-edit', $data);
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
            'email'         => $this->request->getPost('email'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'no_hp'         => $this->request->getPost('no_hp'),
            'alamat'        => $this->request->getPost('alamat'),
            'province_id'   => $this->request->getPost('state_id'),
            'city_id'       => $this->request->getPost('city_id'),
            'domisili'      => $this->request->getPost('domisili'),
            'provinceDom_id'=> $this->request->getPost('stateDom_id'),
            'cityDom_id'    => $this->request->getPost('cityDom_id'),
            'instagram'     => $this->request->getPost('instagram'),
            'instagram_followers'  => $this->request->getPost('instagram_followers'),
            'tiktok'        => $this->request->getPost('tiktok'),
            'tiktok_followers'    => $this->request->getPost('tiktok_followers'),
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
            $foto->move(ROOTPATH . 'public/uploads/user-image', $newName);

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

        $pendidikan = $this->request->getPost('jenjang_pendidikan');
        $isSMK = strtoupper(trim($pendidikan)) === 'SMK';

        $nilaiIpk = $this->request->getPost('ipk');

        $data = [
            'tingkat_pendidikan' => $pendidikan,
            'instansi_id'        => $this->request->getPost('instansi'),
            'jurusan_id'         => $this->request->getPost('jurusan'),
            'semester'           => $this->request->getPost('semester'),
            'nilai_ipk'          => $isSMK ? null : $nilaiIpk,
        ];

        // Validasi IPK hanya jika bukan SMK dan nilai tidak kosong
        if (!$isSMK && $nilaiIpk !== '') {
            if (!is_numeric($nilaiIpk) || $nilaiIpk < 0 || $nilaiIpk > 4) {
                return redirect()->back()
                    ->with('error', 'Nilai/IPK harus berupa angka antara 0 hingga 4.')
                    ->withInput();
            }
        }

        if (!$this->userModel->validate($data)) {
            return redirect()->back()
                ->with('error', implode('<br>', $this->userModel->errors()))
                ->withInput();
        }

        $result = $this->userModel->update($userId, $data);

        if (!$result) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data.');
        }

        return redirect()->to('/profile')
            ->with('success', 'Data berhasil diperbarui.');
    }

}
