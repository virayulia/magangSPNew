<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MagangModel;
use App\Services\ProgramCronService;
use Config\Database;
use Config\Services;

class CronController extends BaseController
{
    protected $db;
    protected $email;
    protected $unitId = 44;

    public function __construct()
    {
        $this->db    = Database::connect();
        $this->email = Services::email();
    }

    /* =====================================================
     * AUTH TOKEN
     * ===================================================== */
    private function auth($token): bool
    {
        return $token === 'semen123';
    }

    /* =====================================================
     * REMINDER UNIT MASUK (H-3)
     * ===================================================== */
    public function remindUnit($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $date = date('Y-m-d', strtotime('+3 days'));

        return $this->response->setJSON([
            'magang'     => $this->remindUnitMasuk('magang', 'emails/reminder_magang', 'Peserta Magang', $date),
            'penelitian' => $this->remindUnitMasuk('penelitian', 'emails/reminder_penelitian', 'Peneliti', $date),
        ]);
    }

    private function remindUnitMasuk($table, $view, $label, $date)
    {
        $data = $this->baseQuery($table)
            ->where("$table.tanggal_masuk", $date)
            ->where("$table.status_akhir", $table)
            ->get()->getResult();

        return $this->sendGroupedUnitEmail(
            $data,
            "Pemberitahuan Penerimaan $label",
            $view
        );
    }

    /* =====================================================
     * REMINDER SET PEMBIMBING
     * ===================================================== */
    public function remindSetPembimbing($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang'     => $this->remindPembimbing('magang'),
            'penelitian' => $this->remindPembimbing('penelitian'),
        ]);
    }

    private function remindPembimbing($table)
    {
        $data = $this->baseQuery($table)
            ->where("$table.status_akhir", $table)
            ->where("$table.pembimbing_id IS NULL", null, false)
            ->get()->getResult();

        return $this->sendGroupedUnitEmail(
            $data,
            "Reminder Penetapan Pembimbing " . ucfirst($table),
            'emails/reminder_pembimbing'
        );
    }

    /* =====================================================
     * AUTO TOLAK TIDAK KONFIRMASI
     * ===================================================== */
    public function autoTolakTidakKonfirmasi($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang'     => $this->autoTolak('magang'),
            'penelitian' => $this->autoTolak('penelitian'),
        ]);
    }

    private function autoTolak($table)
    {
        $expired = date('Y-m-d', strtotime('-3 days'));

        $data = $this->db->table($table)
            ->select("$table.*, users.email, users.fullname, unit_kerja.unit_kerja")
            ->join('users', "users.id = $table.user_id", 'left')
            ->join('unit_kerja', "unit_kerja.unit_id = $table.unit_id", 'left')
            ->where("$table.status_konfirmasi", null)
            ->where("$table.status_seleksi", 'Diterima')
            ->where("$table.tanggal_seleksi <=", $expired)
            ->get()->getResult();

        $ok = 0;

        foreach ($data as $row) {
            $this->db->table($table)
                ->where("{$table}_id", $row->{$table . '_id'})
                ->update([
                    'status_seleksi'  => 'Ditolak',
                    'status_akhir'    => 'gagal',
                    'tanggal_seleksi' => date('Y-m-d H:i:s')
                ]);

            $this->sendEmail(
                $row->email,
                'Konfirmasi Seleksi Dibatalkan',
                'emails/penolakan_tidak_konfirmasi',
                [
                    'nama' => $row->fullname,
                    'unit' => $row->unit_kerja
                ]
            );

            $ok++;
        }

        return ['berhasil' => $ok];
    }

    /* =====================================================
     * REMINDER LENGKAPI BERKAS
     * ===================================================== */
    public function reminderLengkapiBerkas($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang'     => $this->reminderBerkas('magang'),
            'penelitian' => $this->reminderBerkas('penelitian'),
        ]);
    }

    private function reminderBerkas($table)
    {
        $data = $this->db->table($table)
            ->select("$table.*, users.email, users.fullname, unit_kerja.unit_kerja")
            ->join('users', "users.id = $table.user_id", 'left')
            ->join('unit_kerja', "unit_kerja.unit_id = $table.unit_id", 'left')
            ->where("$table.status_akhir", $table)
            ->whereIn("DATEDIFF($table.tanggal_masuk, CURDATE())", [7,6,5,4])
            ->get()->getResult();

        $sent = 0;

        foreach ($data as $row) {
            $sent += $this->sendEmail(
                $row->email,
                'Reminder Lengkapi Berkas',
                'emails/reminder_lengkapi_berkas',
                [
                    'nama' => $row->fullname,
                    'unit' => $row->unit_kerja,
                ]
            );
        }

        return ['terkirim' => $sent];
    }

    /* =====================================================
     * EMAIL AKHIR PROGRAM
     * ===================================================== */
    public function autoKirimEmailAkhirProgram($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang'     => $this->emailAkhirProgram('magang', 'emails/akhir_magang', 'Peserta Magang'),
            'penelitian' => $this->emailAkhirProgram('penelitian', 'emails/akhir_penelitian', 'Penelitian'),
        ]);
    }

    private function emailAkhirProgram($table, $view, $label)
    {
        $today = date('Y-m-d');

        $data = $this->db->table($table)
            ->select("$table.*, users.email, users.fullname, unit_kerja.unit_kerja")
            ->join('users', "users.id = $table.user_id", 'left')
            ->join('unit_kerja', "unit_kerja.unit_id = $table.unit_id", 'left')
            ->where("$table.tanggal_selesai", $today)
            ->where("$table.status_akhir", $table)
            ->get()->getResult();

        $sent = 0;

        foreach ($data as $row) {
            $this->sendEmail(
                $row->email,
                'Mohon Mengisi Feedback ' . $label,
                $view,
                [
                    'nama' => $row->fullname,
                    'unit' => $row->unit_kerja
                ]
            );
            $sent++;
        }

        return ['total' => count($data), 'sent' => $sent];
    }

    /* =====================================================
     * HELPER QUERY & EMAIL
     * ===================================================== */
    private function baseQuery($table)
    {
        return $this->db->table($table)
            ->select("
                $table.*,
                users.fullname,
                unit_kerja.unit_kerja,
                unit_kerja.email_pimpinan as email_unit
            ")
            ->join('users', "users.id = $table.user_id", 'left')
            ->join('unit_kerja', "unit_kerja.unit_id = $table.unit_id", 'left');
    }

    private function sendGroupedUnitEmail($data, $subject, $view)
    {
        $grouped = [];

        foreach ($data as $d) {
            if (!$d->email_unit) continue;
            $grouped[$d->email_unit]['unit'] = $d->unit_kerja;
            $grouped[$d->email_unit]['list'][] = $d;
        }

        $sent = 0;

        foreach ($grouped as $email => $g) {
            $this->email->clear();
            $this->email->setTo($email);
            $this->email->setSubject($subject);
            $this->email->setMailType('html');
            $this->email->setMessage(view($view, [
                'unit' => $g['unit'],
                'list' => $g['list'],
                'signature' => getSignature($this->unitId)
            ]));

            if ($this->email->send()) $sent++;
        }

        return ['terkirim' => $sent];
    }

    private function sendEmail($to, $subject, $view, $data)
    {
        $this->email->clear();
        $this->email->setTo($to);
        $this->email->setSubject($subject);
        $this->email->setMailType('html');
        $this->email->setMessage(view($view, array_merge($data, [
            'signature' => getSignature($this->unitId)
        ])));

        return $this->email->send() ? 1 : 0;
    }
}
