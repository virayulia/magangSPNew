<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseController
{
    protected $magangModel;
    protected $unitKerjaModel;
    protected $kuotaUnitModel;
    protected $db;

    public function __construct()
    {
        $this->magangModel = model('MagangModel');
        $this->unitKerjaModel = model('UnitKerjaModel');
        $this->kuotaUnitModel = model('KuotaUnitModel');
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $today = date('Y-m-d');
        $units = $this->unitKerjaModel->findAll();
        

        /* =====================================================
        *  CHART 1 : HISTOGRAM PER UNIT
        * ===================================================== */
        $labelsUnit = [];

        $aktif_pt = [];
        $aktif_smk = [];
        $akan_pt = [];
        $akan_smk = [];
        $proses_pt = [];
        $proses_smk = [];
        $belum_pt = [];
        $belum_smk = [];

        $kuota_pt = [];
        $kuota_smk = [];

        foreach ($units as $u) {
            $unitId = $u['unit_id'];
            $labelsUnit[] = $u['unit_kerja'];

            $aktif_pt[] = $this->countMagang($unitId, [
                'magang.tanggal_masuk <=' => $today,
                'magang.tanggal_selesai >=' => $today,
                'magang.status_akhir' => 'magang',
                'users.tingkat_pendidikan <>' => 'SMK'
            ]);

            $aktif_smk[] = $this->countMagang($unitId, [
                'magang.tanggal_masuk <=' => $today,
                'magang.tanggal_selesai >=' => $today,
                'magang.status_akhir' => 'magang',
                'users.tingkat_pendidikan' => 'SMK'
            ]);

            $akan_pt[] = $this->countMagang($unitId, [
                'magang.tanggal_masuk >' => $today,
                'magang.status_akhir' => 'magang',
                'users.tingkat_pendidikan <>' => 'SMK'
            ]);

            $akan_smk[] = $this->countMagang($unitId, [
                'magang.tanggal_masuk >' => $today,
                'magang.status_akhir' => 'magang',
                'users.tingkat_pendidikan' => 'SMK'
            ]);

            $proses_pt[] = $this->countMagang($unitId, [
                'magang.status_akhir' => 'proses',
                'users.tingkat_pendidikan <>' => 'SMK'
            ]);

            $proses_smk[] = $this->countMagang($unitId, [
                'magang.status_akhir' => 'proses',
                'users.tingkat_pendidikan' => 'SMK'
            ]);

            $belum_pt[] = $this->countMagang($unitId, [
                'magang.tanggal_selesai <' => $today,
                'magang.status_akhir' => 'magang',
                'users.tingkat_pendidikan <>' => 'SMK'
            ]);

            $belum_smk[] = $this->countMagang($unitId, [
                'magang.tanggal_selesai <' => $today,
                'magang.status_akhir' => 'magang',
                'users.tingkat_pendidikan' => 'SMK'
            ]);

            $qPt = $this->db->table('kuota_unit')
                ->select('kuota')
                ->where('unit_id', $unitId)
                ->where('tingkat_pendidikan !=', 'SMK')
                ->get()
                ->getRow();

            $qSmk = $this->db->table('kuota_unit')
                ->select('kuota')
                ->where('unit_id', $unitId)
                ->where('tingkat_pendidikan', 'SMK')
                ->get()
                ->getRow();

            $kuota_pt[] = $qPt ? (int)$qPt->kuota : 0;
            $kuota_smk[] = $qSmk ? (int)$qSmk->kuota : 0;
        }

        /* =====================================================
        *  CHART 2 : MASUK & KELUAR PER BULAN (DINAMIS)
        * ===================================================== */
        $periode = $this->request->getGet('periode') ?? 6;

        $allDates = $this->magangModel
            ->select('tanggal_masuk, tanggal_selesai')
            ->whereIn('status_akhir', ['proses', 'magang', 'lulus'])
            ->findAll();

        $lastPeriod = null;

        foreach ($allDates as $r) {
            if (!empty($r['tanggal_masuk'])) {
                $temp = date('Y-m-01', strtotime($r['tanggal_masuk']));
                if (!$lastPeriod || $temp > $lastPeriod) $lastPeriod = $temp;
            }
            if (!empty($r['tanggal_selesai'])) {
                $temp = date('Y-m-01', strtotime($r['tanggal_selesai']));
                if (!$lastPeriod || $temp > $lastPeriod) $lastPeriod = $temp;
            }
        }

        if ($lastPeriod == null) {
            $lastPeriod = date('Y-m-01');
        }

        $labelsPeriode = [];
        $masuk = [];
        $keluar = [];

        for ($i = $periode - 1; $i >= 0; $i--) {
            $bulan = date('Y-m', strtotime("$lastPeriod -$i months"));
            $labelsPeriode[] = date('M Y', strtotime($bulan));
            $masuk[$bulan] = 0;
            $keluar[$bulan] = 0;
        }

        foreach ($allDates as $r) {
            if (!empty($r['tanggal_masuk'])) {
                $b = date('Y-m', strtotime($r['tanggal_masuk']));
                if (isset($masuk[$b])) $masuk[$b]++;
            }
            if (!empty($r['tanggal_selesai'])) {
                $b = date('Y-m', strtotime($r['tanggal_selesai']));
                if (isset($keluar[$b])) $keluar[$b]++;
            }
        }


        /* =====================================================
        *  CHART 3 : JUMLAH AKTIF PER BULAN
        * ===================================================== */

        $recordsAll = $this->magangModel
            ->select('tanggal_masuk, tanggal_selesai')
            ->whereIn('status_akhir', ['proses', 'magang', 'lulus'])
            ->findAll();

        $aktifBulanan = [];
        foreach ($labelsPeriode as $lp) {
            $aktifBulanan[] = 0; 
        }

        foreach ($recordsAll as $rec) {
            if (empty($rec['tanggal_masuk']) || empty($rec['tanggal_selesai'])) continue;

            $start = strtotime($rec['tanggal_masuk']);
            $end   = strtotime($rec['tanggal_selesai']);

            foreach ($labelsPeriode as $i => $labelText) {
                $bulan = date('Y-m-01', strtotime($labelText));
                $awalBulan  = strtotime($bulan);
                $akhirBulan = strtotime(date('Y-m-t', strtotime($bulan)));
                if ($start <= $akhirBulan && $end >= $awalBulan) {
                    $aktifBulanan[$i]++;
                }
            }
        }

        /* =====================================================
        *  CHART 4 : KUOTA & AKTIF SMK / PT PER BULAN
        * ===================================================== */

        $mapTingkat = [
            'SMK' => ['SMK'],
            'Perguruan Tinggi' => ['D3', 'D4/S1', 'S1', 'S2']
        ];

        $kuotaAll = $this->magangModel->getSisaKuota(); 

        $sum = [
            'Perguruan Tinggi' => ['kuota' => 0],
            'SMK' => ['kuota' => 0]
        ];

        foreach ($kuotaAll as $row) {
            $t = $row->tingkat_pendidikan;
            $sum[$t]['kuota'] += (int)$row->kuota;
        }

        $totalKuotaAll = $sum['SMK']['kuota'] + $sum['Perguruan Tinggi']['kuota'];

        $aktifPT = array_fill(0, count($labelsPeriode), 0);
        $aktifSMK = array_fill(0, count($labelsPeriode), 0);

        $recordsTingkat = $this->magangModel
            ->join('users', 'users.id = magang.user_id')
            ->select('magang.tanggal_masuk, magang.tanggal_selesai, users.tingkat_pendidikan')
            ->whereIn('status_akhir', ['proses', 'magang', 'lulus'])
            ->findAll();

        foreach ($recordsTingkat as $rec) {

            if (empty($rec['tanggal_masuk']) || empty($rec['tanggal_selesai'])) continue;

            $start = strtotime($rec['tanggal_masuk']);
            $end   = strtotime($rec['tanggal_selesai']);

            foreach ($labelsPeriode as $i => $labelText) {

                $bulan = date('Y-m-01', strtotime($labelText));
                $awalBulan  = strtotime($bulan);
                $akhirBulan = strtotime(date('Y-m-t', strtotime($bulan)));

                if ($start <= $akhirBulan && $end >= $awalBulan) {

                    if ($rec['tingkat_pendidikan'] == 'SMK') {
                        $aktifSMK[$i]++;
                    } else {
                        $aktifPT[$i]++;
                    }
                }
            }
        }
        
        // Total pemagang aktif (status proses + magang)
        $totalPemagang = $this->magangModel
            ->whereIn('status_akhir', ['proses', 'magang'])
            ->countAllResults();

        // Total yang sudah lulus
        $totalLulus = $this->magangModel
            ->where('status_akhir', 'lulus')
            ->countAllResults();

        $totalTerisi = array_sum($aktif_pt)
                        + array_sum($akan_pt)
                        + array_sum($proses_pt)
                        + array_sum($aktif_smk)
                        + array_sum($akan_smk)
                        + array_sum($proses_smk);

        // ===============================
        // TOTAL MAGANG AKTIF BULAN INI
        // ===============================
        $thisMonthStart = date('Y-m-01');
        $thisMonthEnd   = date('Y-m-t');

        $totalAktifBulanIni = $this->magangModel
            ->whereIn('status_akhir', ['magang', 'proses'])
            ->where('tanggal_masuk <=', $thisMonthEnd)
            ->where('tanggal_selesai >=', $thisMonthStart)
            ->countAllResults();

        // ===============================
        // TOTAL PENDAFTAR BULAN INI
        // ===============================
        $bulanIni = date('m');
        $tahunIni = date('Y');

        $totalPendaftarBulanIni = $this->magangModel
            ->where('status_akhir', 'pendaftaran')
            ->countAllResults();

        // =============================================
        // PIE KUOTA TERISI VS TOTAL KUOTA
        // =============================================
        $pieKuota = [
            'terisi' => $totalTerisi,
            'sisa'   => max($totalKuotaAll - $totalTerisi, 0)
        ];

        // =============================================
        // PIE DURASI MAGANG 1–6 BULAN
        // =============================================
        $durasiRecords = $this->magangModel
            ->select('durasi')
            ->whereIn('status_akhir', ['proses', 'magang', 'lulus'])
            ->findAll();

        $durasiCount = [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0];

        foreach ($durasiRecords as $d) {
            $dr = (int)$d['durasi'];
            if ($dr >= 1 && $dr <= 6) {
                $durasiCount[$dr]++;
            }
        }

        // =============================================
        // PIE GENDER L / P
        // =============================================
        $gender = $this->magangModel
            ->join('users', 'users.id = magang.user_id')
            ->select('users.jenis_kelamin')
            ->whereIn('magang.status_akhir', ['proses', 'magang', 'lulus'])
            ->findAll();

        $genderCount = [
            'L' => 0,
            'P' => 0
        ];

        foreach ($gender as $g) {
            $jk = strtoupper($g['jenis_kelamin']);
            if ($jk == 'L' || $jk == 'LAKI-LAKI') $genderCount['L']++;
            if ($jk == 'P' || $jk == 'PEREMPUAN') $genderCount['P']++;
        }



        /* =====================================================
        *  RETURN VIEW
        * ===================================================== */
        return view('admin/dashboard', [
            'chart' => [
                'labels' => $labelsUnit,
                // PT
                'aktif_pt' => $aktif_pt,
                'akan_pt'  => $akan_pt,
                'proses_pt'=> $proses_pt,
                'belum_pt' => $belum_pt,
                'kuota_pt' => $kuota_pt,
                // SMK
                'aktif_smk' => $aktif_smk,
                'akan_smk'  => $akan_smk,
                'proses_smk'=> $proses_smk,
                'belum_smk' => $belum_smk,
                'kuota_smk' => $kuota_smk,
            ],

            'chartMasukKeluar' => [
                'labels' => $labelsPeriode,
                'masuk'  => array_values($masuk),
                'keluar' => array_values($keluar),
            ],

            'chartAktifPerBulan' => [
                'labels'        => $labelsPeriode,
                'aktif_total'   => $aktifBulanan,
                'aktif_smk'     => $aktifSMK,
                'aktif_pt'      => $aktifPT,
                'kuota_total'   => $totalKuotaAll,
                'kuota_smk'     => $sum['SMK']['kuota'],
                'kuota_pt'      => $sum['Perguruan Tinggi']['kuota'],
            ],
            'pieKuota'     => $pieKuota,
            'durasiCount'  => $durasiCount,
            'genderCount'  => $genderCount,
            'totalAktif' => $totalAktifBulanIni,
            'totalPendaftar' =>$totalPendaftarBulanIni,
            'totalLulus'   => $totalLulus,
            'totalPemagang'  => $totalPemagang,
            'periode' => $periode,
        ]);
    }

    private function countMagang($unitId, $filters = [])
    {
        $builder = $this->magangModel
            ->join('users', 'users.id = magang.user_id')
            ->where('magang.unit_id', $unitId);

        foreach ($filters as $key => $value) {
            $builder->where($key, $value);
        }

        return $builder->countAllResults();
    }


    // public function index()
    // {
    //     $today = date('Y-m-d');
    //     $units = $this->unitKerjaModel->findAll();

    //     /* =====================================================
    //     *  CHART 1 : HISTOGRAM PER UNIT
    //     * ===================================================== */
    //     $labelsUnit = [];
    //     $aktif = [];
    //     $belumLulus = [];
    //     $prosesDaftar = [];
    //     $akanMagang = [];

    //     foreach ($units as $u) {
    //         $unitId = $u['unit_id'];
    //         $labelsUnit[] = $u['unit_kerja'];

    //         $aktif[] = $this->magangModel
    //             ->where('unit_id', $unitId)
    //             ->where('tanggal_masuk <=', $today)
    //             ->where('tanggal_selesai >=', $today)
    //             ->where('status_akhir', 'magang')
    //             ->countAllResults();

    //         $akanMagang[] = $this->magangModel
    //             ->where('unit_id', $unitId)
    //             ->where('tanggal_masuk >', $today)
    //             ->where('status_akhir', 'magang')
    //             ->countAllResults();

    //         $prosesDaftar[] = $this->magangModel
    //             ->where('unit_id', $unitId)
    //             ->where('status_akhir', 'proses')
    //             ->countAllResults();

    //         $belumLulus[] = $this->magangModel
    //             ->where('unit_id', $unitId)
    //             ->where('tanggal_selesai <', $today)
    //             ->where('status_akhir', 'magang')
    //             ->countAllResults();
    //     }

    //     /* =====================================================
    //     *  CHART 2 : MASUK & KELUAR PER BULAN (DINAMIS)
    //     * ===================================================== */
    //     $periode = $this->request->getGet('periode') ?? 6;

    //     $allDates = $this->magangModel
    //         ->select('tanggal_masuk, tanggal_selesai')
    //         ->whereIn('status_akhir', ['proses', 'magang', 'lulus'])
    //         ->findAll();

    //     $lastPeriod = null;

    //     foreach ($allDates as $r) {
    //         if (!empty($r['tanggal_masuk'])) {
    //             $temp = date('Y-m-01', strtotime($r['tanggal_masuk']));
    //             if (!$lastPeriod || $temp > $lastPeriod) $lastPeriod = $temp;
    //         }
    //         if (!empty($r['tanggal_selesai'])) {
    //             $temp = date('Y-m-01', strtotime($r['tanggal_selesai']));
    //             if (!$lastPeriod || $temp > $lastPeriod) $lastPeriod = $temp;
    //         }
    //     }

    //     if ($lastPeriod == null) {
    //         $lastPeriod = date('Y-m-01');
    //     }

    //     $labelsPeriode = [];
    //     $masuk = [];
    //     $keluar = [];

    //     for ($i = $periode - 1; $i >= 0; $i--) {
    //         $bulan = date('Y-m', strtotime("$lastPeriod -$i months"));
    //         $labelsPeriode[] = date('M Y', strtotime($bulan));
    //         $masuk[$bulan] = 0;
    //         $keluar[$bulan] = 0;
    //     }

    //     foreach ($allDates as $r) {
    //         if (!empty($r['tanggal_masuk'])) {
    //             $b = date('Y-m', strtotime($r['tanggal_masuk']));
    //             if (isset($masuk[$b])) $masuk[$b]++;
    //         }
    //         if (!empty($r['tanggal_selesai'])) {
    //             $b = date('Y-m', strtotime($r['tanggal_selesai']));
    //             if (isset($keluar[$b])) $keluar[$b]++;
    //         }
    //     }


    //     /* =====================================================
    //     *  CHART 3 : JUMLAH AKTIF PER BULAN
    //     * ===================================================== */

    //     // Ambil semua data magang untuk cek rentang aktif
    //     $recordsAll = $this->magangModel
    //         ->select('tanggal_masuk, tanggal_selesai')
    //         ->whereIn('status_akhir', ['proses', 'magang', 'lulus'])
    //         ->findAll();

    //     $aktifBulanan = [];
    //     foreach ($labelsPeriode as $lp) {
    //         $aktifBulanan[] = 0; 
    //     }

    //     foreach ($recordsAll as $rec) {
    //         if (empty($rec['tanggal_masuk']) || empty($rec['tanggal_selesai'])) continue;

    //         $start = strtotime($rec['tanggal_masuk']);
    //         $end   = strtotime($rec['tanggal_selesai']);

    //         foreach ($labelsPeriode as $i => $labelText) {

    //             // Konversi "M Y" → "Y-m-01"
    //             $bulan = date('Y-m-01', strtotime($labelText));
    //             $awalBulan  = strtotime($bulan);
    //             $akhirBulan = strtotime(date('Y-m-t', strtotime($bulan)));

    //             // Jika peserta aktif di bulan ini
    //             if ($start <= $akhirBulan && $end >= $awalBulan) {
    //                 $aktifBulanan[$i]++;
    //             }
    //         }
    //     }

    //     /* =====================================================
    //     *  CHART 4 : KUOTA & AKTIF SMK / PT PER BULAN
    //     * ===================================================== */

    //     $mapTingkat = [
    //         'SMK' => ['SMK'],
    //         'Perguruan Tinggi' => ['D3', 'D4/S1', 'S1', 'S2']
    //     ];

    //     $kuotaAll = $this->magangModel->getSisaKuota(); 

    //     $sum = [
    //         'Perguruan Tinggi' => ['kuota' => 0],
    //         'SMK' => ['kuota' => 0]
    //     ];

    //     foreach ($kuotaAll as $row) {
    //         $t = $row->tingkat_pendidikan;
    //         $sum[$t]['kuota'] += (int)$row->kuota;
    //     }

    //     $totalKuotaAll = $sum['SMK']['kuota'] + $sum['Perguruan Tinggi']['kuota'];


    //     // --- Hitung aktif per bulan berdasarkan tingkat --- //
    //     $aktifPT = array_fill(0, count($labelsPeriode), 0);
    //     $aktifSMK = array_fill(0, count($labelsPeriode), 0);

    //     $recordsTingkat = $this->magangModel
    //         ->join('users', 'users.id = magang.user_id')
    //         ->select('magang.tanggal_masuk, magang.tanggal_selesai, users.tingkat_pendidikan')
    //         ->whereIn('status_akhir', ['proses', 'magang', 'lulus'])
    //         ->findAll();

    //     foreach ($recordsTingkat as $rec) {

    //         if (empty($rec['tanggal_masuk']) || empty($rec['tanggal_selesai'])) continue;

    //         $start = strtotime($rec['tanggal_masuk']);
    //         $end   = strtotime($rec['tanggal_selesai']);

    //         foreach ($labelsPeriode as $i => $labelText) {

    //             $bulan = date('Y-m-01', strtotime($labelText));
    //             $awalBulan  = strtotime($bulan);
    //             $akhirBulan = strtotime(date('Y-m-t', strtotime($bulan)));

    //             if ($start <= $akhirBulan && $end >= $awalBulan) {

    //                 if ($rec['tingkat_pendidikan'] == 'SMK') {
    //                     $aktifSMK[$i]++;
    //                 } else {
    //                     $aktifPT[$i]++;
    //                 }
    //             }
    //         }
    //     }
        


    //     /* =====================================================
    //     *  RETURN VIEW
    //     * ===================================================== */
    //     return view('admin/dashboard', [
    //         'chart' => [
    //             'labels' => $labelsUnit,
    //             'aktif' => $aktif,
    //             'belumLulus' => $belumLulus,
    //             'prosesDaftar' => $prosesDaftar,
    //             'akanMagang' => $akanMagang,
    //         ],

    //         'chartMasukKeluar' => [
    //             'labels' => $labelsPeriode,
    //             'masuk'  => array_values($masuk),
    //             'keluar' => array_values($keluar),
    //         ],

    //         'chartAktifPerBulan' => [
    //             'labels'        => $labelsPeriode,
    //             'aktif_total'   => $aktifBulanan,
    //             'aktif_smk'     => $aktifSMK,
    //             'aktif_pt'      => $aktifPT,
    //             'kuota_total'   => $totalKuotaAll,
    //             'kuota_smk'     => $sum['SMK']['kuota'],
    //             'kuota_pt'      => $sum['Perguruan Tinggi']['kuota'],
    //         ],
    //         'totalKuota' => $totalKuotaAll,
    //         'periode' => $periode,
    //     ]);
    // }


}
