<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\MY_TCPDF as TCPDF;
use App\Models\UserModel;
use App\Models\InstansiModel;
use App\Models\MagangModel;
use App\Models\SoalSafetyModel;
use App\Models\JawabanSafetyModel;
use App\Models\PenilaianModel;
use App\Models\DetailJawabanSafetyModel;
use App\Models\FeedbackModel;
use App\Models\SertifikatModel;

class MagangController extends BaseController
{
    
    protected $userModel;
    protected $instansiModel;
    protected $magangModel;
    protected $soalSafetyModel;
    protected $jawabanModel;
    protected $penilaianModel;
    protected $detailJawabanModel;
    protected $feedbackModel;
    protected $sertifikatModel;

    
    public function __construct()
    {
        // Inisialisasi model
        $this->userModel = new UserModel();
        $this->instansiModel = new InstansiModel();
        $this->magangModel = new MagangModel();
        $this->soalSafetyModel = new SoalSafetyModel();
        $this->jawabanModel = new JawabanSafetyModel();
        $this->penilaianModel = new PenilaianModel();
        $this->detailJawabanModel = new DetailJawabanSafetyModel();
        $this->feedbackModel = new FeedbackModel();
        $this->sertifikatModel = new SertifikatModel();


    }

    public function statusLamaran()
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
        $data['pendaftaran'] = $this->magangModel
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->select('magang.magang_id as magang_id, magang.*, unit_kerja.*')
            ->where('user_id', $userId)
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang'])
            ->orderBy('tanggal_daftar', 'DESC')
            ->first();

        // Ambil semua riwayat pendaftaran (untuk ditampilkan sebagai histori)
        $data['histori'] = $this->magangModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->select('magang.*, unit_kerja.unit_kerja')
            ->whereNotIn('magang.status_akhir', ['pendaftaran', 'proses', 'magang'])
            ->orderBy('magang.tanggal_daftar', 'DESC')
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
       
