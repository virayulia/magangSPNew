<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MagangModel;
use App\Services\ProgramCronService;

class CronController extends BaseController
{

    private ProgramCronService $service;

    public function __construct()
    {
        $this->service = new ProgramCronService();
    }

    private function auth($token)
    {
        if ($token !== 'semen123') {
            return false;
        }
        return true;
    }

    public function remindUnit($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        $date = date('Y-m-d', strtotime('+3 days'));

        return $this->response->setJSON([
            'magang' => $this->service->remindUnitMasuk('magang', 'emails/reminder_magang', 'Peserta Magang', $date),
            'penelitian' => $this->service->remindUnitMasuk('penelitian', 'emails/reminder_penelitian', 'Peneliti', $date),
        ]);
    }

    public function remindSetPembimbing($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang' => $this->service->remindPembimbing('magang'),
            'penelitian' => $this->service->remindPembimbing('penelitian'),
        ]);
    }

    public function autoTolakTidakKonfirmasi($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang'      => $this->service->autoTolakTidakKonfirmasi('magang'),
            'penelitian'  => $this->service->autoTolakTidakKonfirmasi('penelitian'),
        ]);
    }

    public function reminderLengkapiBerkas($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang'      => $this->service->reminderLengkapiBerkas('magang'),
            'penelitian'  => $this->service->reminderLengkapiBerkas('penelitian'),
        ]);
    }

    public function autoKirimEmailAkhirProgram($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang'      => $this->service->emailAkhirProgram(
                'magang',
                'emails/akhir_magang',
                'Peserta Magang'
            ),
            'penelitian'  => $this->service->emailAkhirProgram(
                'penelitian',
                'emails/akhir_penelitian',
                'Penelitian'
            ),
        ]);
    }

    public function reminderUploadLaporan($token = null)
    {
        if (!$this->auth($token)) {
            return $this->response->setStatusCode(403)
                ->setJSON(['error' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'magang' => $this->service->reminderUploadLaporan('magang'),
            'penelitian' => $this->service->reminderUploadLaporan('penelitian'),
        ]);
    }


}
