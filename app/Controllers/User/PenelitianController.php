<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;
use App\Models\InstansiModel;
use App\Models\MagangModel;
use App\Models\SoalSafetyModel;
use App\Models\JawabanSafetyModel;
use App\Models\DetailJawabanSafetyModel;
use App\Models\PenilaianModel;
use App\Models\PenelitianModel;
use App\Models\PenelitianKeywordModel;
use App\Models\KeywordModel;
use App\Models\FeedbackPenelitianModel;
use App\Models\SuratKeteranganModel;
use App\Models\UnitUserModel;
use Endroid\QrCode\Builder\Builder as QrBuilder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

class PenelitianController extends BaseController
{
    protected $userModel;
    protected $instansiModel;
    protected $soalSafetyModel;
    protected $jawabanModel;
    protected $penilaianModel;
    protected $penelitianModel;
    protected $penelitianKeywordModel;
    protected $keywordModel;
    protected $detailJawabanModel;
    protected $feedbackPenelitiaModel;
    protected $suratKeteranganModel;
    protected $magangModel;
    protected $unitUserModel;
    
    public function __construct()
    {
        // Inisialisasi model
        $this->userModel = new UserModel();
        $this->instansiModel = new InstansiModel();
        $this->soalSafetyModel = new SoalSafetyModel();
        $this->jawabanModel = new JawabanSafetyModel();
        $this->penilaianModel = new PenilaianModel();
        $this->penelitianModel = new PenelitianModel();
        $this->penelitianKeywordModel = new PenelitianKeywordModel();
        $this->keywordModel = new KeywordModel();
        $this->detailJawabanModel = new DetailJawabanSafetyModel();
        $this->feedbackPenelitiaModel = new FeedbackPenelitianModel();
        $this->suratKeteranganModel = new SuratKeteranganModel();
        $this->magangModel = new MagangModel();
        $this->unitUserModel = new UnitUserModel();

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
        // $db = \Config\Database::connect();
        // $today = date('Y-m-d');

        // $periode = $db->table('periode_magang')
        //     ->where('tanggal_buka <=', $today)
        //     ->orderBy('tanggal_buka', 'DESC')
        //     ->limit(1)
        //     ->get()
        //     ->getRow();
       
        return view('user/penelitian', [
            // 'periode'             => $periode,
            'pendaftaran'         => $data['pendaftaran'],        
            'histori' => $data['histori'],
            'user_data'           => $data['user_data'],
        ]);
    }

    public function daftar()
    {
        if (!logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'judul'            => 'required|min_length[5]',
            'rencana_masuk'    => 'required|valid_date',
            'durasi'             => 'permit_empty|string',
            'deskripsi'        => 'permit_empty|string',
            'dosen_pembimbing' => 'permit_empty|string',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $validation->listErrors());
        }

        // 🔍 CEK MAGANG YANG MASIH AKTIF
        $aktifMagang = $this->magangModel
            ->where('user_id', user_id())
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang'])
            ->first();

