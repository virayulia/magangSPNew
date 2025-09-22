<?php

namespace App\Controllers;

use App\Models\KuotaunitModel;
use App\Models\MagangModel;
use App\Models\UnitKerjaModel;
use App\Models\UserModel;

class Admin extends BaseController
{
    protected $magangModel;
    protected $userModel;
    protected $unitKerjaModel;
    protected $kuotaUnitModel;

    public function __construct()
    {
        $this->magangModel = new MagangModel();
        $this->userModel = new UserModel();
        $this->unitKerjaModel = new UnitKerjaModel();
        $this->kuotaUnitModel = new KuotaunitModel();
    }

    public function index(): string
    {
        $pendaftaran = $this->magangModel->select('magang.*, users.fullname, users.nisn_nim')
                                        ->join('users', 'users.id = magang.user_id')
                                        ->where('magang.status_akhir =', 'pendaftaran')
                                        ->findAll();

        return view('admin/index', ['pendaftaran' => $pendaftaran]);
    }

    public function detail($id)
    {
        $pendaftaran = $this->magangModel
            ->select('magang.*, users.*, instansi.nama_instansi')
            ->join('users', 'users.id = magang.user_id')
            ->join('instansi', 'instansi.id = users.instansi_id')
            ->where('magang.id', $id)
            ->first();

        if (!$pendaftaran) {
            return redirect()->to('/manage-pendaftaran')->with('error', 'Data tidak ditemukan.');
        }

        return view('admin/detail', [
            'pendaftaran' => $pendaftaran
        ]);
    }

    public function indexUnit(): string
    {
        // Ambil periode aktif saat ini
       $unit = $this->unitKerjaModel
                    ->orderBy('active', 'DESC')
                    ->orderBy('unit_kerja', 'ASC')
                    ->findAll();
                
        return view('admin/kelola_unit', ['unit' => $unit]);
    }

    public function updateUnit($id)
    {
        // Ambil periode aktif saat ini
        $data = [
            'unit_kerja'  => $this->request->getPost('unit_kerja'),
            'safety' => $this->request->getPost('safety'),
            'active' => $this->request->getPost('active'),
        ];

        $this->unitKerjaModel->update($id, $data);
   
        return redirect()->back()->with('success', 'Periode berhasil diperbarui.');
    }

    public function indexKuotaUnit(): string
    {
        // Ambil periode aktif saat ini
        $unit = $this->unitKerjaModel->select('unit_kerja.*, kuota_unit.*')
                    ->join('kuota_unit', 'kuota_unit.unit_id = unit_kerja.unit_id')
                    ->findAll();
   
        return view('admin/kelola_kuota_unit', ['unit' => $unit]);
    }

    public function updateKelolaUnit($id)
    {
        // Ambil periode aktif saat ini
        $data = [
            'tingkat_pendidikan'  => $this->request->getPost('tingkat_pendidikan'),
            'kuota' => $this->request->getPost('kuota'),
        ];

        $this->kuotaUnitModel->update($id, $data);
   
        return redirect()->back()->with('success', 'Periode berhasil diperbarui.');
    }

    public function indexKuota(): string
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Ambil periode aktif saat ini
        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->where('tanggal_tutup >=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

           

        // Jika tidak ada periode aktif, tampilkan periode bulan berjalan
        if (!$periode) {
            $firstDay = date('Y-m-01');
            $lastDay  = date('Y-m-t');
            $periode = (object)[
                'tanggal_buka' => $firstDay,
                'tanggal_tutup' => $lastDay
            ];
        }

        $data['kuota_unit'] = $this->magangModel->getSisaKuota();

        $data['periode'] = $periode;
        // dd($data);
   
        return view('admin/kelola_kuota', $data);
    }

    public function indexSeleksi(): string
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Ambil periode aktif saat ini
        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->where('tanggal_tutup >=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

        // Jika tidak ada periode aktif, tampilkan periode bulan berjalan
        if (!$periode) {
            $firstDay = date('Y-m-01');
            $lastDay  = date('Y-m-t');
            $periode = (object)[
                'tanggal_buka' => $firstDay,
                'tanggal_tutup' => $lastDay
            ];
        }

        $data['kuota_unit'] = $this->magangModel->getSisaKuota();

        $data['periode'] = $periode;
        // dd($data);
   
        return view('admin/kelola_seleksi', $data);
    }