        return view('user/status-lamaran', [
            'periode'             => $periode,
            'pendaftaran'         => $data['pendaftaran'],        
            'histori' => $data['histori'],
            'user_data'           => $data['user_data'],
        ]);
    }

    public function daftar()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $userId = user()->id;
        $unitId = $this->request->getPost('unit_id');
        $durasi = $this->request->getPost('durasi');

        if (!$durasi || !is_numeric($durasi)) {
            return redirect()->back()->with('error', 'Durasi magang tidak valid.');
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Cek periode aktif
        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->where('tanggal_tutup >=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->get()
            ->getRow();
        $periode_id = $periode->periode_id ?? null;

        // Cek apakah user sudah pernah daftar magang (status belum ditolak)
        $existingMagang = $this->magangModel
            ->where('user_id', $userId)
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang']) // status belum ditolak
            ->first();

        // Cek apakah user sedang daftar penelitian (status belum ditolak)
        $existingPenelitian = $db->table('penelitian')
            ->where('user_id', $userId)
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'penelitian']) // status belum ditolak
            ->get()
            ->getRow();

        if ($existingMagang || $existingPenelitian) {
            return redirect()->back()->with('error', 'Anda telah melakukan pendaftaran magang atau penelitian. Anda tidak dapat mendaftar lagi saat ini.');
        }

        // Simpan pendaftaran baru
        $this->magangModel->insert([
            'user_id'       => $userId,
            'unit_id'       => $unitId,
            'durasi'        => $durasi,
            'periode_id'    => $periode_id,
            'status_akhir'  => 'pendaftaran',
            'tanggal_daftar'=> date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/magang')->with('success', 'Pendaftaran berhasil dikirim. Silakan pantau pendaftaran Anda di Menu Profil - Pendaftaran Magang.');
    }

    public function konfirmasi()
    {
        // Ambil data pendaftaran berdasarkan id
        $request = service('request');
        $id = $request->getPost('magang_id');
        $pendaftaran = $this->magangModel->find($id);

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
        if ($this->magangModel->update($id, $data)) {
            // Jika berhasil, redirect dengan pesan sukses
            return redirect()->to('/status-lamaran')->with('success', 'Konfirmasi pendaftaran berhasil! Silakan menunggu validasi dari admin sebelum masuk ke tahap berikutnya.');
        } else {
            // Jika gagal, tampilkan pesan error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengkonfirmasi pendaftaran.');
        }
 
    }

    public function validasiBerkas()
    {
        $request = service('request');
        $id = $request->getPost('magang_id');

        // Ambil data pendaftaran
        $pendaftaran = $this->magangModel->find($id);

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        $data = [
            'status_validasi_berkas'     => 'Y',
            'tanggal_validasi_berkas' => date('Y-m-d H:i:s')
        ];

        // Jika sebelumnya dinyatakan TIDAK lengkap, reset ulang
        if ($pendaftaran['status_berkas_lengkap'] === 'N') {
            $data['status_berkas_lengkap']        = null;
            $data['tanggal_berkas_lengkap']    = null;
            $data['cttn_berkas_lengkap']  = null;
        }

        if ($this->magangModel->update($id, $data)) {
            return redirect()->to('/status-lamaran')->with('success', 'Validasi berhasil. Kami menghargai komitmen Anda dalam melengkapi dokumen dengan benar. Selanjutnya, silahkan cek email dan website ini secara berkala untuk info validasi berkas Anda.');
        } else {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memvalidasi berkas.');
        }
    }

    public function cetakTandaPengenal($id)
    {

        $magang = $this->magangModel->join('unit_kerja','unit_kerja.unit_id = magang.unit_id')
                                    ->find($id);

        if (!$magang) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data magang tidak ditemukan.');
        }

        // Ambil data user berdasarkan user_id dari tabel magang
        $user = $this->userModel->join('instansi', 'instansi.instansi_id = users.instansi_id')
                                ->where('users.id', $magang['user_id'])
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
            'magang' => $magang,
            'user' => $user,
        ]);
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

        // Ambil data pendaftaran user (pastikan sesuai nama tabel kamu)
        $pendaftaran = $this->magangModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang'])
            ->orderBy('tanggal_daftar', 'desc')
            ->first();

    
        $riwayatSafety = $this->jawabanModel
            ->join('magang', 'magang.magang_id = jawaban_safety.magang_id')
            ->where('magang.magang_id', $pendaftaran['magang_id'])
            ->orderBy('tanggal_ujian', 'desc')
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
       

        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/pelaksanaan', [
            'periode'   => $periode,
            'user_data' => $userData,
            'pendaftaran' => $pendaftaran,
            'riwayat_safety' => $riwayatSafety
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
        $pendaftaran = $this->magangModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->orderBy('tanggal_daftar', 'desc')
            ->first();

        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/surat_pernyataan', [
            'user_data' => $userData,
            'pendaftaran' => $pendaftaran
        ]);
    }

    public function setujuiPernyataan()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $userId = user()->id; // Pastikan sesuai field user kamu
        $tanggal = date('Y-m-d');

        // Update data pendaftaran
        $builder->where('user_id', $userId)
            ->update([
                'tanggal_setujui_pernyataan' => $tanggal
            ]);

        return redirect()->to(base_url('pelaksanaan'))->with('success', 'Surat pernyataan berhasil disetujui.');
    }

    public function safetyTes()
    {
        $data['soal'] = $this->soalSafetyModel->findAll();

        return view('user/tes-safety', $data);
    }

    public function submitTes()
    {
        $userId = user_id(); 
        $jawabanUser = $this->request->getPost('jawaban');

        // Ambil data magang aktif user
        $magang = $this->magangModel
            ->where('user_id', $userId)
            ->where('status_akhir', 'magang')
            ->first();

        if (!$magang) {
            return redirect()->to('/pelaksanaan')->with('error', '❌ Data magang tidak ditemukan.');
        }

        $magangId = $magang['magang_id'];
        $tanggalHariIni = date('Y-m-d');

        // Hitung jumlah percobaan HARI INI
        $percobaanHariIni = $this->jawabanModel
            ->where('magang_id', $magangId)
            ->where('tanggal_ujian', $tanggalHariIni)
            ->countAllResults();

        if ($percobaanHariIni >= 3) {
            return redirect()->to('/pelaksanaan')
                ->with('error', '❌ Anda sudah 3 kali tes hari ini. Silakan coba lagi besok.');
        }

        $soalSemua = $this->soalSafetyModel->findAll();
        $nilaiPerSoal = 100 / count($soalSemua);
        $skor = 0;

        // Mulai transaksi database
        $db = \Config\Database::connect();
        $db->transStart();

        // Simpan jawaban utama dulu
        $this->jawabanModel->insert([
            'magang_id'     => $magangId,
            'nilai'         => 0,
            'percobaan_ke'  => $percobaanHariIni + 1, 
            'tanggal_ujian' => $tanggalHariIni,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $jawabanId = $this->jawabanModel->getInsertID(); // ID jawaban baru
        
        // Simpan detail jawaban per soal
        foreach ($soalSemua as $soal) {
            $soalId = $soal['soal_id'];
            $jawabanBenar = strtolower(trim($soal['jawaban_benar']));
            $jawaban = strtolower(trim($jawabanUser[$soalId] ?? ''));

            $benar = $jawaban === $jawabanBenar;

            if ($benar) {
                $skor += $nilaiPerSoal;
            }

            // Simpan ke detail_jawaban_safety
            $this->detailJawabanModel->insert([
                'jawaban_safety_id' => $jawabanId,
                'soal_safety_id'    => $soalId,
                'jawaban_user'      => $jawaban,
                'benar'             => $benar ? 1 : 0,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        }

        // Bulatkan skor & update jawaban utama
        $skor = round($skor);
        $this->jawabanModel->update($jawabanId, ['nilai' => $skor]);

        $db->transComplete();

        $status = $skor >= 70 ? 'lulus' : 'gagal';
        return redirect()->to('/pelaksanaan')->with('success', "Tes selesai. Skor Anda: $skor ($status)");
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
        $pendaftaran = $this->magangModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang'])
            ->orderBy('tanggal_daftar', 'desc')
            ->first();
    
        $riwayatSafety = $this->jawabanModel
            ->join('magang', 'magang.magang_id = jawaban_safety.magang_id')
            ->where('magang.user_id', $userId)
            ->orderBy('tanggal_ujian', 'desc')
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
       

        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/unggah-laporan', [
            'periode'   => $periode,
            'user_data' => $userData,
            'pendaftaran' => $pendaftaran,
            'riwayat_safety' => $riwayatSafety
        ]);
    }

    public function uploadLaporanAbsensi($id)
    {
        $laporan = $this->request->getFile('laporan');
        $absensi = $this->request->getFile('absensi');
        $magang  = $this->magangModel->find($id);

        if (!$magang) {
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        $user = $this->userModel->find($magang['user_id']);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $result = [];

        // === Upload Laporan ===
        if ($laporan && $laporan->isValid()) {
            if ($laporan->getSize() > 10 * 1024 * 1024) { // 10 MB
                return redirect()->back()->with('error', 'Ukuran file laporan terlalu besar. Maksimal 10 MB.');
            }

            $laporanName = uploadBerkasUser($laporan, $user->fullname ?? 'user', 'laporan');
            if ($laporanName) {
                $this->magangModel->update($id, ['laporan' => $laporanName]);
                $result[] = 'Laporan berhasil diupload.';
            }
        }

        // === Upload Absensi ===
        if ($absensi && $absensi->isValid()) {
            if ($absensi->getSize() > 2 * 1024 * 1024) { // 2 MB
                return redirect()->back()->with('error', 'Ukuran file absensi terlalu besar. Maksimal 2 MB.');
            }

            $absensiName = uploadBerkasUser($absensi, $user->fullname ?? 'user', 'absensi');
            if ($absensiName) {
                $this->magangModel->update($id, ['absensi' => $absensiName]);
                $result[] = 'Absensi berhasil diupload.';
            }
        }

        if (!empty($result)) {
            return redirect()->back()->with('success', implode(' ', $result));
        }

        return redirect()->back()->with('error', 'Tidak ada file yang berhasil diupload.');
    }


    public function sertifikatIndex()
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
        
        $penilaian = $this->magangModel
            ->join('penilaian', 'penilaian.magang_id = magang.magang_id')
            ->where('magang.user_id', $userId)
            ->first();

        $pendaftaran = $this->magangModel
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->select('magang.magang_id as magang_id, magang.*, unit_kerja.*')
            ->where('user_id', $userId)
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang', 'lulus'])
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
            $feedback = $db->table('feedback')
                ->where('magang_id', $pendaftaran['magang_id'])
                ->get()
                ->getRow();
        }
            
        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/sertifikat-magang', [
            'periode'   => $periode,
            'user_data' => $userData,
            'penilaian' => $penilaian,
            'pendaftaran' => $pendaftaran,  
            'feedback'    => $feedback
        ]);
    }

    public function saveFeedback()
    {
        $userId   = user_id();
        $magangId = $this->request->getPost('magang_id');

        $data = [
            'magang_id'        => $magangId,
            
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
        $feedback = $this->feedbackModel
            ->where('magang_id', $magangId)
            ->get()
            ->getRow();

        if ($feedback) {
            // update
            $this->feedbackModel->update($feedback->feedback_id, $data);
        } else {
            // insert
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->feedbackModel->insert($data);
        }

        return redirect()->back()->with('success', 'Feedback berhasil disimpan.');
    }


    public function cetakSertifikat($saveToFile = false)
    {
        $userId = user_id();

        // Ambil data user & magang terbaru yang lulus
        $user = $this->userModel->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
                                ->join('instansi', 'instansi.instansi_id = users.instansi_id')
                                ->find($userId);
        $magang = $this->magangModel->join('unit_kerja', 'unit_kerja.unit_id=magang.unit_id')
            ->where('user_id', $userId)
            ->where('status_akhir', 'lulus')
            ->orderBy('magang_id', 'DESC')
            ->first();

        if (!$magang) {
            return redirect()->back()->with('error', 'Tidak ada magang aktif untuk dicetak sertifikat.');
        }

        $penilaian = $this->penilaianModel->where('magang_id', $magang['magang_id'])->first();

        if (!$penilaian || $magang['ka_unit_approve'] != 1) {
            return redirect()->back()->with('error', 'Sertifikat belum bisa diunduh.');
        }

        // Hitung total & rata-rata
        $totalNilai = $penilaian['nilai_disiplin']
            + $penilaian['nilai_kerajinan']
            + $penilaian['nilai_tingkahlaku']
            + $penilaian['nilai_kerjasama']
            + $penilaian['nilai_kreativitas']
            + $penilaian['nilai_kemampuankerja']
            + $penilaian['nilai_tanggungjawab']
            + $penilaian['nilai_penyerapan'];

        $rataRata = round($totalNilai / 8, 0); // bulatkan ke integer

        // Tentukan kategori
        if ($rataRata >= 90) $kategori = 'Baik Sekali';
        elseif ($rataRata >= 80) $kategori = 'Baik';
        elseif ($rataRata >= 70) $kategori = 'Cukup';
        elseif ($rataRata >= 60) $kategori = 'Kurang';
        else $kategori = 'Sangat Kurang';

        // ================== Nomor Sertifikat ==================
        $tahunSekarang = date('Y');
        $bulanSekarang = date('m');

        // cek apakah sudah ada nomor sertifikat untuk magang ini
        $sertifikat = $this->sertifikatModel
            ->where('magang_id', $magang['magang_id'])
            ->first();

        if (!$sertifikat) {
            // ambil nomor urut terakhir tahun berjalan
            $last = $this->sertifikatModel
                ->where('tahun', $tahunSekarang)
                ->orderBy('nomor', 'DESC')
                ->first();

            $nextNumber = $last ? intval($last['nomor']) + 1 : 1;

            // simpan ke tabel sertifikat
            $this->sertifikatModel->insert([
                'magang_id' => $magang['magang_id'],
                'nomor'     => $nextNumber,
                'tahun'     => $tahunSekarang,
            ]);
        } else {
            $nextNumber = $sertifikat['nomor'];
        }

        // format nomor sertifikat
        $noUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $noSertifikat = "{$noUrut}/MAGANG/SP/{$bulanSekarang}.{$tahunSekarang}";


        // Inisialisasi TCPDF
        $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(TRUE, 0);

        // ================= Halaman 1 =================
        $pdf->AddPage();
        $cover1 = FCPATH . 'assets/img/page1.png';
        $pdf->Image($cover1, 0, 0, 210, 297, '', '', '', false, 300);

        // Nomor sertifikat
        $pdf->SetFont('times', 'I', 14);
        $pdf->SetXY(11, 63.5); 
        $pdf->Cell(210, 10,": " .$noSertifikat, 0, 1, 'C');

        // Nama peserta
        $pdf->SetFont('times', 'B', 24);
        $pdf->SetXY(0, 90);
        $pdf->Cell(210, 18, $user->fullname ?? $user->nama, 0, 1, 'C');

        $pdf->SetFont('times', '', 16);
        $pdf->Cell(210, 9, ($user->nisn_nim ?? '-'), 0, 1, 'C');
        $pdf->Cell(210, 9, ($user->nama_jurusan ?? '-'), 0, 1, 'C');
        $pdf->Cell(210, 9, ($user->nama_instansi ?? '-'), 0, 1, 'C');

        // Kalimat keterangan
        $pdf->Ln(10);
        $pdf->SetFont('times', '', 14);
        $marginKiri = 25;
        $marginKanan = 25;
        $halamanLebar = $pdf->GetPageWidth();
        $lebarText = $halamanLebar - $marginKiri - $marginKanan;
        $pdf->SetX($marginKiri);

        $teks = "Telah selesai melakukan kerja praktek di " .
            ($magang['unit_kerja'] ?? '-') . " PT Semen Padang " .
            "dari tanggal " . format_tanggal_indonesia($magang['tanggal_masuk']) .
            " s/d " . format_tanggal_indonesia($magang['tanggal_selesai']) .
            " dengan hasil :";

        $pdf->MultiCell($lebarText, 8, $teks, 0, 'C');

        //Kategori
        $pdf->SetFont('times', 'B', 18);
        $pdf->SetXY(0, 170);
        $pdf->Cell(210, 10, $kategori, 0, 1, 'C');


        // Ambil tanggal approve
        $tanggalApprove = !empty($magang['tanggal_approve']) 
            ? format_tanggal_indonesia($magang['tanggal_approve']) 
            : '-';

        // Posisi mulai (pojok kiri bawah, misal 190mm dari atas)
        $pdf->SetFont('times', '', 16);
        $pdf->SetXY(30, 200);
        $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');

        $pdf->SetFont('times', 'B', 16);
        $pdf->SetX(30);
        $pdf->Cell(0, 8, "Training & KM", 0, 1, 'L');

        // Tambahkan tanda tangan (PNG/JPG transparan lebih bagus)
        $ttdPath = FCPATH . 'assets/img/ttd.png'; // ganti dengan path tanda tanganmu
        if (file_exists($ttdPath)) {
            $pdf->Image($ttdPath, 30, 215, 45, 0, '', '', '', false, 300);

        }

        // Nama pejabat
        $pdf->SetFont('times', 'B', 16);
        $pdf->SetXY(30, 235);
        $pdf->Cell(0, 8, "Siska Ayu Soraya", 0, 1, 'L');

        $pdf->SetFont('times', '', 14);
        $pdf->SetX(30);
        $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');


        // ================= Halaman 2 =================
        $pdf->AddPage();
        $cover2 = FCPATH . 'assets/img/page2.png';
        $pdf->Image($cover2, 0, 0, 210, 297, '', '', '', false, 300);

        $pdf->SetFont('times', '', 16);

        $startY = 86;
        $stepY  = 12.5;

        $nilaiList = [
            $penilaian['nilai_disiplin'],
            $penilaian['nilai_kerajinan'],
            $penilaian['nilai_tingkahlaku'],
            $penilaian['nilai_kerjasama'],
            $penilaian['nilai_kreativitas'],
            $penilaian['nilai_kemampuankerja'],
            $penilaian['nilai_tanggungjawab'],
            $penilaian['nilai_penyerapan'],
        ];

        // Fungsi terbilang khusus 0 - 100
        function terbilang($angka) {
            $angka = intval($angka);
            if ($angka > 100) return "Seratus"; // mentok 100

            $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima",
                    "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

            if ($angka == 0) return "Nol";
            elseif ($angka < 12) return $baca[$angka];
            elseif ($angka < 20) return $baca[$angka - 10] . " Belas";
            elseif ($angka < 100) {
                $puluh = intval($angka / 10);
                $sisa  = $angka % 10;
                $hasil = $baca[$puluh] . " Puluh";
                if ($sisa > 0) $hasil .= " " . $baca[$sisa];
                return $hasil;
            } else {
                return "Seratus";
            }
        }

        foreach ($nilaiList as $i => $nilai) {
            $y = $startY + ($i * $stepY);

            // angka
            $pdf->SetXY(97, $y);
            $pdf->Cell(20, 10, $nilai, 0, 0, 'C');

            // huruf
            $pdf->SetXY(123, $y);
            $pdf->Cell(40, 10, terbilang($nilai), 0, 0, 'L');
        }

        // Rata-rata + kategori
        $pdf->SetXY(97, $startY + (8 * $stepY));
        $pdf->Cell(20, 10, $rataRata, 0, 0, 'C');
        $pdf->SetXY(123, $startY + (8 * $stepY));
        $pdf->Cell(40, 10, terbilang($rataRata), 0, 0, 'L');

        // tampilkan kategori full, bukan A/B/C
        $pdf->SetXY(123, $startY + (8 * $stepY) + 12.5);
        $pdf->Cell(60, 10, $kategori, 0, 0, 'L');

        //TTD pojok kanan
        $pdf->SetFont('times', '', 16);
        $pdf->SetXY(120, 215);
        $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');
        $pdf->SetFont('times', 'B', 16);
        $pdf->SetX(130);
        $pdf->Cell(0, 8, "Training & KM", 0, 1, 'L');

        // Tambahkan tanda tangan (PNG/JPG transparan lebih bagus)
        $ttdPath = FCPATH . 'assets/img/ttd.png'; // ganti dengan path tanda tanganmu
        if (file_exists($ttdPath)) {
            $pdf->Image($ttdPath, 130, 228, 45, 0, '', '', '', false, 300);

        }

        // Nama pejabat
        $pdf->SetFont('times', 'B', 16);
        $pdf->SetXY(130, 245);
        $pdf->Cell(0, 8, "Siska Ayu Soraya", 0, 1, 'L');

        $pdf->SetFont('times', '', 14);
        $pdf->SetX(130);
        $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');

        // ================= Output =================
        $fileName = 'sertifikat-magang-' . url_title($user->fullname ?? $user->nama, '-', true) . '-' . date('YmdHis') . '.pdf';

        if ($saveToFile) {
            $filePath = WRITEPATH . 'uploads/' . $fileName;
            $pdf->Output($filePath, 'F');
            return $filePath;
        } else {
            $this->response->setContentType('application/pdf');
            $pdf->Output($fileName, 'I');
            exit;
        }
    }




}