        if ($aktifMagang) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda masih memiliki pendaftaran magang yang belum selesai. Harap selesaikan magang anda sebelum mendaftar penelitian.');
        }


        $db = \Config\Database::connect();
        $db->transStart();

        $dataPenelitian = [
            'user_id'          => user_id(),
            'judul_penelitian' => $this->request->getPost('judul'),
            'rencana_masuk'    => $this->request->getPost('rencana_masuk'),
            'durasi'           => $this->request->getPost('durasi'),
            'deskripsi'        => $this->request->getPost('deskripsi'),
            'dosen_pembimbing' => $this->request->getPost('dosen_pembimbing'),
            'tanggal_daftar'   => date('Y-m-d H:i:s'),
            'status_akhir'     => 'pendaftaran',
        ];

        $this->penelitianModel->insert($dataPenelitian);
        $penelitianId = $this->penelitianModel->getInsertID();

        $keyword_ids = $this->request->getPost('keyword_ids');
        if (!empty($keyword_ids)) {
        foreach ($keyword_ids as $keyword) {
                if (is_numeric($keyword)) {
                    $keywordId = $keyword;
                } else {
                    $this->keywordModel->insert(['keyword_nama' => $keyword,
                                                 'status'    => 'waiting',
                                                 'is_suggested' => 1,
                                                 'created_at' => date('Y-m-d H:i:s')]);
                    $keywordId = $this->keywordModel->getInsertID();
                }

                $this->penelitianKeywordModel->insert([
                    'penelitian_id' => $penelitianId,
                    'keyword_id'    => $keywordId,
                ]);
            }
        }

         $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan data penelitian.');
        }

        return redirect()->to('/')->with('success', 'Pendaftaran penelitian berhasil dikirim. Silakan menunggu konfirmasi.');
    }

    public function statusPenelitian()
    {
        $userId = user_id();

        // Ambil data profil pengguna
        $data['user_data'] = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->select('users.fullname, users.email,users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
            users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
            users.tingkat_pendidikan, users.instansi_id, users.jurusan_id, users.semester, 
            users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
            users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
            users.buktibpjs_tk, users.ktp_kk, users.status, instansi.nama_instansi, jurusan.nama_jurusan')
            ->where('users.id', $userId)
            ->first();

        // Ambil satu pendaftaran yang masih berjalan
        $data['pendaftaran'] = $this->penelitianModel
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
            ->select('penelitian.penelitian_id as penelitian_id, penelitian.*, unit_kerja.*')
            ->where('user_id', $userId)
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'penelitian','lulus'])
            ->orderBy('tanggal_daftar', 'DESC')
            ->first();

        // Ambil semua riwayat pendaftaran (untuk ditampilkan sebagai histori)
        $data['histori'] = $this->penelitianModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id')
            ->select('penelitian.*, unit_kerja.unit_kerja')
            ->whereNotIn('penelitian.status_akhir', ['pendaftaran', 'proses', 'penelitian'])
            ->orderBy('penelitian.tanggal_daftar', 'DESC')
            ->findAll();

        return view('user/status-penelitian', [
            'pendaftaran'         => $data['pendaftaran'],        
            'histori' => $data['histori'],
            'user_data'           => $data['user_data'],
        ]);
    }

    public function konfirmasi()
    {
        // Ambil data pendaftaran berdasarkan id
        $request = service('request');
        $id = $request->getPost('penelitian_id');
        $pendaftaran = $this->penelitianModel->find($id);

        // Cek jika data pendaftaran ditemukan
        if (!$pendaftaran) {
            // Jika tidak ditemukan, tampilkan error atau redirect
            return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        // Update status konfirmasi dan tanggal konfirmasi
        $data = [
            'status_konfirmasi' => 'Y',  // Status konfirmasi di-set menjadi 1
            'tanggal_konfirmasi' => date('Y-m-d H:i:s'),  // Tanggal konfirmasi adalah hari ini\
        ];

        // Update data pendaftaran
        if ($this->penelitianModel->update($id, $data)) {
            // Jika berhasil, redirect dengan pesan sukses
            return redirect()->to('/status-penelitian')->with('success', 'Konfirmasi pendaftaran berhasil! Silakan menunggu validasi dari admin sebelum masuk ke tahap berikutnya.');
        } else {
            // Jika gagal, tampilkan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengkonfirmasi pendaftaran.');
        }
 
    }

    public function pelaksanaan()
    {
        $userId = user_id();

        // Ambil data profil
        $userData = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->select('users.fullname, users.email,users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
            users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
            users.tingkat_pendidikan, users.instansi_id, users.jurusan_id, users.semester, 
            users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
            users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
            users.buktibpjs_tk, users.ktp_kk, users.status, instansi.nama_instansi, jurusan.nama_jurusan')
            ->where('users.id', $userId)
            ->first();

        // Ambil data pendaftaran user
        $pendaftaran = $this->penelitianModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id')
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'penelitian', 'lulus'])
            ->orderBy('tanggal_daftar', 'desc')
            ->first();

           

        //Ambil riwayat safety
        $riwayatSafety = [];
        if ($pendaftaran) {
            $riwayatSafety = $this->jawabanModel
                ->join('penelitian', 'penelitian.penelitian_id = jawaban_safety.relasi_id')
                ->where('penelitian.penelitian_id', $pendaftaran['penelitian_id'])
                ->where('jawaban_safety.tipe', 'penelitian')
                ->orderBy('tanggal_ujian', 'desc')
                ->findAll();
        }
        
        // Ambil periode aktif
        // $db = \Config\Database::connect();
        // $today = date('Y-m-d');

        // $periode = $db->table('periode_magang')
        //     ->where('tanggal_buka <=', $today)
        //     ->orderBy('tanggal_buka', 'DESC')
        //     ->limit(1)
        //     ->get()
        //     ->getRow();
       

        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/pelaksanaan_penelitian', [
            // 'periode'   => $periode,
            'user_data' => $userData,
            'pendaftaran' => $pendaftaran,
            'riwayat_safety' => $riwayatSafety
        ]);
    }

    public function cetakTandaPengenal($id)
    {

        $penelitian = $this->penelitianModel->join('unit_kerja','unit_kerja.unit_id = penelitian.unit_id')
                                    ->find($id);

        if (!$penelitian) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data penelitian tidak ditemukan.');
        }

        // Ambil data user berdasarkan user_id dari tabel magang
        $user = $this->userModel->join('instansi', 'instansi.instansi_id = users.instansi_id')
                                ->where('users.id', $penelitian['user_id'])
                                ->select('users.fullname, users.email,users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
            users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
            users.tingkat_pendidikan, users.instansi_id, users.jurusan_id,  users.semester, 
            users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
            users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
            users.buktibpjs_tk, users.ktp_kk, users.status, instansi.nama_instansi as nama_instansi') // jika kamu butuh nama instansi
                                ->first();

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data pengguna tidak ditemukan.');
        }
        return view('user/template_tanda_pengenal', [
            'data' => $penelitian,
            'tipe' => 'penelitian',
            'user' => $user,
        ]);
    }


    public function suratPernyataan()
    {
        $userId = user_id();

        // Ambil data profil
        $userData = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->select('users.fullname,users.email, users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
            users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
            users.tingkat_pendidikan, users.instansi_id, users.jurusan_id, users.semester, 
            users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
            users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
            users.buktibpjs_tk, users.ktp_kk, users.status, instansi.nama_instansi, jurusan.nama_jurusan')
            ->where('users.id', $userId)
            ->first();

        // Ambil data pendaftaran user (pastikan sesuai nama tabel kamu)
        $pendaftaran = $this->penelitianModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id')
            ->orderBy('tanggal_daftar', 'desc')
            ->first();

        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/surat_pernyataan', [
            'tipe'      => 'penelitian',
            'user_data' => $userData,
            'pendaftaran' => $pendaftaran
        ]);
    }

    public function setujuiPernyataan()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('penelitian');

        $userId = user()->id; // Pastikan sesuai field user kamu
        $tanggal = date('Y-m-d');

        // Update data pendaftaran
        $builder->where('user_id', $userId)
            ->update([
                'tanggal_setujui_pernyataan' => $tanggal
            ]);

        return redirect()->to(base_url('pelaksanaan-penelitian'))->with('success', 'Surat pernyataan berhasil disetujui.');
    }

    public function safetyTes()
    {
        $soal = $this->soalSafetyModel->findAll();

        return view('user/tes-safety',
                    ['soal' => $soal,
                    'tipe'  => 'penelitian']);
    }

    public function submitTes()
    {
        $userId = user_id(); 
        $jawabanUser = $this->request->getPost('jawaban');

        $penelitian = $this->penelitianModel
            ->where('user_id', $userId)
            ->where('status_akhir', 'penelitian')
            ->first();

        if (!$penelitian) {
            return redirect()->to('/pelaksanaan-penelitian')->with('error', '❌ Data penelitian tidak ditemukan.');
        }

        $penelitianId = $penelitian['penelitian_id'];
        $tanggalHariIni = date('Y-m-d');

        $percobaanHariIni = $this->jawabanModel
            ->where('relasi_id', $penelitianId)
            ->where('tanggal_ujian', $tanggalHariIni)
            ->where('tipe','penelitian')
            ->countAllResults();

        if ($percobaanHariIni >= 3) {
            return redirect()->to('/pelaksanaan-penelitian')
                ->with('error', '❌ Anda sudah 3 kali tes hari ini. Silakan coba lagi besok.');
        }

        $soalSemua = $this->soalSafetyModel->findAll();
        $nilaiPerSoal = 100 / count($soalSemua);
        $skor = 0;

        $db = \Config\Database::connect();
        $db->transStart();

        $this->jawabanModel->insert([
            'tipe'          => 'penelitian',
            'relasi_id'     => $penelitianId,
            'nilai'         => 0,
            'percobaan_ke'  => $percobaanHariIni + 1, 
            'tanggal_ujian' => $tanggalHariIni,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $jawabanId = $this->jawabanModel->getInsertID(); 
        
        foreach ($soalSemua as $soal) {
            $soalId = $soal['soal_id'];
            $jawabanBenar = strtolower(trim($soal['jawaban_benar']));
            $jawaban = strtolower(trim($jawabanUser[$soalId] ?? ''));

            $benar = $jawaban === $jawabanBenar;

            if ($benar) {
                $skor += $nilaiPerSoal;
            }

            $this->detailJawabanModel->insert([
                'jawaban_safety_id' => $jawabanId,
                'soal_safety_id'    => $soalId,
                'jawaban_user'      => $jawaban,
                'benar'             => $benar ? 1 : 0,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        }
        
        $skor = round($skor);
        $this->jawabanModel->update($jawabanId, ['nilai' => $skor]);

        $db->transComplete();

        $status = $skor >= 70 ? 'lulus' : 'gagal';
        return redirect()->to('/pelaksanaan-penelitian')->with('success', "Tes selesai. Skor Anda: $skor ($status)");
    }

    public function unggahIndex()
    {
        $userId = user_id();

        // Ambil data profil
        $userData = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->select('users.fullname, users.email,users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
            users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
            users.tingkat_pendidikan, users.instansi_id, users.jurusan_id, users.semester, 
            users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
            users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
            users.buktibpjs_tk, users.ktp_kk, users.status, instansi.nama_instansi, jurusan.nama_jurusan')
            ->where('users.id', $userId)
            ->first();

        // Ambil data pendaftaran user (pastikan sesuai nama tabel kamu)
        $pendaftaran = $this->penelitianModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id')
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'penelitian', 'lulus'])
            ->orderBy('tanggal_daftar', 'desc')
            ->first();
       
        return view('user/unggah-form-penelitian', [
            'user_data' => $userData,
            'pendaftaran' => $pendaftaran,
        ]);
    }

    public function uploadFormPenelitian($id)
    {
        $formData = $this->request->getFile('formData');
        $absensi = $this->request->getFile('absensi');

        $penelitian  = $this->penelitianModel->find($id);

        if (!$penelitian) {
            return redirect()->back()->with('error', 'Data penelitian tidak ditemukan.');
        }

        $user = $this->userModel->find($penelitian['user_id']);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $result = [];
        // === Upload Formulir ===
        if ($formData && $formData->isValid()) {
            if ($formData->getSize() > 2 * 1024 * 1024) { // 2 MB
                return redirect()->back()->with('error', 'Ukuran file absensi terlalu besar. Maksimal 2 MB.');
            }

            $formDataName = uploadBerkasUser($formData, $user->fullname ?? 'user', 'form-penelitian', $penelitian['formulir_penelitian']);
            if ($formDataName) {
                $this->penelitianModel->update($id, ['formulir_penelitian' => $formDataName]);
                $result[] = 'Formulir penelitian berhasil diupload.';
            }
        }

        // === Upload Absensi ===
        if ($absensi && $absensi->isValid()) {
            if ($absensi->getSize() > 2 * 1024 * 1024) { // 2 MB
                return redirect()->back()->with('error', 'Ukuran file absensi terlalu besar. Maksimal 2 MB.');
            }

            $absensiName = uploadBerkasUser($absensi, $user->fullname ?? 'user', 'absensi-penelitian', $penelitian['absensi']);
            if ($absensiName) {
                $this->penelitianModel->update($id, ['absensi' => $absensiName]);
                $result[] = 'Absensi berhasil diupload.';
            }
        }

        if (!empty($result)) {
            return redirect()->back()->with('success', implode(' ', $result));
        }

        return redirect()->back()->with('error', 'Tidak ada file yang berhasil diupload.');
    }


    public function suratKeterangan()
    {
        $userId = user_id();

        // Ambil data profil
        $userData = $this->userModel
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->select('users.fullname,users.email, users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
            users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
            users.tingkat_pendidikan, users.instansi_id, users.jurusan_id,  users.semester, 
            users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
            users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
            users.buktibpjs_tk, users.ktp_kk, users.status, instansi.nama_instansi, jurusan.nama_jurusan')
            ->where('users.id', $userId)
            ->first();

        $pendaftaran = $this->penelitianModel
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id')
            ->select('penelitian.penelitian_id as penelitian_id, penelitian.*, unit_kerja.*')
            ->where('user_id', $userId)
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'penelitian', 'lulus'])
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

        // 🔹 Cek apakah sudah isi feedback
        $feedback = null;
        if ($pendaftaran) {
            $feedback = $db->table('feedback_penelitian')
                ->where('penelitian_id', $pendaftaran['penelitian_id'])
                ->get()
                ->getRow();
        }
       
            
        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/surat-keterangan', [
            'periode'   => $periode,
            'user_data' => $userData,
            'pendaftaran' => $pendaftaran,  
            'feedback'    => $feedback
        ]);
    }

    public function saveFeedback()
    {
        $userId   = user_id();
        $penelitianId = $this->request->getPost('penelitian_id');

        $data = [
            'penelitian_id'        => $penelitianId,
            
            // Feedback untuk Pusdiklat
            'diklat_website'   => $this->request->getPost('diklat_website'),
            'diklat_admin'     => $this->request->getPost('diklat_admin'),
            'diklat_saran'     => $this->request->getPost('diklat_saran'),
            
            // Feedback untuk Unit Kerja
            'unit_supervisor'  => $this->request->getPost('unit_supervisor'),
            'unit_pengalaman'  => $this->request->getPost('unit_pengalaman'),
            'unit_suasana'     => $this->request->getPost('unit_suasana'),
            'unit_kesan'       => $this->request->getPost('unit_kesan'),

            'updated_at'       => date('Y-m-d H:i:s')
        ];

        // cek apakah feedback sudah ada
        $feedback = $this->feedbackPenelitiaModel
            ->where('penelitian_id', $penelitianId)
            ->get()
            ->getRow();

        if ($feedback) {
            // update
            $this->feedbackPenelitiaModel->update($feedback->feedback_id, $data);
        } else {
            // insert
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->feedbackPenelitiaModel->insert($data);
        }

        return redirect()->back()->with('success', 'Feedback berhasil disimpan.');
    }

    

    public function cetakSuratKeterangan($saveToFile = false)
    {
        $userId = user_id();

        // Ambil data user & penelitian yang lulus
        $user = $this->userModel
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id')
            ->find($userId);

        $penelitian = $this->penelitianModel
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id')
            ->where('user_id', $userId)
            ->where('status_akhir', 'lulus')
            ->orderBy('penelitian_id', 'DESC')
            ->first();

        if (!$penelitian) {
            return redirect()->back()->with('error', 'Tidak ada penelitian aktif untuk dicetak surat keterangan.');
        }

        if ($penelitian['ka_unit_approve'] != 1) {
            return redirect()->back()->with('error', 'Surat keterangan belum bisa diunduh.');
        }

        $kepala = $this->unitUserModel
        ->select('users.fullname, unit_kerja.unit_kerja, users.eselon, users.tanda_tangan')
        ->join('users', 'users.id = unit_user.user_id')
        ->join('unit_kerja', 'unit_kerja.unit_id = unit_user.unit_id')
        ->where('unit_user.unit_id', 44)
        ->where('users.eselon', '2')
        ->first();

        // --- Rapikan Fullname ---
        $fullname = $kepala['fullname'];

        // Hilangkan gelar umum (awal / akhir nama)
        $fullname = preg_replace('/\b(Drs?|Ir|H|S\.Kom|M\.Kom|M\.Sc|M\.M|PhD|S\.Pd|M\.Pd|S\.T|M\.T)\b\.?/i', '', $fullname);

        // Hilangkan gelar belakang seperti: ", S.Kom", ", M.Kom"
        $fullname = preg_replace('/,\s*[A-Za-z\.]+$/i', '', $fullname);

        // Rapikan spasi
        $fullname = trim($fullname);

        // Kapital awal setiap kata
        $fullname = ucwords(strtolower($fullname));

        $namaKepala = $fullname;

        $unitkerja = $kepala['unit_kerja'];

        // Hilangkan kata "Unit" di awal
        $unitkerja = preg_replace('/^Unit\s*/i', '', $unitkerja);

        // Kapital awal kata (kalau mau)
        $unitkerja = ucwords(strtolower($unitkerja));

        $unitKepala = $unitkerja;


        // ================== Nomor Surat Keterangan ==================
        $tahunSekarang = date('Y');
        $bulanSekarang = date('m');

        $suratKeterangan = $this->suratKeteranganModel
            ->where('penelitian_id', $penelitian['penelitian_id'])
            ->first();

        if (!$suratKeterangan) {
            $last = $this->suratKeteranganModel
                ->where('tahun', $tahunSekarang)
                ->orderBy('nomor', 'DESC')
                ->first();
            $nextNumber = $last ? intval($last['nomor']) + 1 : 1;
            $qrToken = bin2hex(random_bytes(20));

            $this->suratKeteranganModel->insert([
                'penelitian_id' => $penelitian['penelitian_id'],
                'nomor' => $nextNumber,
                'tahun' => $tahunSekarang,
                'qr_token'  => $qrToken,
            ]);
            $suratKeterangan = $this->suratKeteranganModel
                ->where('penelitian_id', $penelitian['penelitian_id'])
                ->first();
        } 
        else {
            $nextNumber = $suratKeterangan['nomor'];
        }

        // === Lazy QR token
        if (empty($suratKeterangan['qr_token'])) {
            $qrToken = bin2hex(random_bytes(20));
            $this->suratKeteranganModel->update($suratKeterangan['sertifikat_id'], [
                'qr_token' => $qrToken
            ]);
            $suratKeterangan['qr_token'] = $qrToken;
        }

        // ================= FILE PATH =================
        $fileName = 'surat-keterangan-' . $suratKeterangan['qr_token'] . '.pdf';
        $filePath = FCPATH . 'uploads/surat/pdf/' . $fileName;

        // ================= JIKA FILE SUDAH ADA =================
        if (!empty($suratKeterangan['file_surat']) && file_exists($filePath)) {
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setBody(file_get_contents($filePath));
        }

        // ================= NOMOR SURKET =================

        $noUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $noSurat = "{$noUrut}/PENELITIAN/SP/{$bulanSekarang}.{$tahunSekarang}";

        // ================= GENERATE QR =================
        $qrUrl  = base_url('surat/verify/' . $suratKeterangan['qr_token']);
        // $localIp = '192.168.229.8';
        // $qrUrl = "http://{$localIp}/magangSPNew/public/sertifikat/verify/" . $sertifikat['qr_token'];
        $qrPath = FCPATH . 'uploads/surat/qr/' . $suratKeterangan['qr_token'] . '.png';
        $logoPath = FCPATH . 'assets/img/SP_logo.png';

        if (!file_exists($logoPath)) {
            throw new \RuntimeException('Logo QR tidak ditemukan: ' . $logoPath);
        }
        if (!file_exists($qrPath)) {
            if (!is_dir(dirname($qrPath))) {
                mkdir(dirname($qrPath), 0775, true);
            }
        

            $builder = new QrBuilder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $qrUrl,

                // ❗ TIDAK BOLEH NULL
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10,

                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255),

                labelText: '',
                

                // LOGO
                logoPath: $logoPath,
                logoResizeToWidth: 70,
                logoResizeToHeight: null,
                logoPunchoutBackground: true
            );

            $result = $builder->build();
            $result->saveToFile($qrPath);


        }

        // ================== TCPDF ==================
        $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(30, 20, 30); // ← margin kiri kanan = 3cm
        $pdf->AddPage();

        // ================== Header (Logo) ==================
        $sigLogo = FCPATH . 'assets/img/SIG_Logo.png';
        $spLogo = FCPATH . 'assets/img/SP_logo22.png';

        if (file_exists($sigLogo)) {
            $pdf->Image($sigLogo, 25, 15, 15, '', 'PNG');
        }
        if (file_exists($spLogo)) {
            $pdf->Image($spLogo, 175, 15, 15, '', 'PNG');
        }

        // ================== Judul ==================
        $pdf->Ln(15);
        $pdf->SetFont('times', 'B', 20);
        $pdf->Cell(0, 12, 'SURAT KETERANGAN', 0, 1, 'C');

        // Garis bawah (underline judul)
        $pageWidth = $pdf->GetPageWidth();
        $textWidth = $pdf->GetStringWidth('SURAT KETERANGAN', 'times', 'B', 20);
        $centerX = ($pageWidth - $textWidth) / 2;
        $pdf->SetLineWidth(0.3);
        $pdf->Line($centerX, $pdf->GetY() - 2, $centerX + $textWidth, $pdf->GetY() - 2);

        // Nomor surat
        $pdf->SetFont('times', '', 12);
        $pdf->Cell(0, 5, 'Nomor: ' . $noSurat, 0, 1, 'C');

        // ================== Paragraf pembuka ==================
        $pdf->Ln(8);
        $pdf->SetFont('times', '', 12);
        $pdf->MultiCell(0, 7, 'Dengan ini menerangkan bahwa mahasiswa yang tersebut di bawah ini:', 0, 'L');

        // ================== Data Mahasiswa ==================
        $pdf->Ln(4);
        $pdf->SetFont('times', '', 12);
        $pdf->Cell(35, 7, 'Nama', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(0, 7, $user->fullname ?? $user->nama, 0, 1);

        $pdf->Cell(35, 7, 'NIM/NISN', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(0, 7, $user->nisn_nim ?? '-', 0, 1);

        $pdf->Cell(35, 7, 'Jurusan', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(0, 7, $user->nama_jurusan ?? '-', 0, 1);

        $pdf->Cell(35, 7, 'Perguruan Tinggi', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(0, 7, $user->nama_instansi ?? '-', 0, 1);

        // ================== Isi surat ==================
        $pdf->Ln(8);
        $pdf->MultiCell(
            0,
            7,
            'Telah selesai melaksanakan Penelitian/Pengambilan Data di ' .
            ($penelitian['unit_kerja'] ?? '-') . ' PT Semen Padang, ' .
            'dari tanggal ' . format_tanggal_indonesia($penelitian['tanggal_masuk']) .
            ' s.d. ' . format_tanggal_indonesia($penelitian['tanggal_selesai']) . '.',
            0,
            'J'
        );

        // ================== Judul Penelitian ==================
        $pdf->Ln(8);
        $pdf->SetFont('times', '', 12);
        $pdf->MultiCell(0, 7, 'Dengan judul :', 0, 'L');

        $pdf->Ln(8);
        $pdf->SetFont('times', 'B', 12);
        $judul = '"' . ($penelitian['judul_penelitian'] ?? '-') . '"';
        $pdf->MultiCell(0, 7, $judul, 0, 'C');

        // ================== Penutup ==================
        $pdf->Ln(8);
        $pdf->SetFont('times', '', 12);
        $pdf->MultiCell(0, 7, 'Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.', 0, 'J');

        // ================== Tanda tangan (rata kiri) ==================
        $pdf->Ln(15);
        $tanggalApprove = !empty($penelitian['tanggal_approve'])
            ? format_tanggal_indonesia($penelitian['tanggal_approve'])
            : format_tanggal_indonesia(date('Y-m-d'));

        $pdf->SetFont('times', '', 12);
        $pdf->Cell(0, 7, "Padang, " . $tanggalApprove, 0, 1, 'L');
        $pdf->Cell(0, 7, $unitKepala, 0, 1, 'L');
        $pdf->Ln(15);

        $stamp = FCPATH . 'assets/img/stempel.png';
        if (file_exists($stamp)) {
            $pdf->Image($stamp, 25, 200, 35, '', 'PNG');
        }

        $ttdPath = FCPATH . 'uploads/tanda-tangan/'.$kepala['tanda_tangan'];
        if (file_exists($ttdPath)) {
            $pdf->Image($ttdPath, 35, $pdf->GetY() - 10, 40, '', 'PNG');
        }

        $pdf->SetFont('times', 'B', 12);
        $pdf->Ln(13);
        $pdf->Cell(0, 7, $namaKepala, 0, 1, 'L');
        $pdf->SetFont('times', '', 12);
        $pdf->Cell(0, 7, "Kepala", 0, 1, 'L');

        // === TEKS DI ATAS QR
        $pdf->SetFont('times', 'I', 9);   
        $pdf->SetTextColor(0, 0, 0);

        // Posisi X sama dengan QR, Y sedikit di atas QR
        $pdf->SetXY(160, 230);
        $pdf->Cell(30, 6, 'Scan me for validate', 0, 0, 'C');

        // === QR KANAN BAWAH HALAMAN 1
        $pdf->Image($qrPath, 160, 235, 30, 30, 'PNG');

        // ================== Output ==================
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0775, true);
        }

        $pdf->Output($filePath, 'F');

        $this->suratKeteranganModel->update($suratKeterangan['surat_keterangan_id'], [
            'file_surat' => $fileName
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setBody(file_get_contents($filePath));
    }

    public function cetak_absensi($id)
    {
        $db = db_connect();
        $pendaftaran = $db->table('penelitian')
            ->select('penelitian.*, users.fullname, users.nisn_nim, unit_kerja.unit_kerja')
            ->join('users', 'users.id = penelitian.user_id')
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id')
            ->where('penelitian.penelitian_id', $id)
            ->get()->getRowArray();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // ==================== MULAI PDF ====================
        $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Lembar Absensi');
        $pdf->SetMargins(25, 15, 25); // margin kiri-kanan lebih lebar sedikit
        $pdf->setPrintHeader(false); // ⬅️ tambahkan baris ini
        $pdf->setPrintFooter(false); // ⬅️ tambahkan baris ini (opsional)
        $pdf->AddPage();

        // ================== Header (Logo) ==================
        $sigLogo = FCPATH . 'assets/img/SIG_Logo.png';
        $spLogo = FCPATH . 'assets/img/SP_logo22.png';

        if (file_exists($sigLogo)) {
            $pdf->Image($sigLogo, 25, 12, 10, '', 'PNG');
        }
        if (file_exists($spLogo)) {
            $pdf->Image($spLogo, 180, 10, 10, '', 'PNG');
        }

        // ==================== JUDUL ====================
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(0, 8, 'LEMBAR ABSENSI', 0, 1, 'C');
        $pdf->Ln(4);

        // ==================== DATA PESERTA ====================
        $pdf->SetFont('times', '', 11);
        $pdf->Cell(35, 7, 'Nama', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(100, 7, $pendaftaran['fullname'], 0, 1);

        $pdf->Cell(35, 7, 'NISN/NIM', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(100, 7, $pendaftaran['nisn_nim'], 0, 1);

        $pdf->Cell(35, 7, 'Bagian / Unit Kerja', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(100, 7, $pendaftaran['unit_kerja'], 0, 1);

        $pdf->Cell(35, 7, 'Bulan', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 1);
        $pdf->Ln(3);

        // ==================== TABEL ABSENSI ====================
        $pdf->SetFont('times', 'B', 10);

        // Baris header atas (Paraf)
        $pdf->Cell(20, 10, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Masuk', 1, 0, 'C');
        $pdf->Cell(20, 10, 'Keluar', 1, 0, 'C');
        $pdf->Cell(105, 5, 'Paraf', 1, 0, 'C'); // merge 5 kolom ke bawah
        $pdf->Cell(0, 5, '', 0, 1); // newline untuk baris header bawah

        // Header bawah (Hadir, Sakit, Izin, Alpa, Keterangan)
        $pdf->SetX($pdf->GetX() - 160);
        $pdf->Cell(10, 5, '', 0, 0); 
        $pdf->Cell(15, 5, 'Hadir', 1, 0, 'C');
        $pdf->Cell(15, 5, 'Sakit', 1, 0, 'C');
        $pdf->Cell(15, 5, 'Izin', 1, 0, 'C');
        $pdf->Cell(15, 5, 'Alpa', 1, 0, 'C');
        $pdf->Cell(45, 5, 'Keterangan', 1, 1, 'C');

        $pdf->SetFont('times', '', 10);

        // Isi tabel (1–31)
        for ($i = 1; $i <= 31; $i++) {

            // ============= Warna Belang =============
            if ($i % 2 == 0) {
                // warna abu muda
                $pdf->SetFillColor(235, 235, 235);
            } else {
                // warna putih
                $pdf->SetFillColor(255, 255, 255);
            }
            $fill = true;
            // ========================================

            $pdf->Cell(20, 6, $i, 1, 0, 'C', $fill);
            $pdf->Cell(20, 6, '', 1, 0, 'C', $fill);
            $pdf->Cell(20, 6, '', 1, 0, 'C', $fill);
            $pdf->Cell(15, 6, '', 1, 0, 'C', $fill);
            $pdf->Cell(15, 6, '', 1, 0, 'C', $fill);
            $pdf->Cell(15, 6, '', 1, 0, 'C', $fill);
            $pdf->Cell(15, 6, '', 1, 0, 'C', $fill);
            $pdf->Cell(45, 6, '', 1, 1, 'C', $fill);
        }


        // ==================== OUTPUT ====================
        $pdf->Output('Absensi_' . $pendaftaran['nisn_nim'] . '.pdf', 'I');
        exit;
    }

    public function cetak_formulir_data($id)
    {
        $db = db_connect();
        $pendaftaran = $db->table('penelitian')
            ->select('penelitian.*, users.fullname, pembimbing.fullname as nama_pembimbing, users.nisn_nim,jurusan.nama_jurusan, instansi.nama_instansi, unit_kerja.unit_kerja')
            ->join('users', 'users.id = penelitian.user_id')
            ->join('users pembimbing', 'users.id = penelitian.pembimbing_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id')
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id')
            ->where('penelitian.penelitian_id', $id)
            ->get()->getRowArray();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // ==================== MULAI PDF ====================
        $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Formulir Pengambilan Data');
        $pdf->SetMargins(20, 20, 20);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // ==================== HEADER DENGAN LOGO ====================
        // ================== Header (Logo) ==================
        $sigLogo = FCPATH . 'assets/img/SIG_Logo.png';
        $spLogo = FCPATH . 'assets/img/SP_logo22.png';

        if (file_exists($sigLogo)) {
            $pdf->Image($sigLogo, 25, 12, 10, '', 'PNG');
        }
        if (file_exists($spLogo)) {
            $pdf->Image($spLogo, 180, 10, 10, '', 'PNG');
        }
        $pdf->Ln(3);

        $pdf->SetFont('times', 'B', 13);

        $pdf->MultiCell(0, 7, 
            "FORMULIR PENGAMBILAN DATA / PENELITIAN MAHASISWA\nDI PT SEMEN PADANG", 
            0, 
            'C'
        );
        $pdf->Ln(5);

        // ==================== KETERANGAN ====================
        $pdf->SetFont('times', '', 11);
        $pdf->MultiCell(0, 6, 'Yang bertanda tangan di bawah ini menerangkan bahwa:', 0, 'L', 0, 1);
        $pdf->Ln(3);

        // ==================== DATA MAHASISWA ====================
        $pdf->Cell(40, 7, 'Nama', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(100, 7, $pendaftaran['fullname'], 0, 1);

        $pdf->Cell(40, 7, 'NIM / NIS', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(100, 7, $pendaftaran['nisn_nim'], 0, 1);

        $pdf->Cell(40, 7, 'Jurusan', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(100, 7, $pendaftaran['nama_jurusan'], 0, 1);

        $pdf->Cell(40, 7, 'Perguruan Tinggi', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(100, 7, $pendaftaran['nama_instansi'], 0, 1);

        $pdf->Cell(40, 7, 'Judul Penelitian', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->MultiCell(0, 7, $pendaftaran['judul_penelitian'] ?? '-', 0, 'L');

        $pdf->Cell(40, 7, 'Unit Kerja', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(100, 7, $pendaftaran['unit_kerja'], 0, 1);

        $pdf->Cell(40, 7, 'Jadwal Penelitian', 0, 0);
        $pdf->Cell(5, 7, ':', 0, 0);
        $pdf->Cell(100, 7, format_tanggal_indonesia($pendaftaran['tanggal_masuk']).' s/d '. format_tanggal_indonesia($pendaftaran['tanggal_selesai']) ?? '-', 0, 1);

        $pdf->Ln(4);

        // ==================== PERNYATAAN ====================
        $pdf->MultiCell(0, 6, 'Telah selesai melakukan pengambilan data/penelitian terkait perusahaan sebagai berikut:', 0, 'L', 0, 1);
        $pdf->Ln(3);

        // ==================== TABEL DATA ====================
        $pdf->SetFont('times', 'B', 10);
        $pdf->Cell(10, 10, 'No', 1, 0, 'C');
        $pdf->Cell(60, 10, 'Jenis Data', 1, 0, 'C');
        $pdf->Cell(45, 10, 'Unit Kerja', 1, 0, 'C');

        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell(40, 5, "Nama Pejabat\nyang Memberikan Data", 0, 'C'); 
        $pdf->SetXY($x, $y);
        $pdf->Cell(40, 10, '', 1, 0);

        $pdf->Cell(25, 10, 'Tanda Tangan', 1, 1, 'C');


        $pdf->SetFont('times', '', 10);
        for ($i = 1; $i <= 10; $i++) {

            // Belang-belang
            if ($i % 2 == 0) {
                $pdf->SetFillColor(235, 235, 235); // abu terang
            } else {
                $pdf->SetFillColor(255, 255, 255); // putih
            }
            $fill = true;

            $pdf->Cell(10, 8, $i, 1, 0, 'C', $fill);
            $pdf->Cell(60, 8, '', 1, 0, '', $fill);
            $pdf->Cell(45, 8, '', 1, 0, '', $fill);
            $pdf->Cell(40, 8, '', 1, 0, '', $fill);
            $pdf->Cell(25, 8, '', 1, 1, '', $fill);
        }


        $pdf->Ln(6);

        // ==================== PERNYATAAN ====================
        $pdf->MultiCell(0, 6, 'Demikian Formulir ini disampaikan, untuk dapat digunakan seperlunya.', 0, 'L', 0, 1);
        $pdf->Ln(7);


        // ==================== TANDA TANGAN ====================
        $pdf->SetFont('times', '', 11);
        $pdf->Cell(60, 6, 'Mahasiswa', 0, 0, 'C');

        $pdf->Cell(150, 6, 'Padang,', 0, 1, 'C');
        $pdf->Cell(294, 10, 'Pembimbing Lapangan', 0, 1, 'C');
        $pdf->Ln(20);
        $pdf->SetFont('times', 'B', 11);
        $pdf->Cell(60, 6, '(' . $pendaftaran['fullname'] . ')', 0, 0, 'C');
        $pdf->Cell(180, 6, $pendaftaran['nama_pembimbing'] ?? '(........................................)', 0, 1, 'C');

        // ==================== OUTPUT ====================
        $pdf->Output('Formulir_Pengambilan_Data_' . $pendaftaran['nisn_nim'] . '.pdf', 'I');
        exit;
    }




}
