<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MagangModel;

class CronController extends BaseController
{

    // public function remindUnit($token = null)
    // {
    //     // Amankan dengan token
    //     if ($token !== 'semen123') {
    //         return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
    //     }

    //     $db = \Config\Database::connect();
    //     $targetDate = date('Y-m-d', strtotime('+2 days'));

    //     // Ambil data mahasiswa yang akan masuk
    //     $pendaftar = $db->table('magang')
    //         ->select('magang.*, users.fullname, instansi.nama_instansi, jurusan.nama_jurusan, unit_kerja.email_pimpinan as email_unit, unit_kerja.unit_kerja')
    //         ->join('users', 'magang.user_id = users.id', 'left')
    //         ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
    //         ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
    //         ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
    //         ->where('magang.tanggal_masuk', $targetDate)
    //         ->where('magang.status_akhir', 'magang')
    //         ->get()->getResult();


    //     // Kelompokkan per unit
    //     $grouped = [];
    //     foreach ($pendaftar as $p) {
    //         if (!$p->email_unit) continue;
    //         $grouped[$p->email_unit]['unit'] = $p->unit_kerja;
    //         $grouped[$p->email_unit]['list'][] = [
    //             'nama'     => $p->fullname,
    //             'jurusan'     => $p->nama_jurusan,
    //             'instansi' => $p->nama_instansi,
    //             'tanggal'  => $p->tanggal_masuk
    //         ];
    //     }

    //     $email = \Config\Services::email();
    //     $count = 0;

    //     foreach ($grouped as $email_unit => $dataUnit) {
    //         $email->clear();
    //         $email->setTo($email_unit);
    //         $email->setSubject('Pemberitahuan Penerimaan Peserta Magang');
    //         $email->setMailType('html');

    //         $email->setMessage(view('emails/reminder_magang', [
    //             'unit'  => $dataUnit['unit'],
    //             'list'  => $dataUnit['list'],
    //         ]));

    //         if ($email->send()) $count++;
    //     }

    //     return $this->response->setJSON([
    //         'status' => 'success',
    //         'message' => "$count email pengingat dikirim (per unit)."
    //     ]);
    // }

    public function remindUnit($token = null)
    {
        // Amankan dengan token
        if ($token !== 'semen123') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $targetDate = date('Y-m-d', strtotime('+3 days'));

        // Ambil data mahasiswa yang akan masuk
        $pendaftar = $db->table('magang')
            ->select('magang.*, users.fullname, instansi.nama_instansi, jurusan.nama_jurusan, unit_kerja.email_pimpinan as email_unit, unit_kerja.unit_kerja')
            ->join('users', 'magang.user_id = users.id', 'left')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->where('magang.tanggal_masuk', $targetDate)
            ->where('magang.status_akhir', 'magang')
            ->get()->getResult();

        // Kelompokkan per unit
        $grouped = [];
        foreach ($pendaftar as $p) {
            if (!$p->email_unit) continue;
            $grouped[$p->email_unit]['unit'] = $p->unit_kerja;
            $grouped[$p->email_unit]['list'][] = [
                'nama'     => $p->fullname,
                'jurusan'  => $p->nama_jurusan,
                'instansi' => $p->nama_instansi,
                'tanggal'  => $p->tanggal_masuk
            ];
        }

        $email = \Config\Services::email();
        $count = 0;
        $sentUnits = []; // simpan unit yg sudah dikirimi email

        foreach ($grouped as $email_unit => $dataUnit) {
            $email->clear();
            $email->setTo($email_unit);
            $unit_id = 44;
            $signature = getSignature($unit_id);
            $email->setSubject('Pemberitahuan Penerimaan Peserta Magang');
            $email->setMailType('html');

            $email->setMessage(view('emails/reminder_magang', [
                'unit'  => $dataUnit['unit'],
                'list'  => $dataUnit['list'],
                'signature' => $signature
            ]));

            if ($email->send()) {
                $count++;
                $sentUnits[] = $dataUnit['unit'] . " <{$email_unit}>";
            }
        }

        // === Tambahan: Kirim CC laporan ke admin ===
        if (!empty($sentUnits)) {
            $email->clear();
            $email->setTo('musmardi@sig.id'); // ganti dengan email admin/penanggung jawab
            $email->setSubject('Laporan Email Pemberitahuan Magang');
            $email->setMailType('html');

            $email->setMessage("
                <p>Berikut unit yang sudah dikirim email pemberitahuan magang tanggal <b>{$targetDate}</b>:</p>
                <ul>" . implode('', array_map(fn($u) => "<li>{$u}</li>", $sentUnits)) . "</ul>
                <p>Total: {$count} unit.</p>
            ");

            $email->send(); // kirim email laporan
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "$count email pengingat dikirim (per unit)."
        ]);
    }

