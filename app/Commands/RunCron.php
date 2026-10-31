<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RunCron extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Cron';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'cron:run';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Menjalankan tugas cron job Magang & Penelitian secara handal melalui CLI.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'cron:run [job_name] [date|kemarin]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'job_name' => 'Pilihan tugas: auto-tolak | remind-pembimbing | remind-unit | remind-laporan | email-akhir | remind-berkas | all',
        'date'     => 'Opsional: tanggal target masuk (misal 2026-11-02) atau keyword "kemarin" untuk kompensasi job terlewat',
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        // Hilangkan limit waktu eksekusi untuk CLI
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $jobTarget = strtolower($params[0] ?? 'all');
        $dateParam = $params[1] ?? null;
        $customDate = null;
        if ($dateParam) {
            if (in_array(strtolower($dateParam), ['kemarin', 'yesterday'])) {
                // Kondisi kemarin: H-3 dari kemarin (yesterday + 3 days)
                $customDate = date('Y-m-d', strtotime('-1 day +3 days'));
            } elseif (strtotime($dateParam) !== false) {
                $customDate = date('Y-m-d', strtotime($dateParam));
            }
        }

        $token = 'semen123';
        $cron  = new \App\Controllers\CronController();
        $cron->initController(\Config\Services::request(), \Config\Services::response(), \Config\Services::logger());

        $jobDefinitions = [
            'auto-tolak' => [
                'name'    => 'Auto Tolak Tidak Konfirmasi (SLA 3 Hari)',
                'handler' => fn() => $cron->autoTolakTidakKonfirmasi($token),
            ],
            'remind-pembimbing' => [
                'name'    => 'Reminder Penetapan Pembimbing Unit',
                'handler' => fn() => $cron->remindSetPembimbing($token),
            ],
            'remind-unit' => [
                'name'    => 'Reminder Unit Kerja H-3 Masuk' . ($customDate ? " (Target Masuk: $customDate)" : ""),
                'handler' => fn() => $cron->remindUnit($token, $customDate),
            ],
            'remind-laporan' => [
                'name'    => 'Reminder Upload Laporan & Absensi',
                'handler' => fn() => $cron->reminderUploadLaporan($token),
            ],
            'email-akhir' => [
                'name'    => 'Kirim Email Akhir Program & Feedback',
                'handler' => fn() => $cron->autoKirimEmailAkhirProgram($token),
            ],
            'remind-berkas' => [
                'name'    => 'Reminder Lengkapi Berkas BPJS Ketenagakerjaan',
                'handler' => fn() => $cron->reminderLengkapiBerkas($token),
            ],
        ];

        // Normalisasi alias nama job jika dipanggil dengan format alternatif
        $aliasMap = [
            'autotolaktidakkonfirmasi' => 'auto-tolak',
            'remind-pembimbing'        => 'remind-pembimbing',
            'remindpembimbing'         => 'remind-pembimbing',
            'remind-unit'              => 'remind-unit',
            'remindunit'               => 'remind-unit',
            'remind-upload-laporan'    => 'remind-laporan',
            'remind-laporan'           => 'remind-laporan',
            'reminduploadlaporan'      => 'remind-laporan',
            'autokirimemailakhirmagang'=> 'email-akhir',
            'email-akhir'              => 'email-akhir',
            'reminderlengkapiberkas'   => 'remind-berkas',
            'remind-berkas'            => 'remind-berkas',
        ];

        if (isset($aliasMap[$jobTarget])) {
            $jobTarget = $aliasMap[$jobTarget];
        }

        $now = date('Y-m-d H:i:s');
        CLI::write("==================================================", 'yellow');
        CLI::write("[$now] Memulai Job Cron: " . strtoupper($jobTarget), 'yellow');
        CLI::write("==================================================", 'yellow');

        $jobsToRun = [];
        if ($jobTarget === 'all') {
            $jobsToRun = $jobDefinitions;
        } elseif (isset($jobDefinitions[$jobTarget])) {
            $jobsToRun[$jobTarget] = $jobDefinitions[$jobTarget];
        } else {
            CLI::error("Job '$jobTarget' tidak dikenal.");
            CLI::write("Daftar job yang valid: " . implode(', ', array_keys($jobDefinitions)) . ", all");
            return;
        }

        $totalOk   = 0;
        $totalFail = 0;

        foreach ($jobsToRun as $key => $job) {
            CLI::print("-> Menjalankan [{$job['name']}]... ");
            try {
                $response = $job['handler']();

                // Ambil data jika response berupa JSON Response object
                $outputData = null;
                if ($response instanceof \CodeIgniter\HTTP\ResponseInterface) {
                    $outputData = json_decode($response->getBody(), true);
                }

                $detailText = '';
                if ($outputData) {
                    $detailText = ' => ' . json_encode($outputData);
                }

                CLI::write("[BERHASIL]$detailText", 'green');
                log_message('info', "CRON SUCCESS [{$key}]: " . json_encode($outputData));
                $totalOk++;
            } catch (\Throwable $e) {
                CLI::write("[GAGAL]: " . $e->getMessage(), 'red');
                log_message('error', "CRON ERROR [{$key}]: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                $totalFail++;
            }

            // Jeda aman antar job
            usleep(500000); // 0.5 detik
        }

        $finished = date('Y-m-d H:i:s');
        CLI::write("--------------------------------------------------", 'yellow');
        CLI::write("[$finished] Selesai. Sukses: $totalOk, Gagal: $totalFail", $totalFail > 0 ? 'red' : 'green');
        CLI::write("==================================================", 'yellow');
    }
}
