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
            $view,
            [
                'tanggal_masuk' => $date
            ]
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
        $today = date('Y-m-d');
        
        $data = $this->baseQuery($table)
            ->where("$table.status_akhir", $table)
            ->where("$table.pembimbing_id IS NULL", null, false)
            ->where("$table.tanggal_masuk <", $today)
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
            ->select("$table.*, users.email, users.fullname, unit_kerja.unit_kerja, users.bpjs_tk, users.buktibpjs_tk")
            ->join('users', "users.id = $table.user_id", 'left')
            ->join('unit_kerja', "unit_kerja.unit_id = $table.unit_id", 'left')
            ->where("$table.status_akhir", $table)
            ->whereIn("DATEDIFF($table.tanggal_masuk, CURDATE())", [7,6,5,4])
            ->groupStart()
                    ->where('users.bpjs_tk IS NULL')
                    ->orWhere('users.buktibpjs_tk IS NULL')
                    ->orWhere('users.bpjs_tk', '')
                    ->orWhere('users.buktibpjs_tk', '')
                ->groupEnd()
            ->get()->getResult();

        $sent = 0;

        foreach ($data as $row) {
            $kosong = [];
            if (empty($data->bpjs_tk)) {
                $kosong[] = 'Kartu BPJS Ketenagakerjaan';
            }
            if (empty($data->buktibpjs_tk)) {
                $kosong[] = 'Bukti Pembayaran/Masa Berlaku BPJS Ketenagakerjaan';
            }
            $sent += $this->sendEmail(
                $row->email,
                'Reminder Lengkapi Berkas',
                'emails/reminder_lengkapi_berkas',
                [
                    'nama' => $row->fullname,
                    'unit' => $row->unit_kerja,
                    'tanggal_masuk' => $row->tanggal_masuk,
                    'dokumenKosong' => $kosong,
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
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang'     => $this->sendEmailAkhir('magang'),
            'penelitian' => $this->sendEmailAkhir('penelitian'),
        ]);
    }

    private function sendEmailAkhir($table)
    {
        $db     = \Config\Database::connect();
        $today  = date('Y-m-d');

        /* ================== KONFIG ================== */
        $config = [
            'magang' => [
                'subject' => 'Mohon Segera Isi Feedback Magang Anda',
                'view'    => 'emails/akhir_magang',
            ],
            'penelitian' => [
                'subject' => 'Mohon Segera Isi Feedback Penelitian Anda',
                'view'    => 'emails/akhir_penelitian',
            ],
        ];

        $cfg = $config[$table];

        /* ================== AMBIL DATA ================== */
        $dataList = $db->table($table)
            ->select("
                {$table}.*,
                users.email,
                users.fullname,
                users.username,
                unit_kerja.unit_kerja
            ")
            ->join('users', 'users.id = '.$table.'.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = '.$table.'.unit_id', 'left')
            ->where("{$table}.tanggal_selesai", $today)
            ->where("{$table}.status_akhir", $table)
            ->get()
            ->getResult();

        /* ================== KIRIM EMAIL ================== */
        $success = 0;
        $fail    = 0;
        $pesan   = [];

        foreach ($dataList as $data) {

            $email = \Config\Services::email();
            $email->setTo($data->email);
            $email->setSubject($cfg['subject']);
            $email->setMailType('html');

            $email->setMessage(view($cfg['view'], [
                'nama'            => $data->fullname ?? $data->username,
                'unit'            => $data->unit_kerja ?? 'Unit terkait',
                'tanggal_selesai' => $today,
                'signature'       => getSignature(44),
            ]));

            if ($email->send()) {
                $success++;
            } else {
                $fail++;
                $pesan[] = "Gagal kirim ke {$data->email}";
                log_message(
                    'error',
                    "Gagal kirim email akhir {$table} ID {$data->{$table.'_id'}}: "
                    . print_r($email->printDebugger(), true)
                );
            }
        }

        return [
            'berhasil' => $success,
            'gagal'    => $fail,
            'pesan'    => $pesan,
        ];
    }

    /* =====================================================
    * REMINDER UPLOAD LAPORAN (MAGANG & PENELITIAN)
    * ===================================================== */
    public function reminderUploadLaporan($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang'     => $this->remindUploadDokumen('magang'),
            'penelitian' => $this->remindUploadDokumen('penelitian'),
        ]);
    }

    private function remindUploadDokumen($table)
    {
        $db     = \Config\Database::connect();
        $today  = date('Y-m-d');

        /* ================== KONFIG ================== */
        $config = [
            'magang' => [
                'subject' => 'Reminder Upload Laporan & Absensi Magang',
                'dokumen' => [
                    [
                        'field' => 'laporan',
                        'url'   => 'url_laporan',
                        'label' => 'Laporan Magang',
                    ],
                    [
                        'field' => 'absensi',
                        'url'   => 'url_absensi',
                        'label' => 'Absensi Magang',
                    ],
                ],
            ],
            'penelitian' => [
                'subject' => 'Reminder Upload Dokumen Penelitian',
                'dokumen' => [
                    [
                        'field' => 'formulir_penelitian',
                        'label' => 'Form Penelitian',
                    ],
                    [
                        'field' => 'absensi',
                        'label' => 'Absensi Penelitian',
                    ],
                ],
            ],
        ];

        $cfg = $config[$table];

        /* ================== QUERY ================== */
        $builder = $db->table($table);
        $builder
            ->select("
                {$table}.*,
                users.email,
                users.fullname,
                unit_kerja.unit_kerja
            ")
            ->join('users', 'users.id = '.$table.'.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = '.$table.'.unit_id', 'left')
            ->whereIn("{$table}.status_akhir", [$table, 'lulus'])
            ->where("{$table}.tanggal_selesai <=", $today)
            ->where("DATE_ADD({$table}.tanggal_selesai, INTERVAL 15 DAY) >=", $today);

        // kondisi dokumen belum lengkap
        $builder->groupStart();
        foreach ($cfg['dokumen'] as $doc) {
            $builder->orGroupStart();
            $builder->where("{$table}.{$doc['field']} IS NULL");

            // khusus magang (punya URL)
            if (isset($doc['url'])) {
                $builder->where("{$table}.{$doc['url']} IS NULL");
            }

            $builder->groupEnd();
        }
        $builder->groupEnd();

        $dataList = $builder->get()->getResult();

        /* ================== KIRIM EMAIL ================== */
        $success = 0;
        $fail    = 0;
        $pesan   = [];

        foreach ($dataList as $data) {

            $deadline = (new \DateTime($data->tanggal_selesai))->modify('+15 days');
            $todayDt  = new \DateTime($today);

            if ($todayDt > $deadline) continue;

            $sisaHari = $todayDt->diff($deadline)->days;

            // cek dokumen belum lengkap
            $belumLengkap = [];
            foreach ($cfg['dokumen'] as $doc) {
                $fieldKosong = empty($data->{$doc['field']});
                $urlKosong   = isset($doc['url']) ? empty($data->{$doc['url']}) : true;

                if ($fieldKosong && $urlKosong) {
                    $belumLengkap[] = $doc['label'];
                }
            }

            if (empty($belumLengkap)) continue;

            $email = \Config\Services::email();
            $email->setTo($data->email);
            $email->setSubject($cfg['subject']);
            $email->setMailType('html');

            $email->setMessage(view('emails/reminder_upload_laporan', [
                'nama'           => $data->fullname ?? 'Saudara',
                'unit'           => $data->unit_kerja ?? '-',
                'tanggalSelesai' => date('d-m-Y', strtotime($data->tanggal_selesai)),
                'sisaHari'       => $sisaHari,
                'dokumen'        => $belumLengkap,
                'signature'      => getSignature(44),
                'kegiatan'       => $table,
            ]));

            if ($email->send()) {
                $success++;
            } else {
                $fail++;
                $pesan[] = "Gagal kirim ke {$data->email}";
                log_message('error', $email->printDebugger());
            }
        }

        return [
            'berhasil' => $success,
            'gagal'    => $fail,
            'pesan'    => $pesan,
        ];
    }



    // public function autoKirimEmailAkhirProgram($token = null)
    // {
    //     if (!$this->auth($token)) {
    //         return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
    //     }

    //     return $this->response->setJSON([
    //         'magang'     => $this->emailAkhirProgram('magang', 'emails/akhir_magang', 'Peserta Magang'),
    //         'penelitian' => $this->emailAkhirProgram('penelitian', 'emails/akhir_penelitian', 'Penelitian'),
    //     ]);
    // }

    // private function emailAkhirProgram($table, $view, $label)
    // {
    //     $today = date('Y-m-d');

    //     $data = $this->db->table($table)
    //         ->select("$table.*, users.email, users.fullname, unit_kerja.unit_kerja")
    //         ->join('users', "users.id = $table.user_id", 'left')
    //         ->join('unit_kerja', "unit_kerja.unit_id = $table.unit_id", 'left')
    //         ->where("$table.tanggal_selesai", $today)
    //         ->where("$table.status_akhir", $table)
    //         ->get()->getResult();

    //     $sent = 0;

    //     foreach ($data as $row) {
    //         $this->sendEmail(
    //             $row->email,
    //             'Mohon Mengisi Feedback ' . $label,
    //             $view,
    //             [
    //                 'nama' => $row->fullname,
    //                 'unit' => $row->unit_kerja
    //             ]
    //         );
    //         $sent++;
    //     }

    //     return ['total' => count($data), 'sent' => $sent];
    // }


    /* =====================================================
     * HELPER QUERY & EMAIL
     * ===================================================== */
    private function baseQuery($table)
    {
        return $this->db->table($table)
            ->select("
                $table.*,
                users.fullname,
                users.email,
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

    private function sendGroupedUnitEmail($data, $subject, $view, $extraData = [])
    {
        $grouped = [];
        $sent = 0;

        // ===============================
        // 1. GROUP DATA BERDASARKAN EMAIL UNIT
        // ===============================
        foreach ($data as $d) {
            if (empty($d->email_unit)) continue;

            if (!isset($grouped[$d->email_unit])) {
                $grouped[$d->email_unit] = [
                    'unit' => $d->unit_kerja,
                    'list' => []
                ];
            }

            $grouped[$d->email_unit]['list'][] = [
                'nama'     => $d->fullname,
                'jurusan'  => $d->nama_jurusan,
                'instansi' => $d->nama_instansi,
                'tanggal'  => $d->tanggal_masuk,
                'selesai'  => $d->tanggal_selesai,
                'durasi'   => $d->durasi
            ];
        }

        // ===============================
        // 2. KIRIM EMAIL PER UNIT (BCC ADMIN)
        // ===============================
        foreach ($grouped as $emailUnit => $g) {

            $this->email->clear();
            $this->email->setTo($emailUnit);

            // ✅ SALINAN EMAIL YANG SAMA KE ADMIN
            $this->email->setBCC('musmardi@sig.id');

            $this->email->setSubject($subject);
            $this->email->setMailType('html');

            $message = view($view, array_merge([
                'unit'      => $g['unit'],
                'list'      => $g['list'],
                'signature' => getSignature($this->unitId)
            ], $extraData));

            $this->email->setMessage($message);

            if ($this->email->send()) {
                $sent++;
            }
        }

        return [
            'terkirim' => $sent
        ];
    }

    
    // private function sendGroupedUnitEmail($data, $subject, $view, $extraData = [])
    // {
    //     $grouped = [];
    //     $sentUnits = [];
    //     $sent = 0;

    //     foreach ($data as $d) {
    //         if (!$d->email_unit) continue;

    //         $grouped[$d->email_unit]['unit'] = $d->unit_kerja;
    //         $grouped[$d->email_unit]['list'][] = [
    //             'nama'     => $d->fullname,
    //             'jurusan'  => $d->nama_jurusan,
    //             'instansi' => $d->nama_instansi,
    //             'tanggal'  => $d->tanggal_masuk,
    //             'selesai'  => $d->tanggal_selesai,
    //             'durasi'   => $d->durasi  
    //         ];
    //     }

    //     $email = \Config\Services::email();

    //     foreach ($grouped as $email => $g) {
    //         $this->email->clear();
    //         $this->email->setTo($email);
    //         $this->email->setSubject($subject);
    //         $this->email->setMailType('html');

    //         $this->email->setMessage(view($view, array_merge([
    //             'unit' => $g['unit'],
    //             'list' => $g['list'],
    //             'signature' => getSignature($this->unitId)
    //         ], $extraData)));

    //         if ($this->email->send()) $sent++;
    //     }

    //     // ✅ CC laporan ke admin (SAMA seperti kode lama)
    //     if (!empty($sentUnits)) {
    //         $email->clear();
    //         $email->setTo('musmardi@sig.id');
    //         $email->setSubject("Laporan $subject");
    //         $email->setMailType('html');

    //         $email->setMessage("
    //             <p>Berikut unit yang sudah dikirim email pemberitahuan magang tanggal <b>{$extraData}</b>:</p>
    //             <ul>" . implode('', array_map(fn($u) => "<li>{$u}</li>", $sentUnits)) . "</ul>
    //             <p>Total: {$sent} unit.</p>
    //         ");

    //         $email->send();
    //     }

    //     return ['terkirim' => $sent];
    // }

    // private function sendGroupedUnitEmail($data, $subject, $view)
    // {
    //     $grouped = [];

    //     foreach ($data as $d) {
    //         if (!$d->email_unit) continue;
    //         $grouped[$d->email_unit]['unit'] = $d->unit_kerja;
    //         $grouped[$d->email_unit]['list'][] = $d;
    //     }

    //     $sent = 0;

    //     foreach ($grouped as $email => $g) {
    //         $this->email->clear();
    //         $this->email->setTo($email);
    //         $this->email->setSubject($subject);
    //         $this->email->setMailType('html');
    //         $this->email->setMessage(view($view, [
    //             'unit' => $g['unit'],
    //             'list' => $g['list'],
    //             'signature' => getSignature($this->unitId)
    //         ]));

    //         if ($this->email->send()) $sent++;
    //     }

    //     return ['terkirim' => $sent];
    // }

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
