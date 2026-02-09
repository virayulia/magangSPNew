<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Controllers\Unitkerja;
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
use App\Models\UnitUserModel;
use Endroid\QrCode\Builder\Builder as QrBuilder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;


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
    protected $unitUserModel;

    
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
        $this->unitUserModel = new UnitUserModel();


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
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang','lulus'])
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
            'data' => $magang,
            'tipe'  => 'magang',
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

        // Ambil data pendaftaran user
        $pendaftaran = $this->magangModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang', 'lulus'])
            ->orderBy('tanggal_daftar', 'desc')
            ->first();

        //Ambil riwayat safety
        $riwayatSafety = [];
        if ($pendaftaran) {
            $riwayatSafety = $this->jawabanModel
                ->join('magang', 'magang.magang_id = jawaban_safety.relasi_id')
                ->where('magang.magang_id', $pendaftaran['magang_id'])
                ->where('jawaban_safety.tipe', 'magang')
                ->orderBy('tanggal_ujian', 'desc')
                ->findAll();
        }
        
        // Ambil periode aktif
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

        // Ambil semua riwayat pendaftaran (untuk ditampilkan sebagai histori)
        $data['histori'] = $this->magangModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->select('magang.*, unit_kerja.unit_kerja')
            ->whereNotIn('magang.status_akhir', ['pendaftaran', 'proses', 'magang', 'batal'])
            ->orderBy('magang.tanggal_daftar', 'DESC')
            ->findAll();
       

        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/pelaksanaan', [
            'periode'   => $periode,
            'user_data' => $userData,
            'pendaftaran' => $pendaftaran,
            'riwayat_safety' => $riwayatSafety,
            'histori' => $data['histori'],
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
            'tipe'      => 'magang',
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
        $soal = $this->soalSafetyModel->findAll();

        return view('user/tes-safety',
                    ['soal' => $soal,
                    'tipe'  => 'magang']);
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
            ->where('relasi_id', $magangId)
            ->where('tanggal_ujian', $tanggalHariIni)
            ->where('tipe','magang')
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
            'tipe'          => 'magang',
            'relasi_id'     => $magangId,
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

     public function cetak_absensi($id)
    {
        $db = db_connect();
        $pendaftaran = $db->table('magang')
            ->select('magang.*, users.fullname, users.nisn_nim, unit_kerja.unit_kerja')
            ->join('users', 'users.id = magang.user_id')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->where('magang.magang_id', $id)
            ->get()->getRowArray();

        if (!$pendaftaran) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // ==================== MULAI PDF ====================
        $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetTitle('Lembar Presensi');
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
        $pdf->Cell(0, 8, 'LEMBAR PRESENSI', 0, 1, 'C');
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
            ->whereIn('status_akhir', ['pendaftaran', 'proses', 'magang', 'lulus'])
            ->orderBy('tanggal_daftar', 'desc')
            ->first();
    
        // $riwayatSafety = $this->jawabanModel
        //     ->join('magang', 'magang.magang_id = jawaban_safety.magang_id')
        //     ->where('magang.user_id', $userId)
        //     ->orderBy('tanggal_ujian', 'desc')
        //     ->findAll();
        
        // Ambil periode aktif
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();
        
        // Ambil semua riwayat pendaftaran (untuk ditampilkan sebagai histori)
        $data['histori'] = $this->magangModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->select('magang.*, unit_kerja.unit_kerja')
            ->whereNotIn('magang.status_akhir', ['pendaftaran', 'proses', 'magang', 'batal'])
            ->orderBy('magang.tanggal_daftar', 'DESC')
            ->findAll();
       

        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/unggah-laporan', [
            'periode'   => $periode,
            'user_data' => $userData,
            'pendaftaran' => $pendaftaran,
            'histori' => $data['histori'],
            // 'riwayat_safety' => $riwayatSafety
        ]);
    }

    public function uploadLaporanAbsensi($id)
    {
        // Ambil input form
        $judulLaporan = $this->request->getPost('judul');
        $laporan      = $this->request->getFile('laporan');
        $url_laporan      = $this->request->getPost('url_laporan');
        $absensi      = $this->request->getFile('absensi');
        $url_absensi      = $this->request->getPost('url_absensi');

        $magang = $this->magangModel->find($id);

        if (!$magang) { 
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        $user = $this->userModel->find($magang['user_id']);
        if (!$user) {
            return redirect()->back()->with('error', 'User tidak ditemukan.');
        }

        $result = [];

        // Ambil tanggal + jam sekarang
        $now = date('Y-m-d H:i:s');

        // === Simpan judul laporan ===
        if (!empty($judulLaporan)) {
            $this->magangModel->update($id, [
                'judul_laporan' => $judulLaporan,
            ]);
            $result[] = 'Judul laporan berhasil disimpan.';
        }
        if (!empty($url_laporan)) {
            $this->magangModel->update($id, [
                'url_laporan' => $url_laporan,
                'catatan_laporan' => null,
            ]);
            $result[] = 'URL laporan berhasil disimpan.';
        }

        if (!empty($url_absensi)) {
            $this->magangModel->update($id, [
                'url_absensi' => $url_absensi,
                'catatan_absensi' => null,
            ]);
            $result[] = 'URL absensi berhasil disimpan.';
        }

        // === Upload File Laporan ===
        if ($laporan && $laporan->isValid()) {

            if ($laporan->getSize() > 10 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran file laporan terlalu besar. Maksimal 10 MB.');
            }

            $laporanName = uploadBerkasUser($laporan, $user->fullname ?? 'user', 'laporan', $magang['laporan']);
            if ($laporanName) {
                $this->magangModel->update($id, [
                    'laporan'            => $laporanName,
                    'tgl_upload_laporan' => $now,
                    'catatan_laporan'    => null,
                ]);
                $result[] = 'File laporan berhasil diupload.';
            }
        }

        // === Upload Absensi ===
        if ($absensi && $absensi->isValid()) {

            if ($absensi->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran file absensi terlalu besar. Maksimal 2 MB.');
            }

            $absensiName = uploadBerkasUser($absensi, $user->fullname ?? 'user', 'absensi', $magang['absensi']);
            if ($absensiName) {
                $this->magangModel->update($id, [
                    'absensi'            => $absensiName,
                    'tgl_upload_absensi' => $now,
                    'catatan_absensi'    => null,
                ]);
                $result[] = 'File absensi berhasil diupload.';
            }
        }

        // === Notifikasi ===
        if (!empty($result)) {
            return redirect()->back()->with('success', implode(' ', $result));
        }

        return redirect()->back()->with('error', 'Tidak ada file atau data yang berhasil diupload.');
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

        $data['histori'] = $this->magangModel
            ->where('user_id', $userId)
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->join('sertifikat_magang','sertifikat_magang.magang_id = magang.magang_id')
            ->select('magang.*, unit_kerja.unit_kerja, sertifikat_magang.file_sertifikat')
            ->whereNotIn('magang.status_akhir', ['pendaftaran', 'proses', 'magang', 'batal'])
            ->orderBy('magang.tanggal_daftar', 'DESC')
            ->findAll();
            
        // Kalau sudah lulus, tampilkan view pelaksanaan normal
        return view('user/sertifikat-magang', [
            'periode'   => $periode,
            'user_data' => $userData,
            'penilaian' => $penilaian,
            'pendaftaran' => $pendaftaran,  
            'feedback'    => $feedback,
            'histori' => $data['histori'],
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


    public function cetakSertifikat()
    {
        $userId = user_id();

        // ================= DATA USER & MAGANG =================
        $user = $this->userModel
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id')
            ->find($userId);

        $magang = $this->magangModel
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->where('user_id', $userId)
            ->where('status_akhir', 'lulus')
            ->orderBy('magang_id', 'DESC')
            ->first();

        if (!$magang) {
            return redirect()->back()->with('error', 'Tidak ada magang lulus.');
        }

        $penilaian = $this->penilaianModel
            ->where('magang_id', $magang['magang_id'])
            ->first();

        if (!$penilaian || $magang['ka_unit_approve'] != 1) {
            return redirect()->back()->with('error', 'Sertifikat belum bisa dicetak.');
        }

        // ================= HITUNG NILAI =================
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

        $rataRata = round(array_sum($nilaiList) / count($nilaiList));

        if ($rataRata >= 90) $kategori = 'Baik Sekali';
        elseif ($rataRata >= 80) $kategori = 'Baik';
        elseif ($rataRata >= 70) $kategori = 'Cukup';
        elseif ($rataRata >= 60) $kategori = 'Kurang';
        else $kategori = 'Sangat Kurang';

        // ================= SERTIFIKAT =================
        $tahun = date('Y');
        $bulan = date('m');

        $sertifikat = $this->sertifikatModel
            ->where('magang_id', $magang['magang_id'])
            ->first();

        // === Jika BELUM ADA DATA sertifikat → buat
        if (!$sertifikat) {
            $last = $this->sertifikatModel
                ->where('tahun', $tahun)
                ->orderBy('nomor', 'DESC')
                ->first();

            $nomor = $last ? $last['nomor'] + 1 : 1;
            $qrToken = bin2hex(random_bytes(20));

            $this->sertifikatModel->insert([
                'magang_id' => $magang['magang_id'],
                'nomor'     => $nomor,
                'tahun'     => $tahun,
                'qr_token'  => $qrToken,
            ]);

            $sertifikat = $this->sertifikatModel
                ->where('magang_id', $magang['magang_id'])
                ->first();
        }

        // === Lazy QR token
        if (empty($sertifikat['qr_token'])) {
            $qrToken = bin2hex(random_bytes(20));
            $this->sertifikatModel->update($sertifikat['sertifikat_id'], [
                'qr_token' => $qrToken
            ]);
            $sertifikat['qr_token'] = $qrToken;
        }

        // ================= FILE PATH =================
        $fileName = 'sertifikat-' . $sertifikat['qr_token'] . '.pdf';
        $filePath = FCPATH . 'uploads/sertifikat/pdf/' . $fileName;

        // ================= JIKA FILE SUDAH ADA =================
        if (!empty($sertifikat['file_sertifikat']) && file_exists($filePath)) {
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setBody(file_get_contents($filePath));
        }
        // =================    DATA KEPALA UNIT  =================
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


        // ================= NOMOR SERTIFIKAT =================
        $noUrut = str_pad($sertifikat['nomor'], 4, '0', STR_PAD_LEFT);
        $noSertifikat = "{$noUrut}/MAGANG/SP/{$bulan}.{$tahun}";

        // ================= GENERATE QR =================
        $qrUrl  = base_url('sertifikat/verify/' . $sertifikat['qr_token']);
        // $localIp = '192.168.229.8';
        // $qrUrl = "http://{$localIp}/magangSPNew/public/sertifikat/verify/" . $sertifikat['qr_token'];
        $qrPath = FCPATH . 'uploads/sertifikat/qr/' . $sertifikat['qr_token'] . '.png';
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

        // ================= TCPDF =================
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
            // Tambah Stempel
            $stempelPath = FCPATH . 'assets/img/stempel.png';
            if (file_exists($stempelPath)) {
                // X, Y, Width
                $pdf->Image($stempelPath, 17, 210, 45, 0, 'PNG', '', '', false, 300);
            }
            // Posisi mulai (pojok kiri bawah, misal 190mm dari atas)
            $pdf->SetFont('times', '', 16);
            $pdf->SetXY(30, 200);
            $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');

            $pdf->SetFont('times', 'B', 16);
            $pdf->SetX(30);
            $pdf->Cell(0, 8, $unitKepala, 0, 1, 'L');

            // Tambahkan tanda tangan (PNG/JPG transparan lebih bagus)
            $ttdPath = FCPATH . 'uploads/tanda-tangan/'. $kepala['tanda_tangan']; // ganti dengan path tanda tanganmu
            if (file_exists($ttdPath)) {
                $pdf->Image($ttdPath, 30, 215, 45, 0, '', '', '', false, 300);

            }

            // Nama pejabat
            $pdf->SetFont('times', 'B', 16);
            $pdf->SetXY(30, 235);
            $pdf->Cell(0, 8, $namaKepala, 0, 1, 'L');

            $pdf->SetFont('times', '', 14);
            $pdf->SetX(30);
            $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');

            // === TEKS DI ATAS QR
            $pdf->SetFont('times', 'I', 9);   // font kecil & miring
            $pdf->SetTextColor(0, 0, 0);

            // Posisi X sama dengan QR, Y sedikit di atas QR
            $pdf->SetXY(160, 245);
            $pdf->Cell(30, 6, 'Scan me for validate', 0, 0, 'C');

            // === QR KANAN BAWAH HALAMAN 1
            $pdf->Image($qrPath, 160, 250, 30, 30, 'PNG');

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

            // tampilkan kategori full
            $pdf->SetXY(123, $startY + (8 * $stepY) + 12.5);
            $pdf->Cell(60, 10, $kategori, 0, 0, 'L');

            //tambah stempel
            if (file_exists($stempelPath)) {
                $pdf->Image($stempelPath, 110, 215, 45, 0, 'PNG', '', '', false, 300);
            }
            //TTD pojok kanan
            $pdf->SetFont('times', '', 16);
            $pdf->SetXY(105, 215);
            $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');
            $pdf->SetFont('times', 'B', 16);
            $pdf->SetX(105);
            $pdf->Cell(0, 8, $unitKepala, 0, 1, 'L');

            
            // Tambahkan tanda tangan (PNG/JPG transparan lebih bagus)
            $ttdPath = FCPATH . 'uploads/tanda-tangan/'.$kepala['tanda_tangan']; 
            if (file_exists($ttdPath)) {
                $pdf->Image($ttdPath, 105, 228, 45, 0, '', '', '', false, 300);

            }

            // Nama pejabat
            $pdf->SetFont('times', 'B', 16);
            $pdf->SetXY(105, 245);
            $pdf->Cell(0, 8, $namaKepala, 0, 1, 'L');

            $pdf->SetFont('times', '', 14);
            $pdf->SetX(105);
            $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');

            // ================= SIMPAN PDF =================
            if (!is_dir(dirname($filePath))) {
                mkdir(dirname($filePath), 0775, true);
            }

            $pdf->Output($filePath, 'F');

            $this->sertifikatModel->update($sertifikat['sertifikat_id'], [
                'file_sertifikat' => $fileName
            ]);

            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setBody(file_get_contents($filePath));
    }






    // old
    // public function cetakSertifikat($saveToFile = false)
    // {
    //     $userId = user_id();

    //     // Ambil data user & magang terbaru yang lulus
    //     $user = $this->userModel->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
    //                             ->join('instansi', 'instansi.instansi_id = users.instansi_id')
    //                             ->find($userId);
    //     $magang = $this->magangModel->join('unit_kerja', 'unit_kerja.unit_id=magang.unit_id')
    //         ->where('user_id', $userId)
    //         ->where('status_akhir', 'lulus')
    //         ->orderBy('magang_id', 'DESC')
    //         ->first();

    //     if (!$magang) {
    //         return redirect()->back()->with('error', 'Tidak ada magang aktif untuk dicetak sertifikat.');
    //     }

    //     $penilaian = $this->penilaianModel->where('magang_id', $magang['magang_id'])->first();

    //     if (!$penilaian || $magang['ka_unit_approve'] != 1) {
    //         return redirect()->back()->with('error', 'Sertifikat belum bisa diunduh.');
    //     }

    //     // Hitung total & rata-rata
    //     $totalNilai = $penilaian['nilai_disiplin']
    //         + $penilaian['nilai_kerajinan']
    //         + $penilaian['nilai_tingkahlaku']
    //         + $penilaian['nilai_kerjasama']
    //         + $penilaian['nilai_kreativitas']
    //         + $penilaian['nilai_kemampuankerja']
    //         + $penilaian['nilai_tanggungjawab']
    //         + $penilaian['nilai_penyerapan'];

    //     $rataRata = round($totalNilai / 8, 0); // bulatkan ke integer

    //     // Tentukan kategori
    //     if ($rataRata >= 90) $kategori = 'Baik Sekali';
    //     elseif ($rataRata >= 80) $kategori = 'Baik';
    //     elseif ($rataRata >= 70) $kategori = 'Cukup';
    //     elseif ($rataRata >= 60) $kategori = 'Kurang';
    //     else $kategori = 'Sangat Kurang';

    //     $kepala = $this->unitUserModel
    //     ->select('users.fullname, unit_kerja.unit_kerja, users.eselon, users.tanda_tangan')
    //     ->join('users', 'users.id = unit_user.user_id')
    //     ->join('unit_kerja', 'unit_kerja.unit_id = unit_user.unit_id')
    //     ->where('unit_user.unit_id', 44)
    //     ->where('users.eselon', '2')
    //     ->first();

    //     // --- Rapikan Fullname ---
    //     $fullname = $kepala['fullname'];

    //     // Hilangkan gelar umum (awal / akhir nama)
    //     $fullname = preg_replace('/\b(Drs?|Ir|H|S\.Kom|M\.Kom|M\.Sc|M\.M|PhD|S\.Pd|M\.Pd|S\.T|M\.T)\b\.?/i', '', $fullname);

    //     // Hilangkan gelar belakang seperti: ", S.Kom", ", M.Kom"
    //     $fullname = preg_replace('/,\s*[A-Za-z\.]+$/i', '', $fullname);

    //     // Rapikan spasi
    //     $fullname = trim($fullname);

    //     // Kapital awal setiap kata
    //     $fullname = ucwords(strtolower($fullname));

    //     $namaKepala = $fullname;

    //     $unitkerja = $kepala['unit_kerja'];

    //     // Hilangkan kata "Unit" di awal
    //     $unitkerja = preg_replace('/^Unit\s*/i', '', $unitkerja);

    //     // Kapital awal kata (kalau mau)
    //     $unitkerja = ucwords(strtolower($unitkerja));

    //     $unitKepala = $unitkerja;

    //     // ================== Nomor Sertifikat ==================
    //     $tahunSekarang = date('Y');
    //     $bulanSekarang = date('m');

    //     // cek apakah sudah ada nomor sertifikat untuk magang ini
    //     $sertifikat = $this->sertifikatModel
    //         ->where('magang_id', $magang['magang_id'])
    //         ->first();

    //     if (!$sertifikat) {
    //         // ambil nomor urut terakhir tahun berjalan
    //         $last = $this->sertifikatModel
    //             ->where('tahun', $tahunSekarang)
    //             ->orderBy('nomor', 'DESC')
    //             ->first();

    //         $nextNumber = $last ? intval($last['nomor']) + 1 : 1;

    //         // simpan ke tabel sertifikat
    //         $this->sertifikatModel->insert([
    //             'magang_id' => $magang['magang_id'],
    //             'nomor'     => $nextNumber,
    //             'tahun'     => $tahunSekarang,
    //         ]);
    //     } else {
    //         $nextNumber = $sertifikat['nomor'];
    //     }

    //     // format nomor sertifikat
    //     $noUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    //     $noSertifikat = "{$noUrut}/MAGANG/SP/{$bulanSekarang}.{$tahunSekarang}";


        // // Inisialisasi TCPDF
        // $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        // $pdf->SetPrintHeader(false);
        // $pdf->SetPrintFooter(false);
        // $pdf->SetMargins(0, 0, 0);
        // $pdf->SetAutoPageBreak(TRUE, 0);

    //     // ================= Halaman 1 =================
    //     $pdf->AddPage();
    //     $cover1 = FCPATH . 'assets/img/page1.png';
    //     $pdf->Image($cover1, 0, 0, 210, 297, '', '', '', false, 300);

    //     // Nomor sertifikat
    //     $pdf->SetFont('times', 'I', 14);
    //     $pdf->SetXY(11, 63.5); 
    //     $pdf->Cell(210, 10,": " .$noSertifikat, 0, 1, 'C');

    //     // Nama peserta
    //     $pdf->SetFont('times', 'B', 24);
    //     $pdf->SetXY(0, 90);
    //     $pdf->Cell(210, 18, $user->fullname ?? $user->nama, 0, 1, 'C');

    //     $pdf->SetFont('times', '', 16);
    //     $pdf->Cell(210, 9, ($user->nisn_nim ?? '-'), 0, 1, 'C');
    //     $pdf->Cell(210, 9, ($user->nama_jurusan ?? '-'), 0, 1, 'C');
    //     $pdf->Cell(210, 9, ($user->nama_instansi ?? '-'), 0, 1, 'C');

    //     // Kalimat keterangan
    //     $pdf->Ln(10);
    //     $pdf->SetFont('times', '', 14);
    //     $marginKiri = 25;
    //     $marginKanan = 25;
    //     $halamanLebar = $pdf->GetPageWidth();
    //     $lebarText = $halamanLebar - $marginKiri - $marginKanan;
    //     $pdf->SetX($marginKiri);

    //     $teks = "Telah selesai melakukan kerja praktek di " .
    //         ($magang['unit_kerja'] ?? '-') . " PT Semen Padang " .
    //         "dari tanggal " . format_tanggal_indonesia($magang['tanggal_masuk']) .
    //         " s/d " . format_tanggal_indonesia($magang['tanggal_selesai']) .
    //         " dengan hasil :";

    //     $pdf->MultiCell($lebarText, 8, $teks, 0, 'C');

    //     //Kategori
    //     $pdf->SetFont('times', 'B', 18);
    //     $pdf->SetXY(0, 170);
    //     $pdf->Cell(210, 10, $kategori, 0, 1, 'C');


    //     // Ambil tanggal approve
    //     $tanggalApprove = !empty($magang['tanggal_approve']) 
    //         ? format_tanggal_indonesia($magang['tanggal_approve']) 
    //         : '-';
    //     // Tambah Stempel
    //     $stempelPath = FCPATH . 'assets/img/stempel.png';
    //     if (file_exists($stempelPath)) {
    //         // X, Y, Width
    //         $pdf->Image($stempelPath, 17, 210, 45, 0, 'PNG', '', '', false, 300);
    //     }
    //     // Posisi mulai (pojok kiri bawah, misal 190mm dari atas)
    //     $pdf->SetFont('times', '', 16);
    //     $pdf->SetXY(30, 200);
    //     $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');

    //     $pdf->SetFont('times', 'B', 16);
    //     $pdf->SetX(30);
    //     $pdf->Cell(0, 8, $unitKepala, 0, 1, 'L');

    //     // Tambahkan tanda tangan (PNG/JPG transparan lebih bagus)
    //     $ttdPath = FCPATH . 'uploads/tanda-tangan/'. $kepala['tanda_tangan']; // ganti dengan path tanda tanganmu
    //     if (file_exists($ttdPath)) {
    //         $pdf->Image($ttdPath, 30, 215, 45, 0, '', '', '', false, 300);

    //     }

    //     // Nama pejabat
    //     $pdf->SetFont('times', 'B', 16);
    //     $pdf->SetXY(30, 235);
    //     $pdf->Cell(0, 8, $namaKepala, 0, 1, 'L');

    //     $pdf->SetFont('times', '', 14);
    //     $pdf->SetX(30);
    //     $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');


    //     // ================= Halaman 2 =================
    //     $pdf->AddPage();
    //     $cover2 = FCPATH . 'assets/img/page2.png';
    //     $pdf->Image($cover2, 0, 0, 210, 297, '', '', '', false, 300);

    //     $pdf->SetFont('times', '', 16);

    //     $startY = 86;
    //     $stepY  = 12.5;

    //     $nilaiList = [
    //         $penilaian['nilai_disiplin'],
    //         $penilaian['nilai_kerajinan'],
    //         $penilaian['nilai_tingkahlaku'],
    //         $penilaian['nilai_kerjasama'],
    //         $penilaian['nilai_kreativitas'],
    //         $penilaian['nilai_kemampuankerja'],
    //         $penilaian['nilai_tanggungjawab'],
    //         $penilaian['nilai_penyerapan'],
    //     ];

    //     // Fungsi terbilang khusus 0 - 100
    //     function terbilang($angka) {
    //         $angka = intval($angka);
    //         if ($angka > 100) return "Seratus"; // mentok 100

    //         $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima",
    //                 "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

    //         if ($angka == 0) return "Nol";
    //         elseif ($angka < 12) return $baca[$angka];
    //         elseif ($angka < 20) return $baca[$angka - 10] . " Belas";
    //         elseif ($angka < 100) {
    //             $puluh = intval($angka / 10);
    //             $sisa  = $angka % 10;
    //             $hasil = $baca[$puluh] . " Puluh";
    //             if ($sisa > 0) $hasil .= " " . $baca[$sisa];
    //             return $hasil;
    //         } else {
    //             return "Seratus";
    //         }
    //     }

    //     foreach ($nilaiList as $i => $nilai) {
    //         $y = $startY + ($i * $stepY);

    //         // angka
    //         $pdf->SetXY(97, $y);
    //         $pdf->Cell(20, 10, $nilai, 0, 0, 'C');

    //         // huruf
    //         $pdf->SetXY(123, $y);
    //         $pdf->Cell(40, 10, terbilang($nilai), 0, 0, 'L');
    //     }

    //     // Rata-rata + kategori
    //     $pdf->SetXY(97, $startY + (8 * $stepY));
    //     $pdf->Cell(20, 10, $rataRata, 0, 0, 'C');
    //     $pdf->SetXY(123, $startY + (8 * $stepY));
    //     $pdf->Cell(40, 10, terbilang($rataRata), 0, 0, 'L');

    //     // tampilkan kategori full
    //     $pdf->SetXY(123, $startY + (8 * $stepY) + 12.5);
    //     $pdf->Cell(60, 10, $kategori, 0, 0, 'L');

    //     //tambah stempel
    //     if (file_exists($stempelPath)) {
    //         $pdf->Image($stempelPath, 110, 215, 45, 0, 'PNG', '', '', false, 300);
    //     }
    //     //TTD pojok kanan
    //     $pdf->SetFont('times', '', 16);
    //     $pdf->SetXY(105, 215);
    //     $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');
    //     $pdf->SetFont('times', 'B', 16);
    //     $pdf->SetX(105);
    //     $pdf->Cell(0, 8, $unitKepala, 0, 1, 'L');

        
    //     // Tambahkan tanda tangan (PNG/JPG transparan lebih bagus)
    //     $ttdPath = FCPATH . 'uploads/tanda-tangan/'.$kepala['tanda_tangan']; // ganti dengan path tanda tanganmu
    //     if (file_exists($ttdPath)) {
    //         $pdf->Image($ttdPath, 105, 228, 45, 0, '', '', '', false, 300);

    //     }

    //     // Nama pejabat
    //     $pdf->SetFont('times', 'B', 16);
    //     $pdf->SetXY(105, 245);
    //     $pdf->Cell(0, 8, $namaKepala, 0, 1, 'L');

    //     $pdf->SetFont('times', '', 14);
    //     $pdf->SetX(105);
    //     $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');

    //     // ================= Output =================
    //     $fileName = 'sertifikat-magang-' . url_title($user->fullname ?? $user->nama, '-', true) . '-' . date('YmdHis') . '.pdf';

    //     if ($saveToFile) {
    //         $filePath = WRITEPATH . 'uploads/' . $fileName;
    //         $pdf->Output($filePath, 'F');
    //         return $filePath;
    //     } else {
    //         $this->response->setContentType('application/pdf');
    //         $pdf->Output($fileName, 'I');
    //         exit;
    //     }
    // }




}