    public function pendaftar()
    {
        $request = \Config\Services::request();
        $unitId = $request->getGet('unit_id');
        $pendidikan = $request->getGet('pendidikan');

        $db = \Config\Database::connect();

        // Ambil pendaftar yang cocok mapping pendidikan (persis seperti getSisaKuota)
        $builder = $db->table('magang')
                ->select('magang.magang_id as magang_id, magang.*, users.*, instansi.nama_instansi, jurusan.nama_jurusan as jurusan')
                ->join('users', 'users.id = magang.user_id', 'left')
                ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
                ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
                ->where('magang.unit_id', $unitId)
                ->where('magang.status_akhir', 'pendaftaran')
                ->where("
                    CASE 
                        WHEN users.tingkat_pendidikan = 'SMA/SMK' THEN 'SMA/SMK'
                        WHEN users.tingkat_pendidikan IN ('D3', 'D4/S1', 'S2') THEN 'Perguruan Tinggi'
                        ELSE users.tingkat_pendidikan
                    END = '$pendidikan'
                ", null, false)
                ->orderBy('magang.tanggal_daftar', 'asc');

        $pendaftar = $builder->get()->getResult();

        // Hitung sisa kuota (pakai getSisaKuota yang sudah ada)
        $allKuota = $this->magangModel->getSisaKuota();
        $sisa = 0;
        foreach ($allKuota as $k) {
            if ($k->unit_id == $unitId && strtolower($k->tingkat_pendidikan) == strtolower($pendidikan)) {
                $sisa = $k->sisa_kuota;
                break;
            }
        }

        return view('admin/modal_pendaftar', [
            'pendaftar' => $pendaftar,
            'kuota_tersedia' => $sisa,
            'error' => null,
        ]);
    }

    public function terimaPendaftar($id = null)
    {
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak ditemukan.']);
        }

        $db = \Config\Database::connect();

        // Ambil data pendaftar + join unit kerja
        $builder = $db->table('magang');
        $builder->select('magang.*, unit_kerja.unit_kerja');
        $builder->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left');
        $pendaftar = $builder->where('magang.magang_id', $id)->get()->getRow();

        if (!$pendaftar) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data pendaftar tidak ditemukan.']);
        }

        // Hitung tanggal mulai dan selesai magang
        $today = new \DateTime();
        $start = new \DateTime($today->format('Y-m-01'));
        $start->modify('+2 month');
        while (in_array($start->format('N'), [6, 7])) {
            $start->modify('+1 day');
        }

        $durasi = (int) $pendaftar->durasi;
        $end = clone $start;
        $end->modify("+$durasi month");

        // Update status dan tanggal
        $db->table('magang')->where('magang_id', $id)->update([
            'status_seleksi'   => 'Diterima',
            'tanggal_seleksi' => date('Y-m-d H:i:s'),
            'tanggal_masuk'   => $start->format('Y-m-d'),
            'tanggal_selesai' => $end->format('Y-m-d'),
            'status_akhir'    => 'proses',
        ]);

        // Ambil data user
        $user = $db->table('users')->where('id', $pendaftar->user_id)->get()->getRow();
        if (!$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data user tidak ditemukan.']);
        }

        $emailPeserta = $user->email;
        $emailInstansi = $user->email_instansi ?? null;

        // ===== Kirim Email =====
        $email = \Config\Services::email();
        $email->setTo($emailPeserta);
        if ($emailInstansi) {
            $email->setCC($emailInstansi);
        }

        $email->setSubject('Penerimaan Magang di PT Semen Padang');
        $email->setMessage(view('emails/penerimaan_magang', [
            'nama'            => $user->fullname ?? $user->username,
            'unit'            => $pendaftar->unit_kerja,
            'tanggal_masuk'   => $start->format('d F Y'),
            'tanggal_selesai' => $end->format('d F Y'),
        ]));

