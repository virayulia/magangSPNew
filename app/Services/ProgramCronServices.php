<?php

namespace App\Services;

class ProgramCronService
{
    private $db;
    private $email;
    private int $unitId = 44;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->email = \Config\Services::email();
    }

    /* =====================================================
     * REMINDER UNIT MASUK (H-3)
     * ===================================================== */
    public function remindUnitMasuk($table, $view, $label, $date)
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
    public function remindPembimbing($table)
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
    public function autoTolakTidakKonfirmasi($table)
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
            $this->db->table($table)->where("{$table}_id", $row->{$table . '_id'})->update([
                'status_seleksi' => 'Ditolak',
                'status_akhir'   => 'gagal',
                'tanggal_seleksi'=> date('Y-m-d H:i:s')
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
    public function reminderLengkapiBerkas($table)
    {
        $data = $this->db->table($table)
            ->select("$table.*, users.email, users.fullname, users.bpjs_tk, users.buktibpjs_tk, unit_kerja.unit_kerja")
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
                    'tanggal_masuk' => $row->tanggal_masuk
                ]
            );
        }

        return ['terkirim' => $sent];
    }

    /* =====================================================
     * EMAIL AKHIR PROGRAM
     * ===================================================== */
    public function emailAkhirProgram(string $table, string $view, string $label)
    {
        $today = date('Y-m-d');

        $data = $this->db->table($table)
            ->select("$table.id, $table.user_id, users.email, users.fullname, unit_kerja.unit_kerja")
            ->join('users', "users.id = $table.user_id", 'left')
            ->join('unit_kerja', "unit_kerja.unit_id = $table.unit_id", 'left')
            ->where("$table.tanggal_selesai", $today)
            ->where("$table.status_akhir", $table)
            ->get()
            ->getResult();

        $sent = 0;

        foreach ($data as $row) {

            $this->sendEmail(
                $row->email,
                'Mohon Mengisi Feedback ' . $label,
                $view,
                [
                    'nama' => $row->fullname,
                    'unit' => $row->unit_kerja,
                ]
            );

            $sent++;
        }

        return [
            'total' => count($data),
            'sent'  => $sent
        ];
    }


    /* =====================================================
     * HELPER
     * ===================================================== */
    private function baseQuery($table)
    {
        return $this->db->table($table)
            ->select("
                $table.*,
                users.fullname,
                instansi.nama_instansi,
                jurusan.nama_jurusan,
                unit_kerja.unit_kerja,
                unit_kerja.email_pimpinan as email_unit
            ")
            ->join('users', "users.id = $table.user_id", 'left')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
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

    /* =====================================================
    * REMINDER UPLOAD LAPORAN & ABSENSI (H+15)
    * ===================================================== */
    public function reminderUploadLaporan(string $table): array
    {
        $today = date('Y-m-d');

        $dataList = $this->db->table($table)
            ->select("
                $table.*,
                users.email,
                users.fullname,
                unit_kerja.unit_kerja
            ")
            ->join('users', "users.id = $table.user_id", 'left')
            ->join('unit_kerja', "unit_kerja.unit_id = $table.unit_id", 'left')
            ->whereIn("$table.status_akhir", [$table, 'lulus'])
            ->where("$table.tanggal_selesai <=", $today)
            ->where("DATE_ADD($table.tanggal_selesai, INTERVAL 15 DAY) >=", $today)
            ->groupStart()
                ->groupStart()
                    ->where("$table.laporan IS NULL")
                    ->where("$table.url_laporan IS NULL")
                ->groupEnd()
                ->orGroupStart()
                    ->where("$table.absensi IS NULL")
                    ->where("$table.url_absensi IS NULL")
                ->groupEnd()
            ->groupEnd()
            ->get()
            ->getResult();

        $success = 0;
        $fail = 0;
        $messages = [];

        foreach ($dataList as $row) {

            // ================= HITUNG SISA HARI =================
            $tglSelesai = new \DateTime($row->tanggal_selesai);
            $deadline   = (clone $tglSelesai)->modify('+15 days');
            $todayDt    = new \DateTime($today);

            if ($todayDt > $deadline) {
                continue; // safety
            }

            $sisaHari = $todayDt->diff($deadline)->days;

            // ================= DOKUMEN BELUM LENGKAP =================
            $dokumen = [];

            if (empty($row->laporan) && empty($row->url_laporan)) {
                $dokumen[] = ($table === 'penelitian')
                    ? 'Form Penelitian'
                    : 'Laporan Magang';
            }

            if (empty($row->absensi) && empty($row->url_absensi)) {
                $dokumen[] = 'Absensi';
            }

            if (empty($dokumen)) {
                continue;
            }

            // ================= EMAIL =================
            $this->email->clear();
            $this->email->setTo($row->email);
            $this->email->setSubject(
                $table === 'penelitian'
                    ? 'Reminder Upload Laporan Penelitian'
                    : 'Reminder Upload Laporan & Absensi Magang'
            );
            $this->email->setMailType('html');

            $view = $table === 'penelitian'
                ? 'emails/reminder_upload_laporan_penelitian'
                : 'emails/reminder_upload_laporan';

            $this->email->setMessage(view($view, [
                'nama'           => $row->fullname ?? 'Saudara',
                'unit'           => $row->unit_kerja ?? '-',
                'tanggalSelesai' => date('d-m-Y', strtotime($row->tanggal_selesai)),
                'sisaHari'       => $sisaHari,
                'dokumen'        => $dokumen,
                'signature'      => getSignature($this->unitId),
            ]));

            if ($this->email->send()) {
                $success++;
            } else {
                $fail++;
                $messages[] = "Gagal kirim ke {$row->email}";
                log_message('error', $this->email->printDebugger());
            }
        }

        return [
            'status'   => 'ok',
            'berhasil' => $success,
            'gagal'    => $fail,
            'pesan'    => $messages,
        ];
    }

}