    public function remindPembimbing($token = null)
    {
        // 🔐 Token keamanan
        if ($token !== 'semen123') {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();

        // Target: mahasiswa sudah masuk magang tapi belum ada pembimbing
        $data = $db->table('magang')
            ->select('
                magang.*,
                users.fullname,
                instansi.nama_instansi,
                jurusan.nama_jurusan,
                unit_kerja.unit_kerja,
                unit_kerja.email_pimpinan as email_unit
            ')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->where('magang.status_akhir', 'magang')
            ->where('magang.pembimbing_id IS NULL', null, false)
            ->get()
            ->getResult();

        // Kelompokkan per unit
        $grouped = [];
        foreach ($data as $p) {
            if (!$p->email_unit) continue;

            $grouped[$p->email_unit]['unit'] = $p->unit_kerja;
            $grouped[$p->email_unit]['list'][] = [
                'nama'     => $p->fullname,
                'jurusan'  => $p->nama_jurusan,
                'instansi' => $p->nama_instansi,
                'tanggal'  => $p->tanggal_masuk,
            ];
        }

        $email = \Config\Services::email();
        $sentUnits = [];
        $count = 0;

        foreach ($grouped as $email_unit => $dataUnit) {

            $email->clear();
            $email->setTo($email_unit);
            $email->setSubject('Reminder Penetapan Pembimbing Magang');
            $email->setMailType('html');

            // Kalau mau signature dinamis per unit
            $unit_id = 44;
            $signature = getSignature($unit_id);

            $email->setMessage(view('emails/reminder_pembimbing', [
                'unit'      => $dataUnit['unit'],
                'list'      => $dataUnit['list'],
                'signature' => $signature
            ]));

            if ($email->send()) {
                $count++;
                $sentUnits[] = $dataUnit['unit'] . " <{$email_unit}>";
            }
        }

        // CC laporan ke admin
        if (!empty($sentUnits)) {
            $email->clear();
            $email->setTo('musmardi@sig.id');
            $email->setSubject('Laporan Reminder Pembimbing Magang');
            $email->setMailType('html');

            $email->setMessage("
                <p>Unit yang sudah dikirim reminder pembimbing:</p>
                <ul>" . implode('', array_map(fn($u) => "<li>{$u}</li>", $sentUnits)) . "</ul>
                <p>Total: {$count} unit.</p>
            ");

            $email->send();
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => "{$count} email reminder pembimbing dikirim."
        ]);
    }

    // public function remindPembimbing($token = null)
    // {
    //     // ===============================
    //     // 1. KEAMANAN TOKEN
    //     // ===============================
    //     if ($token !== env('CRON_TOKEN', 'semen123')) {
    //         return $this->response
    //             ->setStatusCode(403)
    //             ->setJSON(['error' => 'Unauthorized']);
    //     }

    //     $db = \Config\Database::connect();

    //     // ===============================
    //     // 2. MODE TESTING
    //     // ===============================
    //     $testMode = $this->request->getGet('test');       
    //     $testUnit = $this->request->getGet('unit_id');  

    //     // cegah kirim massal via browser
    //     if (!$this->request->isCLI() && !$testMode) {
    //         return $this->response
    //             ->setStatusCode(403)
    //             ->setJSON(['error' => 'Cron hanya boleh dijalankan via CLI atau TEST MODE']);
    //     }

    //     // ===============================
    //     // 3. QUERY DATA
    //     // ===============================
    //     $query = $db->table('magang')
    //         ->select('
    //             magang.magang_id,
    //             magang.tanggal_masuk,
    //             magang.unit_id,
    //             users.fullname,
    //             instansi.nama_instansi,
    //             jurusan.nama_jurusan,
    //             unit_kerja.unit_kerja,
    //             unit_kerja.email_pimpinan as email_unit
    //         ')
    //         ->join('users', 'users.id = magang.user_id', 'left')
    //         ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
    //         ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
    //         ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
    //         ->where('magang.status_akhir', 'magang')
    //         ->where('magang.pembimbing_id IS NULL', null, false);

    //     // 🔍 filter khusus saat testing
    //     if ($testMode && $testUnit) {
    //         $query->where('magang.unit_id', $testUnit);
    //     }

    //     $data = $query->get()->getResult();

    //     if (empty($data)) {
    //         return $this->response->setJSON([
    //             'status' => 'info',
    //             'message' => 'Tidak ada data yang perlu dikirim.'
    //         ]);
    //     }

    //     // ===============================
    //     // 4. KELOMPOKKAN PER UNIT
    //     // ===============================
    //     $grouped = [];
    //     foreach ($data as $p) {
    //         if (!$p->email_unit) continue;

    //         $grouped[$p->email_unit]['unit'] = $p->unit_kerja;
    //         $grouped[$p->email_unit]['list'][] = [
    //             'nama'     => $p->fullname,
    //             'jurusan'  => $p->nama_jurusan,
    //             'instansi' => $p->nama_instansi,
    //             'tanggal'  => $p->tanggal_masuk
    //         ];
    //     }

    //     // ===============================
    //     // 5. KIRIM EMAIL
    //     // ===============================
    //     $email = \Config\Services::email();
    //     $sentUnits = [];
    //     $count = 0;

    //     foreach ($grouped as $email_unit => $dataUnit) {

    //         $email->clear();

    //         // 🎯 tujuan email
    //         $toEmail = $testMode
    //             ? env('TEST_EMAIL', 'virayukia1234@gmail.com')
    //             : $email_unit;

    //         $subjectPrefix = $testMode ? '[TEST] ' : '';

    //         // signature (kalau dinamis per unit)
    //         $unit_id = $testUnit ?? 44;
    //         $signature = getSignature($unit_id);

    //         $email->setTo($toEmail);
    //         $email->setSubject($subjectPrefix . 'Reminder Penetapan Pembimbing Magang');
    //         $email->setMailType('html');

    //         $email->setMessage(view('emails/reminder_pembimbing', [
    //             'unit'      => $dataUnit['unit'],
    //             'list'      => $dataUnit['list'],
    //             'signature' => $signature
    //         ]));

    //         if ($email->send()) {
    //             $count++;
    //             $sentUnits[] = $dataUnit['unit'] . " <{$toEmail}>";
    //         }
    //     }

    //     // ===============================
    //     // 6. LAPORAN KE ADMIN
    //     // ===============================
    //     if (!empty($sentUnits)) {
    //         $email->clear();
    //         $email->setTo('virayulia.contact@gmail.com');
    //         $email->setSubject($subjectPrefix . 'Laporan Reminder Pembimbing Magang');
    //         $email->setMailType('html');

    //         $email->setMessage("
    //             <p>Berikut unit yang dikirimi reminder pembimbing:</p>
    //             <ul>" . implode('', array_map(fn($u) => "<li>{$u}</li>", $sentUnits)) . "</ul>
    //             <p>Total: <strong>{$count}</strong> unit.</p>
    //         ");

    //         $email->send();
    //     }

    //     // ===============================
    //     // 7. RESPONSE
    //     // ===============================
    //     return $this->response->setJSON([
    //         'status' => 'success',
    //         'mode'   => $testMode ? 'TEST MODE' : 'PRODUCTION',
    //         'count'  => $count,
    //         'units'  => $sentUnits
    //     ]);
    // }

    public function autoTolakTidakKonfirmasi($token = null)
    {
        // Amankan dengan token
        if ($token !== 'semen123') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $today = date('Y-m-d');
        $expiredDate = date('Y-m-d', strtotime('-3 days'));

        $dataList = $builder
            ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username, unit_kerja.unit_kerja')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->where('magang.status_konfirmasi', NULL)
            ->where('magang.status_seleksi', 'Diterima')
            ->where('magang.tanggal_seleksi <=', $expiredDate)
            ->get()
            ->getResult();

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($dataList as $data) {
            $update = $builder->where('magang_id', $data->magang_id)->update([
                'status_seleksi'   => 'Ditolak',
                'status_akhir'     => 'gagal',
                'tanggal_seleksi'  => date('Y-m-d H:i:s')
            ]);

            if ($update) {
                $successCount++;

                // Kirim email penolakan
                $email = \Config\Services::email();
                $email->setTo($data->email);
                $unit_id = 44;
                $signature = getSignature($unit_id);
                $email->setSubject('Konfirmasi Seleksi Magang Dibatalkan');
                $email->setMailType('html');
                $email->setMessage(view('emails/penolakan_tidak_konfirmasi', [
                    'nama' => $data->fullname ?? 'Saudara',
                    'unit' => $data->unit_kerja ?? 'Unit terkait',
                    'signature' => $signature
                ]));

                if (!$email->send()) {
                    log_message('error', "Gagal kirim email ke {$data->email}: " . print_r($email->printDebugger(), true));
                }

            } else {
                $failCount++;
                $messages[] = "ID {$data->magang_id}: gagal update status.";
            }
        }

        return $this->response->setJSON([
            'status' => 'ok',
            'berhasil' => $successCount,
            'gagal' => $failCount,
            'pesan' => $messages
        ]);
    }

    // public function autoTolakTidakValidasiBerkas($token = null)
    // {
    //     // Amankan dengan token
    //     if ($token !== 'semen123') {
    //         return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
    //     }

    //     $db = \Config\Database::connect();
    //     $builder = $db->table('magang');

    //     $today = date('Y-m-d');
    //     $deadlineDate = date('Y-m-d', strtotime('-7 days'));

    //     $dataList = $builder
    //         ->select('magang.*, users.email, users.email_instansi, users.fullname, unit_kerja.unit_kerja')
    //         ->join('users', 'users.id = magang.user_id', 'left')
    //         ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
    //         ->where('status_konfirmasi', 'Y')
    //         ->where('status_validasi_berkas IS NULL', null, false)
    //         ->where('tanggal_konfirmasi <=', $deadlineDate)
    //         ->get()
    //         ->getResult();

    //     $successCount = 0;
    //     $failCount = 0;
    //     $messages = [];

    //     foreach ($dataList as $data) {
    //         $update = $builder->where('magang_id', $data->magang_id)->update([
    //             'status_validasi_berkas'  => 'N',
    //             'tanggal_validasi_berkas' => date('Y-m-d'),
    //             'status_akhir'            => 'gagal',
    //         ]);

    //         if ($update) {
    //             $successCount++;

    //             // Kirim email pemberitahuan
    //             $email = \Config\Services::email();
    //             $email->setTo($data->email); // Hanya ke user
    //             $email->setSubject('Status Validasi Berkas Magang Dibatalkan');
    //             $email->setMailType('html');
    //             $email->setMessage(view('emails/penolakan_validasi_berkas', [
    //                 'nama' => $data->fullname ?? 'Saudara',
    //                 'unit' => $data->unit_kerja ?? 'Unit Terkait',
    //                 'tanggal_konfirmasi' => date('d F Y', strtotime($data->tanggal_konfirmasi))
    //             ]));

    //             if (!$email->send()) {
    //                 log_message('error', "Gagal kirim email ke {$data->email}: " . print_r($email->printDebugger(), true));
    //             }


    //         } else {
    //             $failCount++;
    //             $messages[] = "ID {$data->magang_id}: gagal update status validasi berkas.";
    //         }
    //     }

    //     return $this->response->setJSON([
    //         'status' => 'ok',
    //         'berhasil' => $successCount,
    //         'gagal' => $failCount,
    //         'pesan' => $messages
    //     ]);
    // }

    public function reminderLengkapiBerkas($token = null)
    {
        // Token untuk keamanan
        if ($token !== 'semen123') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $today = date('Y-m-d');

        // Ambil data yang tanggal masuknya H-7 sampai H-4 dan belum lengkap dokumen
        $dataList = $builder
                ->select('magang.*, users.email, users.fullname, unit_kerja.unit_kerja, users.bpjs_tk, users.buktibpjs_tk')
                ->join('users', 'users.id = magang.user_id', 'left')
                ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
                ->where('magang.status_akhir', 'magang')
                ->whereIn('DATEDIFF(magang.tanggal_masuk, CURDATE())', [7, 6, 5, 4])
                ->groupStart() // buka group kondisi dokumen
                    ->where('users.bpjs_tk IS NULL')
                    ->orWhere('users.buktibpjs_tk IS NULL')
                    ->orWhere('users.bpjs_tk', '')
                    ->orWhere('users.buktibpjs_tk', '')
                ->groupEnd()
                ->get()
                ->getResult();

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($dataList as $data) {
            $email = \Config\Services::email();
            $email->setTo($data->email);
            $unit_id = 44;
            $signature = getSignature($unit_id);
            $email->setSubject('Reminder: Segera Lengkapi Berkas Magang Anda');
            $email->setMailType('html');

            $kosong = [];
            if (empty($data->bpjs_tk)) {
                $kosong[] = 'Kartu BPJS Ketenagakerjaan';
            }
            if (empty($data->buktibpjs_tk)) {
                $kosong[] = 'Bukti Pembayaran/Masa Berlaku BPJS Ketenagakerjaan';
            }

            $email->setMessage(view('emails/reminder_lengkapi_berkas', [
                'nama' => $data->fullname ?? 'Saudara',
                'unit' => $data->unit_kerja ?? 'Unit terkait',
                'tanggal_masuk' => date('d-m-Y', strtotime($data->tanggal_masuk)),
                'dokumenKosong' => $kosong,
                'signature' => $signature
            ]));

            if ($email->send()) {
                $successCount++;
            } else {
                $failCount++;
                $messages[] = "Gagal kirim email ke {$data->email}: " . print_r($email->printDebugger(), true);
                log_message('error', "Reminder gagal ke {$data->email}");
            }
        }

        return $this->response->setJSON([
            'status' => 'ok',
            'berhasil' => $successCount,
            'gagal' => $failCount,
            'pesan' => $messages
        ]);
    }

    public function autoKirimEmailAkhirMagang($token = null)
    {
        // Amankan dengan token
        if ($token !== 'semen123') {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $today = date('Y-m-d');

        // Cari peserta magang yang berakhir hari ini
        $dataList = $builder
            ->select('magang.*, users.email, users.fullname, users.username, unit_kerja.unit_kerja')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->where('magang.tanggal_selesai', $today)
            ->where('magang.status_akhir', 'magang') // masih status aktif magang
            ->get()
            ->getResult();

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($dataList as $data) {
            $email = \Config\Services::email();
            $email->setTo($data->email);
            $unit_id = 44;
            $signature = getSignature($unit_id);
            $email->setSubject('Mohon Segera Isi Feedback Magang Anda');
            $email->setMailType('html');
            $email->setMessage(view('emails/akhir_magang', [
                'nama'   => $data->fullname ?? $data->username,
                'unit'   => $data->unit_kerja ?? 'Unit terkait',
                'tanggal_selesai' => $today,
                'signature' => $signature
            ]));

            if ($email->send()) {
                $successCount++;
            } else {
                $failCount++;
                $messages[] = "Gagal kirim email ke {$data->email}";
                log_message('error', "Gagal kirim email akhir magang ID {$data->magang_id}: " . print_r($email->printDebugger(), true));
            }
        }

        return $this->response->setJSON([
            'status'   => 'ok',
            'berhasil' => $successCount,
            'gagal'    => $failCount,
            'pesan'    => $messages
        ]);
    }

    public function reminderUploadLaporan($token = null)
    {
        // ================== KEAMANAN ==================
        if ($token !== 'semen123') {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $today = date('Y-m-d');

        // ================== AMBIL DATA ==================
        $dataList = $builder
            ->select('
                magang.*,
                users.email,
                users.fullname,
                unit_kerja.unit_kerja
            ')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->whereIn('magang.status_akhir', ['magang', 'lulus'])
            ->where('magang.tanggal_selesai <=', $today)
            ->where("DATE_ADD(magang.tanggal_selesai, INTERVAL 15 DAY) >=", $today)
            ->groupStart()
                ->groupStart()
                    ->where('magang.laporan IS NULL')
                    ->where('magang.url_laporan IS NULL')
                ->groupEnd()
                ->orGroupStart()
                    ->where('magang.absensi IS NULL')
                    ->where('magang.url_absensi IS NULL')
                ->groupEnd()
            ->groupEnd()
            ->get()
            ->getResult();

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        // ================== LOOP EMAIL ==================
        foreach ($dataList as $data) {

            // Hitung sisa hari upload
            $tglSelesai = new \DateTime($data->tanggal_selesai);
            $deadline   = (clone $tglSelesai)->modify('+15 days');
            $todayDt    = new \DateTime($today);

            $sisaHari = $todayDt->diff($deadline)->days;
            if ($todayDt > $deadline) {
                continue; // safety
            }

            // Dokumen yang belum lengkap
            $belumLengkap = [];

            if (empty($data->laporan) && empty($data->url_laporan)) {
                $belumLengkap[] = 'Laporan Magang';
            }

            if (empty($data->absensi) && empty($data->url_absensi)) {
                $belumLengkap[] = 'Absensi Magang';
            }

            // ================== EMAIL ==================
            $email = \Config\Services::email();
            $email->setTo($data->email);
            $email->setSubject('Reminder Upload Laporan & Absensi Magang');
            $email->setMailType('html');

            $signature = getSignature(44);

            $email->setMessage(view('emails/reminder_upload_laporan', [
                'nama'        => $data->fullname ?? 'Saudara',
                'unit'        => $data->unit_kerja ?? '-',
                'tanggalSelesai' => date('d-m-Y', strtotime($data->tanggal_selesai)),
                'sisaHari'    => $sisaHari,
                'dokumen'     => $belumLengkap,
                'signature'   => $signature
            ]));

            if ($email->send()) {
                $successCount++;
            } else {
                $failCount++;
                $messages[] = "Gagal kirim ke {$data->email}";
                log_message('error', $email->printDebugger());
            }
        }

        return $this->response->setJSON([
            'status'   => 'ok',
            'berhasil' => $successCount,
            'gagal'    => $failCount,
            'pesan'    => $messages
        ]);
    }






}
