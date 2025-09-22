<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\InstansiModel;
use App\Models\MagangModel;
use App\Models\SoalSafetyModel;
use App\Models\JawabanSafetyModel;
use App\Models\PenilaianModel;
use App\Models\PenelitianModel;

class PenelitianController extends BaseController
{
    protected $userModel;
    protected $instansiModel;
    protected $magangModel;
    protected $soalSafetyModel;
    protected $jawabanModel;
    protected $penilaianModel;
    protected $penelitianModel;
    
    public function __construct()
    {
        // Inisialisasi model
        $this->userModel = new UserModel();
        $this->instansiModel = new InstansiModel();
        $this->magangModel = new MagangModel();
        $this->soalSafetyModel = new SoalSafetyModel();
        $this->jawabanModel = new JawabanSafetyModel();
        $this->penilaianModel = new PenilaianModel();
        $this->penelitianModel = new PenelitianModel();

    }

    public function index()
    {
        $userId = user_id();

        // Ambil data profil pengguna
        $data['user_data'] = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->select('users.*, instansi.nama_instansi, jurusan.nama_jurusan')
            ->where('users.id', $userId)
            ->first();

            // Ambil satu pendaftaran penelitian yang masih berjalan
        $data['pendaftaran'] = $this->penelitianModel
            ->select('penelitian.penelitian_id as penelitian_id, penelitian.*')
            ->where('user_id', $userId)
            ->whereIn('status_akhir', ['pengajuan', 'proses', 'penelitian']) // ganti sesuai status penelitian
            ->orderBy('tanggal_daftar', 'DESC')
            ->first();

        // Ambil semua riwayat penelitian yang sudah selesai/ditolak
        $data['histori'] = $this->penelitianModel
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
            ->select('penelitian.*, unit_kerja.unit_kerja')
            ->where('user_id', $userId)
            ->whereNotIn('penelitian.status_akhir', ['pengajuan', 'proses', 'penelitian'])
            ->orderBy('penelitian.tanggal_daftar', 'DESC')
            ->findAll();

        // Ambil periode aktif
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
       
        return view('user/penelitian', [
            'periode'             => $periode,
            'pendaftaran'         => $data['pendaftaran'],        
            'histori' => $data['histori'],
            'user_data'           => $data['user_data'],
        ]);
    }
}