        if ($email->send()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Pendaftaran berhasil diterima dan email dikirim.']);
        } else {
            return $this->response->setJSON(['status' => 'warning', 'message' => 'Diterima, tapi gagal mengirim email.', 'debug' => $email->printDebugger()]);
        }
    }

    public function terimaBanyak()
    {
        $ids = $this->request->getPost('pendaftar_ids');

        if (!$ids || !is_array($ids)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada pendaftar yang dipilih.']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('magang');
        $pdfController = new \App\Controllers\GeneratePDF();
        $email = \Config\Services::email();

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($ids as $id) {
            // Ambil data pendaftar
            $pendaftar = $builder
                ->select('magang.*, unit_kerja.unit_kerja')
                ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
                ->where('magang.magang_id', $id)
                ->get()->getRow();

            if (!$pendaftar) {
                $failCount++;
                $messages[] = "ID $id: Pendaftar tidak ditemukan.";
                continue;
            }

            // Hitung tanggal mulai dan selesai
            $today = new \DateTime();
            $start = new \DateTime($today->format('Y-m-01'));
            $start->modify('+2 month');
            while (in_array($start->format('N'), [6, 7])) {
                $start->modify('+1 day');
            }

            $durasi = (int) $pendaftar->durasi;
            $end = clone $start;
            $end->modify("+$durasi month");

            // Update status & tanggal
            $db->table('magang')->where('magang_id', $id)->update([
                'status_seleksi'   => 'Diterima',
                'tanggal_seleksi' => date('Y-m-d H:i:s'),
                'tanggal_masuk'   => $start->format('Y-m-d'),
                'tanggal_selesai' => $end->format('Y-m-d'),
                'status_akhir'    => 'proses',
            ]);

            // Ambil user
            $user = $db->table('users')->where('id', $pendaftar->user_id)->get()->getRow();
            if (!$user) {
                $failCount++;
                $messages[] = "ID $id: Data user tidak ditemukan.";
                continue;
            }

            $emailPeserta = $user->email;
            $emailInstansi = $user->email_instansi ?? null;

            // Kirim Email
            $email->clear(); // Reset sebelum mengirim email baru
            $email->setTo($emailPeserta);
            if ($emailInstansi) {
                $email->setCC($emailInstansi);
            }

            $email->setSubject('Penerimaan Magang di PT Semen Padang');
            $email->setMailType('html');
            $email->setMessage(view('emails/penerimaan_magang', [
                'nama'            => $user->fullname ?? $user->username,
                'unit'            => $pendaftar->unit_kerja,
                'tanggal_masuk'   => $start->format('d F Y'),
                'tanggal_selesai' => $end->format('d F Y'),
            ]));



            if ($email->send()) {
                $successCount++;
            } else {
                $failCount++;
                $messages[] = "ID $id: Gagal kirim email.";
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "$successCount berhasil diterima. $failCount gagal.",
            'details' => $messages
        ]);
    }

    public function tolakPendaftar($id = null)
    {
        log_message('debug', 'Masuk ke tolakPendaftar() dengan ID: ' . $id);

        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID tidak ditemukan.']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $data = $builder->where('magang_id', $id)->get()->getRow();
        if (!$data) {
            log_message('error', 'Data magang dengan ID ' . $id . ' tidak ditemukan.');
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        $builder->where('magang_id', $id)->update([
            'status_seleksi' => 'Ditolak',
            'tanggal_seleksi' => date('Y-m-d H:i:s'),
            'status_akhir' => 'gagal'
        ]);

        log_message('debug', 'Pendaftaran dengan ID ' . $id . ' berhasil ditolak.');

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Pendaftaran berhasil ditolak.'
        ]);
    }

    public function tolakBanyak()
    {
        $ids = $this->request->getPost('pendaftar_ids');

        if (!$ids || !is_array($ids)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada pendaftar yang dipilih.']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($ids as $id) {
            log_message('debug', 'Memproses tolak ID: ' . $id);

            $data = $builder
                ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username, unit_kerja.unit_kerja')
                ->join('users', 'users.id = magang.user_id', 'left')
                ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
                ->where('magang.magang_id', $id)
                ->get()
                ->getRow();

            if (!$data) {
                log_message('error', "Data magang dengan ID $id tidak ditemukan.");
                $failCount++;
                $messages[] = "ID $id: tidak ditemukan.";
                continue;
            }

            $updated = $builder->where('magang_id', $id)->update([
                'status_seleksi'   => 'Ditolak',
                'tanggal_seleksi' => date('Y-m-d H:i:s'),
                'status_akhir' => 'gagal'
            ]);

            if ($updated) {
                log_message('debug', "ID $id: Berhasil ditolak.");
                $successCount++;

                // ===== Kirim Email Penolakan =====
                $email = \Config\Services::email();
                $email->setTo($data->email);
                if (!empty($data->email_instansi)) {
                    $email->setCC($data->email_instansi);
                }

                $email->setSubject('Hasil Seleksi Pendaftaran Magang di PT Semen Padang');
                $email->setMailType('html');
                $email->setMessage(view('emails/penolakan_magang', [
                    'nama' => $data->fullname ?? 'Saudara',
                    'unit' => $data->unit_kerja ?? 'Unit terkait',
                ]));

                if (!$email->send()) {
                    log_message('error', "Gagal kirim email ke ID $id: " . print_r($email->printDebugger(), true));
                }

            } else {
                log_message('error', "ID $id: Gagal update data.");
                $failCount++;
                $messages[] = "ID $id: gagal ditolak.";
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "$successCount berhasil ditolak. $failCount gagal.",
            'details' => $messages
        ]);
    }


    public function indexBerkas(): string
    {
        
        $data = $this->magangModel->select('magang.*, users.fullname, users.nisn_nim, users.bpjs_kes, users.bpjs_tk, users.buktibpjs_tk')
                                        ->join('users', 'users.id = magang.user_id')
                                        ->where('magang.status_validasi_berkas', 'Y')
                                        ->where('magang.status_berkas_lengkap =', null)
                                        ->orderBy('tanggal_validasi_berkas')
                                        ->findAll();

        return view('admin/kelola_kelengkapan', ['data' => $data]);
    }

    public function validasiBerkas($id)
    {
        $status  = $this->request->getPost('status_validasi');
        $catatan = $this->request->getPost('catatan');

        // Ambil data user dan magang
        $db = \Config\Database::connect();
        $data = $db->table('magang')
            ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username, unit_kerja.unit_kerja')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->where('magang.magang_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        // Siapkan data untuk update
        $updateData = [
            'status_berkas_lengkap'       => $status,
            'tanggal_berkas_lengkap'   => date('Y-m-d H:i:s'),
            'cttn_berkas_lengkap' => $catatan
        ];

        // Penyesuaian jika status valid
        if ($status !== 'N') {
            $updateData['status_akhir'] = 'proses';
        } else {
            $updateData['status_validasi_berkas'] = NULL;
            $updateData['tanggal_validasi_berkas'] = NULL;
        }

        // Lakukan update data
        $this->magangModel->update($id, $updateData);

        // ===================== Kirim Email ===================== //
        $email = \Config\Services::email();
        // Kirim ke dua email: user dan instansi
        $toEmail = $data->email;
        $ccEmail = $data->email_instansi;

        // Pastikan ada email tujuan
        if (!empty($toEmail)) {
            $email->setTo($toEmail);

            if (!empty($ccEmail) && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                $email->setCC($ccEmail);
            }
        }

        $email->setSubject('Hasil Validasi Berkas Magang di PT Semen Padang');
        $email->setMailType('html');

        if ($status === 'N') {
            // Jika berkas tidak valid
            $email->setMessage(view('emails/berkas_tidak_valid', [
                'nama'    => $data->fullname ?? $data->username,
                'unit'    => $data->unit_kerja ?? 'Unit terkait',
                'catatan' => $catatan
            ]));
        } else {
            // Jika berkas valid
            $email->setMessage(view('emails/berkas_valid', [
                'nama' => $data->fullname ?? $data->username,
                'unit' => $data->unit_kerja ?? 'Unit terkait',
            ]));

            // Generate dan lampirkan surat penerimaan
            $generatePDF = new \App\Controllers\GeneratePDF();
            $pdfPath = $generatePDF->suratPenerimaan($id, true);

            if ($pdfPath && file_exists($pdfPath)) {
                $email->attach($pdfPath);
            }
        }

        // Kirim email
        if (!$email->send()) {
            log_message('error', "Gagal kirim email validasi berkas ID $id: " . print_r($email->printDebugger(), true));
        }

        // Hapus file PDF sementara (jika ada dan valid)
        if (!empty($pdfPath) && file_exists($pdfPath)) {
            unlink($pdfPath);
        }

        return redirect()->back()->with('success', 'Validasi berhasil disimpan dan email telah dikirim.');
    }

    public function valid($id)
    {
        $catatan = $this->request->getPost('catatan');

        $db = \Config\Database::connect();
        $data = $db->table('magang')
            ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username, unit_kerja.unit_kerja')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->where('magang.magang_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        // Update data
        $updateData = [
            'status_berkas_lengkap'    => 'Y',
            'tanggal_berkas_lengkap'   => date('Y-m-d H:i:s'),
            'cttn_berkas_lengkap'      => $catatan,
            'status_akhir'             => 'magang'
        ];

        $this->magangModel->update($id, $updateData);

        // Kirim email
        $email = \Config\Services::email();
        $toEmail = $data->email;
        $ccEmail = $data->email_instansi;

        if (!empty($toEmail)) {
            $email->setTo($toEmail);

            if (!empty($ccEmail) && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                $email->setCC($ccEmail);
            }
        }

        $email->setSubject('Hasil Validasi Berkas Magang di PT Semen Padang');
        $email->setMailType('html');

        $email->setMessage(view('emails/berkas_valid', [
            'nama' => $data->fullname ?? $data->username,
            'unit' => $data->unit_kerja ?? 'Unit terkait',
        ]));

        // Generate PDF
        $generatePDF = new \App\Controllers\GeneratePDF();
        $pdfPath = $generatePDF->suratPenerimaan($id, true);

        if ($pdfPath && file_exists($pdfPath)) {
            $email->attach($pdfPath);
        }

        if (!$email->send()) {
            log_message('error', "Gagal kirim email validasi berkas ID $id: " . print_r($email->printDebugger(), true));
        }

        if (!empty($pdfPath) && file_exists($pdfPath)) {
            unlink($pdfPath);
        }

        return redirect()->back()->with('success', 'Validasi berhasil disimpan dan email telah dikirim.');
    }

    public function tidakValid($id)
    {
        $catatan = $this->request->getPost('catatan');

        $db = \Config\Database::connect();
        $data = $db->table('magang')
            ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username, unit_kerja.unit_kerja')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->where('magang.magang_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        // Update data
        $updateData = [
            'status_berkas_lengkap'      => 'N',
            'tanggal_berkas_lengkap'     => date('Y-m-d H:i:s'),
            'cttn_berkas_lengkap'        => $catatan,
            'status_validasi_berkas'     => NULL,
            'tanggal_validasi_berkas'    => NULL
        ];

        $this->magangModel->update($id, $updateData);

        // Kirim email
        $email = \Config\Services::email();
        $toEmail = $data->email;
        $ccEmail = $data->email_instansi;

        if (!empty($toEmail)) {
            $email->setTo($toEmail);

            if (!empty($ccEmail) && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                $email->setCC($ccEmail);
            }
        }

        $email->setSubject('Hasil Validasi Berkas Magang di PT Semen Padang');
        $email->setMailType('html');

        $email->setMessage(view('emails/berkas_tidak_valid', [
            'nama'    => $data->fullname ?? $data->username,
            'unit'    => $data->unit_kerja ?? 'Unit terkait',
            'catatan' => $catatan
        ]));

        if (!$email->send()) {
            log_message('error', "Gagal kirim email validasi berkas ID $id: " . print_r($email->printDebugger(), true));
        }

        return redirect()->back()->with('success', 'Validasi tidak valid berhasil disimpan dan email telah dikirim.');
    }





    public function indexMagang(): string
    {
        
        $data = $this->magangModel->select('magang.*,unit_kerja.unit_kerja, users.*')
                                        ->join('users', 'users.id = magang.user_id')
                                        ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
                                        ->where('magang.status_akhir', 'magang')
                                        ->findAll();

        return view('admin/kelola_magang', ['data' => $data]);
    }





    // Menyetujui pendaftaran
    // public function approve($id)
    // {

    //     $this->pendaftaranModel->update($id, [
    //         'validasi_berkas' => 'Y',
    //         'tgl_validasi_berkas' => date('Y-m-d H:i:s')
    //     ]);

    //     return redirect()->to('/manage-pendaftaran')->with('success', 'Pendaftaran telah disetujui.');
    // }

    // Menolak pendaftaran
    // public function reject($id)
    // {
    //     $alasan = $this->request->getPost('alasan');

    //     if (!$alasan) {
    //         return redirect()->back()->with('error', 'Alasan penolakan harus diisi.');
    //     }

    //     $this->pendaftaranModel->update($id, [
    //         'tanggal_approval' => date('Y-m-d H:i:s'),
    //         'catatan' => $alasan,
    //     ]);

    //     return redirect()->to('/manage-pendaftaran')->with('success', 'Pendaftaran telah ditolak.');
    // }

}
