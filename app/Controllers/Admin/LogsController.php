<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class LogsController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $bulan = date('m');
        $tahun = date('Y');

        // Internal
        $internal = $db->query("
            SELECT DATE(l.date) tanggal, COUNT(DISTINCT l.user_id) total
            FROM auth_logins l
            JOIN auth_groups_users gu ON gu.user_id = l.user_id
            JOIN auth_groups g ON g.id = gu.group_id
            WHERE g.name IN ('admin','pembimbing','she')
            AND MONTH(l.date) = ?
            AND YEAR(l.date) = ?
            GROUP BY DATE(l.date)
        ", [$bulan, $tahun])->getResultArray();

        // Eksternal
        $eksternal = $db->query("
            SELECT DATE(l.date) tanggal, COUNT(DISTINCT l.user_id) total
            FROM auth_logins l
            JOIN auth_groups_users gu ON gu.user_id = l.user_id
            JOIN auth_groups g ON g.id = gu.group_id
            WHERE g.name = 'user'
            AND MONTH(l.date) = ?
            AND YEAR(l.date) = ?
            GROUP BY DATE(l.date)
        ", [$bulan, $tahun])->getResultArray();

        // Bikin array tanggal 1 bulan penuh
        $days = date('t');
        $labels = [];
        $dataInternal = [];
        $dataEksternal = [];

        for ($i = 1; $i <= $days; $i++) {
            $tgl = sprintf('%s-%02d-%02d', $tahun, $bulan, $i);
            $labels[] = $i;

            $dataInternal[] = $this->findTotal($internal, $tgl);
            $dataEksternal[] = $this->findTotal($eksternal, $tgl);
        }

        return view('admin/access_log', [
            'labels' => $labels,
            'internal' => $dataInternal,
            'eksternal' => $dataEksternal,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);
    }

    public function chartData()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');

        $db = \Config\Database::connect();

        // internal
        $internal = $db->query("
            SELECT DATE(l.date) tanggal, COUNT(DISTINCT l.user_id) total
            FROM auth_logins l
            JOIN auth_groups_users gu ON gu.user_id = l.user_id
            JOIN auth_groups g ON g.id = gu.group_id
            WHERE g.name IN ('admin','pembimbing','she')
            AND MONTH(l.date) = ?
            AND YEAR(l.date) = ?
            GROUP BY DATE(l.date)
        ", [$bulan, $tahun])->getResultArray();

        // eksternal
        $eksternal = $db->query("
            SELECT DATE(l.date) tanggal, COUNT(DISTINCT l.user_id) total
            FROM auth_logins l
            JOIN auth_groups_users gu ON gu.user_id = l.user_id
            JOIN auth_groups g ON g.id = gu.group_id
            WHERE g.name = 'user'
            AND MONTH(l.date) = ?
            AND YEAR(l.date) = ?
            GROUP BY DATE(l.date)
        ", [$bulan, $tahun])->getResultArray();

        $days = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $labels = [];
        $dataInternal = [];
        $dataEksternal = [];

        for ($i = 1; $i <= $days; $i++) {
            $tgl = sprintf('%s-%02d-%02d', $tahun, $bulan, $i);
            $labels[] = $i;
            $dataInternal[] = $this->findTotal($internal, $tgl);
            $dataEksternal[] = $this->findTotal($eksternal, $tgl);
        }

        return $this->response->setJSON([
            'labels' => $labels,
            'internal' => $dataInternal,
            'eksternal' => $dataEksternal
        ]);
    }


    private function findTotal($data, $tanggal)
    {
        foreach ($data as $row) {
            if ($row['tanggal'] === $tanggal) {
                return (int)$row['total'];
            }
        }
        return 0;
    }
}
