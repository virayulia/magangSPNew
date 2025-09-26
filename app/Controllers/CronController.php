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
    //     $today = date('Y-m-d');

    //     // Ambil pendaftar dengan tanggal masuk 2 hari lagi
    //     $targetDate = date('Y-m-d', strtotime('+2 days'));

    //     $pendaftar = $db->table('magang')
    //         ->select('magang.*, users.fullname, instansi.nama_instansi, unit_kerja.email_pimpinan as email_unit, unit_kerja.unit_kerja')
    //         ->join('users', 'magang.user_id = users.id', 'left')
    //         ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
    //         ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
    //         ->where('magang.tanggal_masuk', $targetDate)
    //         ->where('magang.status_akhir', 'magang')
    //         ->get()->getResult();

    //     $email = \Config\Services::email();
    //     $count = 0;

    //     foreach ($pendaftar as $data) {
    //         if (!$data->email_unit) continue;

    //         $email->clear();
    //         $email->setTo($data->email_unit);
    //         $email->setSubject('Pengingat Penerimaan Peserta Magang');
    //         $email->setMailType('html');

    //         $email->setMessage(view('emails/reminder_magang', [
    //             'nama' => $data->fullname,
    //             'unit' => $data->unit_kerja,
    //             'instansi' => $data->nama_instansi,
    //             'tanggal_masuk' => date('d F Y', strtotime($data->tanggal_masuk)),
    //         ]));

    //         if ($email->send()) $count++;
    //     }

    //     return $this->response->setJSON([
    //         'status' => 'success',
    //         'message' => "$count email pengingat dikirim."
    //     ]);
    // }

    public function remindUnit($token = null)
{
    // Amankan dengan token
    if ($token !== 'semen123') {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
    }

    $db = \Config\Database::connect();
    $targetDate = date('Y-m-d', strtotime('+2 days'));

    // Ambil data mahasiswa yang akan masuk
    $pendaftar = $db->table('magang')
        ->select('magang.*, users.fullname, instansi.nama_instansi, unit_kerja.email_pimpinan as email_unit, unit_kerja.unit_kerja')
        ->join('users', 'magang.user_id = users.id', 'left')
        ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
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
            'instansi' => $p->nama_instansi,
            'tanggal'  => $p->tanggal_masuk
        ];
    }

    $email = \Config\Services::email();
    $count = 0;

    foreach ($grouped as $email_unit => $dataUnit) {
        $email->clear();
        $email->setTo($email_unit);
        $email->setSubject('Pengingat Penerimaan Peserta Magang');
        $email->setMailType('html');

        $email->setMessage(view('emails/reminder_magang', [
            'unit'  => $dataUnit['unit'],
            'list'  => $dataUnit['list'],
        ]));

        if ($email->send()) $count++;
    }

    return $this->response->setJSON([
        'status' => 'success',
        'message' => "$count email pengingat dikirim (per unit)."
    ]);
}


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

                $email->setSubject('Konfirmasi Seleksi Magang Dibatalkan');
                $email->setMailType('html');
                $email->setMessage(view('emails/penolakan_tidak_konfirmasi', [
                    'nama' => $data->fullname ?? 'Saudara',
                    'unit' => $data->unit_kerja ?? 'Unit terkait',
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

            $email->setSubject('Hari Terakhir Magang Anda di PT Semen Padang');
            $email->setMailType('html');
            $email->setMessage(view('emails/akhir_magang', [
                'nama'   => $data->fullname ?? $data->username,
                'unit'   => $data->unit_kerja ?? 'Unit terkait',
                'tanggal_selesai' => $today,
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





}
