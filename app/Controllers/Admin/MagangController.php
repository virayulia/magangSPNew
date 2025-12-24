<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\KuotaunitModel;
use App\Models\MagangModel;
use App\Models\UnitKerjaModel;
use App\Models\UserModel;
use App\Models\UnitUserModel;
use App\Models\RfidModel;
use App\Models\RfidAssignmentModel;
use App\Models\PenilaianModel;
use App\Models\SertifikatModel;
use Endroid\QrCode\Builder\Builder as QrBuilder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

class MagangController extends BaseController
{
    protected $magangModel;
    protected $userModel;
    protected $unitUserModel;
    protected $unitKerjaModel;
    protected $kuotaUnitModel;
    protected $rfidModel;
    protected $rfidAssignmentModel;
    protected $penilaianModel;
    protected $sertifikatModel;

    public function __construct()
    {
        $this->magangModel = new MagangModel();
        $this->userModel = new UserModel();
        $this->unitUserModel = new UnitUserModel();
        $this->unitKerjaModel = new UnitKerjaModel();
        $this->kuotaUnitModel = new KuotaunitModel();
        $this->rfidModel = new RfidModel();
        $this->rfidAssignmentModel = new RfidAssignmentModel();
        $this->penilaianModel = new PenilaianModel();
        $this->sertifikatModel = new SertifikatModel();
    }

    // public function index()
    // {
    //     $pendaftaran = $this->magangModel->select('magang.*,unit_kerja.unit_kerja, users.*,jurusan.nama_jurusan, 
    //                     instansi.nama_instansi, 
    //                     province_ktp.province AS provinsi_ktp,
    //                     province_dom.province AS provinsi_domisili, 
    //                     city_ktp.regency AS kota_ktp, 
    //                     city_ktp.type AS tipe_kota_ktp,
    //                     city_dom.regency AS kota_domisili,
    //                     city_dom.type AS tipe_kota_domisili')
    //                                     ->join('users', 'users.id = magang.user_id')
    //                                     ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
    //                                     ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id','left')
    //                                     ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
    //                                     ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
    //                                     ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
    //                                     ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
    //                                     ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
    //                                     ->whereIn('magang.status_akhir', ['proses', 'pendaftaran'])
    //                                     ->orderBy('magang.tanggal_daftar', 'asc')
    //                                     ->findAll();
    //     $unitList = $this->unitKerjaModel->findAll();
    //     $statusCount = [
    //         'Pendaftaran' => 0,
    //         'Proses Validasi' => 0,
    //         'Berkas Tidak Valid' => 0,
    //         'Terkonfirmasi' => 0,
    //         'Tidak Konfirmasi' => 0,
    //         'Diterima' => 0    // kalau mau spesifik dari seleksi
    //     ];

    //     foreach ($pendaftaran as $p) {
    //         if (!is_null($p['tanggal_validasi_berkas'])) {
    //             if ($p['status_validasi_berkas'] === 'Y')
    //                 $statusCount['Proses Validasi']++;
    //             else
    //                 $statusCount['Berkas Tidak Valid']++;
    //         } elseif (!is_null($p['status_konfirmasi'])) {
    //             if ($p['status_konfirmasi'] === 'Y')
    //                 $statusCount['Terkonfirmasi']++;
    //             else
    //                 $statusCount['Tidak Konfirmasi']++;
    //         } elseif (!is_null($p['status_seleksi'])) {
    //             if (strtolower($p['status_seleksi']) === 'diterima')
    //                 $statusCount['Diterima']++;
    //             else
    //                 $statusCount['Pendaftaran']++; // selain diterima, boleh dimasukkan ke pendaftaran
    //         } else {
    //             $statusCount['Pendaftaran']++;
    //         }
    //     }

    //     return view('admin/index', ['pendaftaran' => $pendaftaran, 'unitList' => $unitList, 'statusCount' => $statusCount]);
    // }

    public function index()
    {
        $filterStatus = $this->request->getGet('filter');

        $pendaftaranQuery = $this->magangModel
            ->select('magang.*, unit_kerja.unit_kerja, users.*, jurusan.nama_jurusan,
                    instansi.nama_instansi, 
                    province_ktp.province AS provinsi_ktp,
                    province_dom.province AS provinsi_domisili,
                    city_ktp.regency AS kota_ktp, city_ktp.type AS tipe_kota_ktp,
                    city_dom.regency AS kota_domisili, city_dom.type AS tipe_kota_domisili')
            ->join('users', 'users.id = magang.user_id')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
            ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
            ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
            ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
            ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
            ->whereIn('magang.status_akhir', ['proses', 'pendaftaran']);

        if ($filterStatus) {
            switch ($filterStatus) {
                case 'pendaftaran':
                    $pendaftaranQuery->where('magang.status_akhir', 'pendaftaran');
                    break;

                case 'diterima':
                    $pendaftaranQuery->where('magang.status_akhir', 'proses')
                                    ->where('magang.status_konfirmasi IS NULL')
                                    ->where('magang.status_validasi_berkas IS NULL');
                    break;

                case 'terkonfirmasi':
                    $pendaftaranQuery->where('magang.status_konfirmasi', 'Y')
                                    ->where('magang.status_validasi_berkas IS NULL');
                    break;

                case 'tidak-konfirmasi':
                    $pendaftaranQuery->where('magang.status_konfirmasi', 'N')
                                    ->where('magang.status_validasi_berkas IS NULL');
                    break;

                case 'proses-validasi':
                    $pendaftaranQuery->where('magang.status_validasi_berkas', 'Y');
                    break;

                case 'tidak-valid':
                    $pendaftaranQuery->where('magang.status_validasi_berkas', 'N');
                    break;
            }
        }

        $pendaftaran = $pendaftaranQuery->orderBy('magang.tanggal_daftar', 'asc')->findAll();

        $allData = $this->magangModel
            ->select('*')
            ->whereIn('magang.status_akhir', ['proses', 'pendaftaran'])
            ->findAll();

        $statusCount = [
            'Pendaftaran' => 0,
            'Proses Validasi' => 0,
            'Berkas Tidak Valid' => 0,
            'Terkonfirmasi' => 0,
            'Tidak Konfirmasi' => 0,
            'Diterima' => 0
        ];

        foreach ($allData as $p) {
            if (!is_null($p['tanggal_validasi_berkas'])) {
                if ($p['status_validasi_berkas'] === 'Y')
                    $statusCount['Proses Validasi']++;
                else
                    $statusCount['Berkas Tidak Valid']++;
            } elseif (!is_null($p['status_konfirmasi'])) {
                if ($p['status_konfirmasi'] === 'Y')
                    $statusCount['Terkonfirmasi']++;
                else
                    $statusCount['Tidak Konfirmasi']++;
            } elseif (!is_null($p['status_seleksi'])) {
                if (strtolower($p['status_seleksi']) === 'diterima')
                    $statusCount['Diterima']++;
                else
                    $statusCount['Pendaftaran']++;
            } else {
                $statusCount['Pendaftaran']++;
            }
        }


        return view('admin/index', [
            'pendaftaran' => $pendaftaran,
            'statusCount' => $statusCount,
            'filterStatus' => $filterStatus
        ]);
    }


    public function indexGagal()
    {
        $pendaftaran = $this->magangModel->select('magang.*,unit_kerja.unit_kerja, users.*,jurusan.nama_jurusan, 
                        instansi.nama_instansi, 
                        province_ktp.province AS provinsi_ktp,
                        province_dom.province AS provinsi_domisili, 
                        city_ktp.regency AS kota_ktp, 
                        city_ktp.type AS tipe_kota_ktp,
                        city_dom.regency AS kota_domisili,
                        city_dom.type AS tipe_kota_domisili')
                                        ->join('users', 'users.id = magang.user_id')
                                        ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
                                        ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id','left')
                                        ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
                                        ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
                                        ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
                                        ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
                                        ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
                                        ->whereIn('magang.status_akhir', ['gagal', 'batal'])
                                        ->orderBy('magang.tanggal_daftar', 'asc')
                                        ->findAll();
        $unitList = $this->unitKerjaModel->findAll();
        return view('admin/indexGagal', ['pendaftaran' => $pendaftaran, 'unitList' => $unitList]);
    }

    public function detail($id)
    {
        $pendaftaran = $this->magangModel
            ->select('magang.*, users.*, instansi.nama_instansi, jurusan.nama_jurusan,province_ktp.province AS provinsi_ktp,
                        province_dom.province AS provinsi_domisili,
                        city_ktp.regency AS kota_ktp, 
                        city_ktp.type AS tipe_kota_ktp,
                        city_dom.regency AS kota_domisili,
                        city_dom.type AS tipe_kota_domisili')
            ->join('users', 'users.id = magang.user_id')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
            ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
            ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
            ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
            ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
            ->where('magang.magang_id', $id)
            ->first();

        if (!$pendaftaran) {
            return redirect()->to('admin/manage-pendaftaran')->with('error', 'Data tidak ditemukan.');
        }
        return view('admin/detail', [
            'pendaftaran' => $pendaftaran
        ]);
    }
    //OLD unit seleksi difilter kuota
    // public function seleksi()
    // {
    //     $db = \Config\Database::connect();
    //     $today = date('Y-m-d');

    //     // Coba cari periode magang yang sedang berlangsung
    //     $periode = $db->table('periode_magang')
    //         ->where('tanggal_buka <=', $today)
    //         ->where('tanggal_tutup >=', $today)
    //         ->orderBy('tanggal_buka', 'DESC')
    //         ->limit(1)
    //         ->get()
    //         ->getRow();

    //     if ($periode) {
    //         // Ambil semua pendaftar berdasarkan periode yang aktif
    //         $pendaftar = $db->table('magang')
    //             ->join('users', 'users.id = magang.user_id')
    //             ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
    //             ->where('magang.periode_id', $periode->periode_id)
    //             ->select('magang.*, users.fullname, unit_kerja.unit_kerja')
    //             ->orderBy('magang.tanggal_daftar', 'DESC')
    //             ->get()
    //             ->getResult();
    //     } else {
    //         // Jika tidak ada periode aktif, ambil pendaftar berdasarkan tanggal bulan ini
    //         $firstDay = date('Y-m-01');
    //         $lastDay  = date('Y-m-t');

    //         $periode = (object)[
    //             'tanggal_buka' => $firstDay,
    //             'tanggal_tutup' => $lastDay,
    //             'id' => null,
    //         ];

    //         $pendaftar = $db->table('magang')
    //             ->join('users', 'users.id = magang.user_id')
    //             ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
    //             ->where('magang.tanggal_daftar >=', $firstDay)
    //             ->where('magang.tanggal_daftar <=', $lastDay)
    //             ->select('magang.*, users.fullname, unit_kerja.unit_kerja')
    //             ->orderBy('magang.tanggal_daftar', 'DESC')
    //             ->get()
    //             ->getResult();
    //     }

    //      // Filter hanya kuota dengan sisa > 0
    //     $allKuota = $this->magangModel->getSisaKuota();
    //     $filteredKuota = array_filter($allKuota, fn($k) => $k->sisa_kuota > 0);
    //     usort($filteredKuota, function ($a, $b) {
    //         return $b->jumlah_pendaftar <=> $a->jumlah_pendaftar;
    //     });

    //     $data['kuota_unit'] = $filteredKuota;
        
    //     // Kirim data ke view
    //     $data['pendaftar'] = $pendaftar;
    //     $data['periode'] = $periode;

    //     return view('admin/kelola_seleksi', $data);
    // }
    //OLD pendaftar difilter periode
    // public function pendaftar()
    // {
    //     $request = \Config\Services::request();
    //     $unitId = $request->getGet('unit_id');
    //     $pendidikan = $request->getGet('pendidikan');

    //     $db = \Config\Database::connect();
    //     $today = date('Y-m-d');

    //     // Ambil periode aktif atau fallback ke bulan ini
    //     $periode = $db->table('periode_magang')
    //         ->where('tanggal_buka <=', $today)
    //         ->where('tanggal_tutup >=', $today)
    //         ->orderBy('tanggal_buka', 'DESC')
    //         ->limit(1)
    //         ->get()
    //         ->getRow();

    //     if (!$periode) {
    //         // Fallback ke periode "bulan ini"
    //         $periode = (object)[
    //             'periode_id' => null, // tidak ada ID karena tidak ambil dari tabel
    //             'tanggal_buka' => date('Y-m-01'),
    //             'tanggal_tutup' => date('Y-m-t'),
    //         ];
    //     }

    //     // Ambil pendaftar sesuai periode
    //     $builder = $db->table('magang')
    //         ->select('magang.magang_id as magang_id, magang.*, users.*, instansi.nama_instansi, jurusan.nama_jurusan as jurusan')
    //         ->join('users', 'users.id = magang.user_id', 'left')
    //         ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
    //         ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
    //         ->where('magang.unit_id', $unitId)
    //         ->where('magang.status_akhir', 'pendaftaran');

    //     if ($periode->periode_id) {
    //         $builder->where('magang.periode_id', $periode->periode_id);
    //     } else {
    //         // Jika tidak ada periode, filter berdasarkan tanggal bulan ini
    //         $builder->where('magang.tanggal_daftar >=', $periode->tanggal_buka)
    //                 ->where('magang.tanggal_daftar <=', $periode->tanggal_tutup);
    //     }

    //     $builder->where("
    //         CASE 
    //             WHEN users.tingkat_pendidikan = 'SMK' THEN 'SMK'
    //             WHEN users.tingkat_pendidikan IN ('D3', 'D4/S1', 'S2') THEN 'Perguruan Tinggi'
    //             ELSE users.tingkat_pendidikan
    //         END = '$pendidikan'
    //     ", null, false);

    //     $builder->orderBy('magang.tanggal_daftar', 'asc');
    //     $pendaftar = $builder->get()->getResult();

    //     // Hitung sisa kuota
    //     $allKuota = $this->magangModel->getSisaKuota();
    //     $sisa = 0;
    //     foreach ($allKuota as $k) {
    //         if ($k->unit_id == $unitId && strtolower($k->tingkat_pendidikan) == strtolower($pendidikan)) {
    //             $sisa = $k->sisa_kuota;
    //             break;
    //         }
    //     }

    //     return view('admin/modal_pendaftar', [
    //         'pendaftar' => $pendaftar,
    //         'kuota_tersedia' => $sisa,
    //         'error' => null,
    //     ]);
    // }

    public function seleksi()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Cari periode aktif
        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->where('tanggal_tutup >=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

        // Ambil semua pendaftar dengan status 'pendaftaran' (baik periode aktif maupun lama)
        $pendaftar = $db->table('magang')
            ->join('users', 'users.id = magang.user_id')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->where('magang.status_akhir', 'pendaftaran')
            ->select('magang.*, users.fullname, unit_kerja.unit_kerja')
            ->orderBy('magang.tanggal_daftar', 'DESC')
            ->get()
            ->getResult();

        // Ambil semua kuota unit
        $allKuota = $this->magangModel->getSisaKuota();

        // Ambil unit_id yang masih punya pendaftar 'pendaftaran'
        $unitWithPendaftar = array_unique(array_map(fn($p) => $p->unit_id, $pendaftar));

        // Filter kuota agar hanya tampil unit yang memang ada pendaftarnya
        $filteredKuota = array_filter($allKuota, fn($k) => in_array($k->unit_id, $unitWithPendaftar));

        // Urutkan berdasarkan jumlah pendaftar
        usort($filteredKuota, fn($a, $b) => $b->jumlah_pendaftar <=> $a->jumlah_pendaftar);

        $data['kuota_unit'] = $filteredKuota;
        $data['pendaftar'] = $pendaftar;
        $data['periode']   = $periode;

        return view('admin/kelola_seleksi', $data);
    }

    public function pendaftar()
    {
        $request = \Config\Services::request();
        $unitId = $request->getGet('unit_id');
        $pendidikan = $request->getGet('pendidikan');

        $db = \Config\Database::connect();

        // Ambil semua pendaftar status 'pendaftaran' di unit tertentu
        $builder = $db->table('magang')
            ->select('magang.magang_id as magang_id, magang.*, users.*, instansi.nama_instansi, jurusan.nama_jurusan as jurusan')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->where('magang.unit_id', $unitId)
            ->where('magang.status_akhir', 'pendaftaran');

        // Filter tingkat pendidikan
        $builder->where("
            CASE 
                WHEN users.tingkat_pendidikan = 'SMK' THEN 'SMK'
                WHEN users.tingkat_pendidikan IN ('D3', 'D4/S1', 'S2') THEN 'Perguruan Tinggi'
                ELSE users.tingkat_pendidikan
            END = '$pendidikan'
        ", null, false);

        $builder->orderBy('magang.tanggal_daftar', 'asc');
        $pendaftar = $builder->get()->getResult();

        // Hitung sisa kuota
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

           // Ambil tanggal sekarang
            $today = new \DateTime();

            // Ambil tanggal 1 dua bulan ke depan
            $start = new \DateTime($today->format('Y-m-01'));
            $start->modify('+2 month');

            // Jika tanggal masuk adalah Sabtu (6) atau Minggu (7), geser ke hari kerja berikutnya
            while (in_array($start->format('N'), [6, 7])) {
                $start->modify('+1 day');
            }

            // Durasi magang dalam bulan
            $durasi = (int) $pendaftar->durasi;

            // Tanggal selesai = hari terakhir dari bulan ke-(durasi - 1) setelah bulan masuk
            $end = clone $start;
            $end->modify('last day of +' . ($durasi - 1) . ' month');

            // Simpan ke database
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
            $email->clear(); 
            $email->setTo($emailPeserta);
            $unit_id = 44;
            $signature = getSignature($unit_id);
            $email->setSubject('Penerimaan Magang di PT Semen Padang');
            $email->setMailType('html');
            $email->setMessage(view('emails/penerimaan_magang', [
                'nama'            => $user->fullname ?? $user->username,
                'unit'            => $pendaftar->unit_kerja,
                'tanggal_masuk'   => $start->format('d F Y'),
                'tanggal_selesai' => $end->format('d F Y'),
                'signature' => $signature
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

    public function tolakBanyak()
    {
        $ids = $this->request->getPost('pendaftar_ids');
        $catatan = $this->request->getPost('catatan'); 

        if (!$ids || !is_array($ids)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada pendaftar yang dipilih.']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($ids as $id) {
            $data = $builder
                ->select('magang.*, users.email, users.fullname, unit_kerja.unit_kerja')
                ->join('users', 'users.id = magang.user_id', 'left')
                ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
                ->where('magang.magang_id', $id)
                ->get()
                ->getRow();

            if (!$data) {
                $failCount++;
                $messages[] = "ID $id tidak ditemukan.";
                continue;
            }

            $updated = $builder->where('magang_id', $id)->update([
                'status_seleksi'   => 'Ditolak',
                'tanggal_seleksi' => date('Y-m-d H:i:s'),
                'status_akhir' => 'gagal',
                'alasan_batal' => $catatan 
            ]);

            if ($updated) {
                $successCount++;

                // kirim email
                $email = \Config\Services::email();
                $email->setTo($data->email);
                $unit_id = 44;
                $signature = getSignature($unit_id);
                $email->setSubject('Hasil Seleksi Pendaftaran Magang di PT Semen Padang');
                $email->setMailType('html');
                $email->setMessage(view('emails/penolakan_magang', [
                    'nama' => $data->fullname ?? 'Saudara',
                    'unit' => $data->unit_kerja ?? 'Unit terkait',
                    'alasan_batal' => $catatan,
                    'signature' => $signature
                ]));
                $email->send();

            } else {
                $failCount++;
                $messages[] = "ID $id gagal ditolak.";
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "$successCount berhasil ditolak. $failCount gagal.",
            'details' => $messages
        ]);
    }


    // public function tolakBanyak()
    // {
    //     $ids = $this->request->getPost('pendaftar_ids');

    //     if (!$ids || !is_array($ids)) {
    //         return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada pendaftar yang dipilih.']);
    //     }

    //     $db = \Config\Database::connect();
    //     $builder = $db->table('magang');

    //     $successCount = 0;
    //     $failCount = 0;
    //     $messages = [];

    //     foreach ($ids as $id) {
    //         log_message('debug', 'Memproses tolak ID: ' . $id);

    //         $data = $builder
    //             ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username, unit_kerja.unit_kerja')
    //             ->join('users', 'users.id = magang.user_id', 'left')
    //             ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
    //             ->where('magang.magang_id', $id)
    //             ->get()
    //             ->getRow();

    //         if (!$data) {
    //             log_message('error', "Data magang dengan ID $id tidak ditemukan.");
    //             $failCount++;
    //             $messages[] = "ID $id: tidak ditemukan.";
    //             continue;
    //         }

    //         $updated = $builder->where('magang_id', $id)->update([
    //             'status_seleksi'   => 'Ditolak',
    //             'tanggal_seleksi' => date('Y-m-d H:i:s'),
    //             'status_akhir' => 'gagal'
    //         ]);

    //         if ($updated) {
    //             log_message('debug', "ID $id: Berhasil ditolak.");
    //             $successCount++;

    //             // ===== Kirim Email Penolakan =====
    //             $email = \Config\Services::email();
    //             $email->setTo($data->email);

    //             $email->setSubject('Hasil Seleksi Pendaftaran Magang di PT Semen Padang');
    //             $email->setMailType('html');
    //             $email->setMessage(view('emails/penolakan_magang', [
    //                 'nama' => $data->fullname ?? 'Saudara',
    //                 'unit' => $data->unit_kerja ?? 'Unit terkait',
    //             ]));

    //             if (!$email->send()) {
    //                 log_message('error', "Gagal kirim email ke ID $id: " . print_r($email->printDebugger(), true));
    //             }

    //         } else {
    //             log_message('error', "ID $id: Gagal update data.");
    //             $failCount++;
    //             $messages[] = "ID $id: gagal ditolak.";
    //         }
    //     }

    //     return $this->response->setJSON([
    //         'status' => 'success',
    //         'message' => "$successCount berhasil ditolak. $failCount gagal.",
    //         'details' => $messages
    //     ]);
    // }

    public function validasi()
    {
        
        $data = $this->magangModel->select('magang.*, users.fullname, users.nisn_nim, users.bpjs_kes, users.bpjs_tk, users.buktibpjs_tk')
                                        ->join('users', 'users.id = magang.user_id')
                                        ->where('magang.status_konfirmasi', 'Y')
                                        ->where('magang.status_akhir', 'proses')
                                        ->orderBy('tanggal_konfirmasi')
                                        ->findAll();

        return view('admin/kelola_validasi', ['data' => $data]);
    }

    public function bulkValidasi()
    {
        $ids     = $this->request->getPost('ids'); 
        $action  = $this->request->getPost('action'); 
        $catatan = $this->request->getPost('catatan_bulk');

        if (empty($ids) || !in_array($action, ['approve', 'reject'])) {
            return redirect()->back()->with('error', 'Tidak ada peserta yang dipilih atau aksi tidak valid.');
        }

        $db      = \Config\Database::connect();
        $email   = \Config\Services::email();
        $tanggal = date('Y-m-d H:i:s');

        $pesertaList = $db->table('magang')
            ->select('magang.*, users.*, unit_kerja.unit_kerja, jurusan.nama_jurusan, instansi.nama_instansi')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('jurusan', 'users.jurusan_id = jurusan.jurusan_id', 'left')
            ->join('instansi', 'users.instansi_id = instansi.instansi_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->whereIn('magang.magang_id', $ids)
            ->get()
            ->getResult();

        foreach ($pesertaList as $data) {
            $updateData = [
                'status_validasi_berkas'  => ($action === 'approve') ? 'Y' : 'N',
                'tanggal_validasi_berkas' => $tanggal,
            ];

            if ($action === 'approve') {
                $updateData['status_akhir'] = 'magang';
            }

            $this->magangModel->update($data->magang_id, $updateData);

            // Kirim email
            $toEmail = $data->email;
            $ccEmail = $data->email_instansi;

            if (!empty($toEmail)) {
                $email->clear(true); 
                $email->setTo($toEmail);

                if (!empty($ccEmail) && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                    $email->setCC($ccEmail);
                }
                $unit_id = 44;
                $signature = getSignature($unit_id);
                $email->setSubject('Hasil Validasi Magang di PT Semen Padang');
                $email->setMailType('html');

                if ($action === 'approve') {
                    $email->setMessage(view('emails/approve', [
                        'nama'            => $data->fullname ?? $data->username,
                        'unit'            => $data->unit_kerja ?? 'Unit terkait',
                        'user_data'       => $data,
                        'tanggal_surat'   => $data->tanggal_surat,
                        'tanggal_masuk'   => $data->tanggal_masuk,
                        'tanggal_selesai' => $data->tanggal_selesai,
                        'signature' => $signature
                    ]));
                } else {
                    $email->setMessage(view('emails/tidak_approve', [
                        'nama'    => $data->fullname ?? $data->username,
                        'unit'    => $data->unit_kerja ?? 'Unit terkait',
                        'catatan' => $catatan,
                        'signature' => $signature
                    ]));
                }

                if (!$email->send()) {
                    log_message('error', "Gagal kirim email validasi ID {$data->magang_id}: " . print_r($email->printDebugger(), true));
                }
            }
        }

        return redirect()->back()->with('success', 
            $action === 'approve' 
                ? 'Peserta terpilih berhasil divalidasi dan email penerimaan telah dikirim.'
                : 'Peserta terpilih telah ditandai tidak valid dan email penolakan telah dikirim.'
        );
    }


    // public function berkas()
    // {
        
    //     $data = $this->magangModel->select('magang.*, users.fullname, users.nisn_nim, users.bpjs_kes, users.bpjs_tk, users.buktibpjs_tk')
    //                                     ->join('users', 'users.id = magang.user_id')
    //                                     ->where('magang.status_validasi_berkas', 'Y')
    //                                     ->where('magang.status_berkas_lengkap =', null)
    //                                     ->orderBy('tanggal_validasi_berkas')
    //                                     ->findAll();

    //     return view('admin/kelola_kelengkapan', ['data' => $data]);
    // }

    public function berkas($id = null)
    {
        $filter = $this->request->getGet('filter'); 

        $builder = $this->magangModel->select(
            'magang.*, users.fullname, users.nisn_nim, users.bpjs_kes, users.bpjs_tk, users.buktibpjs_tk'
        )->join('users', 'users.id = magang.user_id')
        ->where('magang.status_validasi_berkas', 'Y')
        ->groupStart()
            ->where('magang.status_berkas_lengkap !=', 'Y')
            ->orWhere('magang.status_berkas_lengkap IS NULL')
        ->groupEnd()
        ->orderBy('tanggal_validasi_berkas');

        if (!empty($id)) {
            $builder->where('magang.magang_id', $id);
        }

        $data = $builder->findAll();

        // --- Hitung kategori ---
        $statusBerkas = [
            'lengkap' => 0,
            'tk_bukti_saja' => 0,
            'belum' => 0
        ];

        // Data hasil filter
        $filteredData = [];

        foreach ($data as $d) {
            $bk = $d['bpjs_kes'];
            $tk = $d['bpjs_tk'];
            $bt = $d['buktibpjs_tk'];

            if (!empty($bk) && !empty($tk) && !empty($bt)) {
                $statusBerkas['lengkap']++;
                if ($filter == 'lengkap') $filteredData[] = $d;
            } elseif (empty($bk) && !empty($tk) && !empty($bt)) {
                $statusBerkas['tk_bukti_saja']++;
                if ($filter == 'tk_bukti_saja') $filteredData[] = $d;
            } else {
                $statusBerkas['belum']++;
                if ($filter == 'belum') $filteredData[] = $d;
            }
        }

        // Jika tidak ada filter → tampilkan semua
        if (!$filter) {
            $filteredData = $data;
        }

        return view('admin/kelola_kelengkapan', [
            'data' => $filteredData,
            'statusBerkas' => $statusBerkas,
            'filter' => $filter
        ]);
    }

    public function valid($id)
    {
        $catatan = $this->request->getPost('catatan');

        $db = \Config\Database::connect();
        $data = $db->table('magang')
            ->select('magang.*, users.*, unit_kerja.unit_kerja, jurusan.nama_jurusan, instansi.nama_instansi ')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('jurusan', 'users.jurusan_id = jurusan.jurusan_id', 'left')
            ->join('instansi', 'users.instansi_id = instansi.instansi_id', 'left')
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
        $unit_id = 44;
        $signature = getSignature($unit_id);
        $email->setSubject('Hasil Validasi Berkas Magang di PT Semen Padang');
        $email->setMailType('html');

        $email->setMessage(view('emails/berkas_valid', [
            'nama'            => $data->fullname ?? $data->username,
            'unit'            => $data->unit_kerja ?? 'Unit terkait',
            'user_data'       => $data,
            'tanggal_surat'   => $data->tanggal_surat, 
            'tanggal_masuk'   => $data->tanggal_masuk,
            'tanggal_selesai' => $data->tanggal_selesai,
            'signature' => $signature
        ]));

        if (!$email->send()) {
            log_message('error', "Gagal kirim email validasi berkas ID $id: " . print_r($email->printDebugger(), true));
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
        ];

        $this->magangModel->update($id, $updateData);

        $updateDataBerkas = [
            'bpjs_tk'    => NULL,
            'buktibpjs_tk' => NULL,
        ];

        $this->userModel->update($data->user_id, $updateDataBerkas);

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
        $unit_id = 44;
        $signature = getSignature($unit_id);
        $email->setSubject('Hasil Validasi Berkas Magang di PT Semen Padang');
        $email->setMailType('html');

        $email->setMessage(view('emails/berkas_tidak_valid', [
            'nama'    => $data->fullname ?? $data->username,
            'unit'    => $data->unit_kerja ?? 'Unit terkait',
            'catatan' => $catatan,
            'signature' => $signature
        ]));

        if (!$email->send()) {
            log_message('error', "Gagal kirim email validasi berkas ID $id: " . print_r($email->printDebugger(), true));
        }

        return redirect()->back()->with('success', 'Validasi tidak valid berhasil disimpan dan email telah dikirim.');
    }

    public function safety()
    {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();

        $bulan = $request->getGet('bulan');
        $tahun = $request->getGet('tahun');

        if (!$bulan || !$tahun) {
            $bulan = date('m');
            $tahun = date('Y');
        }

        $hasil = $db->table('jawaban_safety')
            ->select('
                users.fullname,
                users.nisn_nim,
                unit_kerja.unit_kerja,
                jawaban_safety.relasi_id,
                MAX(jawaban_safety.nilai) as nilai_maksimal,
                MAX(jawaban_safety.created_at) as tanggal_terakhir,
                MAX(jawaban_safety.percobaan_ke) as percobaan_terakhir,
                (CASE WHEN MAX(jawaban_safety.nilai) >= 70 THEN "Lulus" ELSE "Tidak Lulus" END) as status
            ')
            ->join('magang', 'magang.magang_id = jawaban_safety.relasi_id AND jawaban_safety.tipe = "magang"')
            ->join('users', 'users.id = magang.user_id')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->where('magang.status_akhir','magang')
            ->where("MONTH(jawaban_safety.created_at)", $bulan)
            ->where("YEAR(jawaban_safety.created_at)", $tahun)
            ->groupBy('jawaban_safety.relasi_id')
            ->get()->getResult();

        return view('admin/kelola_safety', [
            'hasil' => $hasil,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);
    }

    // public function pesertaMagang()
    // {
    //     $bulan = $this->request->getGet('bulan');
    //     $tahun = $this->request->getGet('tahun');

    //     $builder = $this->magangModel->select('
    //                         magang.*,
    //                         unit_kerja.unit_kerja,
    //                         users.*,
    //                         jurusan.nama_jurusan,
    //                         instansi.nama_instansi,
    //                         penilaian.*,
    //                         province_ktp.province AS provinsi_ktp,
    //                         province_dom.province AS provinsi_domisili,
    //                         city_ktp.regency AS kota_ktp, 
    //                         city_ktp.type AS tipe_kota_ktp,
    //                         city_dom.regency AS kota_domisili,
    //                         city_dom.type AS tipe_kota_domisili,
    //                         MAX(jawaban_safety.nilai) as nilai_maksimal,
    //                         MAX(jawaban_safety.created_at) as tanggal_terakhir,
    //                         MAX(jawaban_safety.percobaan_ke) as percobaan_terakhir,
    //                         CASE 
    //                             WHEN MAX(jawaban_safety.nilai) IS NULL THEN "Belum Tes"
    //                             WHEN MAX(jawaban_safety.nilai) >= 70 THEN "Lulus"
    //                             ELSE "Belum Lulus"
    //                         END as status_tes,
    //                         rfid.rfid_no, rfid.id_rfid, 
    //                         ra.assignment_id, ra.status as status_rfid, ra.tanggal_kembali, ra.tanggal_bayar, 
    //                         feedback.feedback_id, 
    //                     ')
    //                     ->join('users', 'users.id = magang.user_id')
    //                     ->join('instansi', 'instansi.instansi_id = users.instansi_id')
    //                     ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
    //                     ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
    //                     ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
    //                     ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
    //                     ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
    //                     ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
    //                     ->join('jawaban_safety', 'magang.magang_id = jawaban_safety.magang_id', 'left')
    //                     ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
    //                     ->join('feedback', 'feedback.magang_id = magang.magang_id', 'left')
    //                      ->join('(
    //                         SELECT r1.*
    //                         FROM rfid_assignment r1
    //                         JOIN (
    //                             SELECT magang_id, MAX(assignment_id) as max_created
    //                             FROM rfid_assignment
    //                             GROUP BY magang_id
    //                         ) r2 ON r1.magang_id = r2.magang_id AND r1.assignment_id = r2.max_created
    //                     ) AS ra', 'ra.magang_id = magang.magang_id', 'left')
    //                     ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
    //                     ->where('magang.status_akhir', 'magang')
    //                     ->groupBy('magang.magang_id');


    //     if (!empty($bulan)) {
    //         $builder->where('MONTH(magang.tanggal_masuk)', $bulan);
    //     }

    //     if (!empty($tahun)) {
    //         $builder->where('YEAR(magang.tanggal_masuk)', $tahun);
    //     }

    //     $data = $builder->findAll();
    //     $unitList = $this->unitKerjaModel->findAll();
    //     $rfid = $this->rfidModel->findAll();

    //     return view('admin/kelola_magang', ['data' => $data, 'unitList' => $unitList, 'rfidList' => $rfid]);
    // }

    public function pesertaMagang2()
    {
        $bulanMasuk  = $this->request->getGet('bulan_masuk');
        $bulanKeluar = $this->request->getGet('bulan_keluar');
        $tahun       = $this->request->getGet('tahun') ?: date('Y');

        // --- Subquery untuk jawaban_safety (ambil percobaan terakhir) ---
        $subSafety = "
            (
                SELECT js1.magang_id, js1.nilai, js1.created_at, js1.percobaan_ke
                FROM jawaban_safety js1
                JOIN (
                    SELECT magang_id, MAX(created_at) AS max_created
                    FROM jawaban_safety
                    GROUP BY magang_id
                ) js2 
                ON js1.magang_id = js2.magang_id AND js1.created_at = js2.max_created
            ) AS js
        ";

        // --- Subquery untuk rfid_assignment (ambil assignment terakhir) ---
        $subRfid = "
            (
                SELECT r1.*
                FROM rfid_assignment r1
                JOIN (
                    SELECT magang_id, MAX(assignment_id) AS max_created
                    FROM rfid_assignment
                    GROUP BY magang_id
                ) r2 
                ON r1.magang_id = r2.magang_id AND r1.assignment_id = r2.max_created
            ) AS ra
        ";

        $builder = $this->magangModel->select('
                magang.*,
                unit_kerja.unit_kerja,
                users.id as user_id, users.fullname, users.email,users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
                users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
                users.tingkat_pendidikan, users.instansi_id, users.jurusan_id, users.semester, 
                users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
                users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
                users.buktibpjs_tk, users.ktp_kk, users.status,
                jurusan.nama_jurusan,
                instansi.nama_instansi,
                province_ktp.province AS provinsi_ktp,
                province_dom.province AS provinsi_domisili,
                city_ktp.regency AS kota_ktp, 
                city_ktp.type AS tipe_kota_ktp,
                city_dom.regency AS kota_domisili,
                city_dom.type AS tipe_kota_domisili,
                js.nilai as nilai_maksimal,
                js.created_at as tanggal_terakhir,
                js.percobaan_ke as percobaan_terakhir,
                rfid.rfid_no, rfid.id_rfid,
                ra.assignment_id, ra.status as status_rfid, ra.tanggal_kembali, ra.tanggal_bayar,
                feedback.feedback_id,
                penilaian.nilai_disiplin, penilaian.nilai_kerajinan,penilaian.nilai_tingkahlaku, penilaian.nilai_kerjasama,
                penilaian.nilai_kreativitas,penilaian.nilai_kemampuankerja,penilaian.nilai_tanggungjawab,penilaian.nilai_penyerapan,
                penilaian.tgl_penilaian, penilaian.approve_kaunit,penilaian.tgl_disetujui,penilaian.approve_by,penilaian.catatan, penilaian.catatan_approval,
                pembimbing.fullname AS nama_pembimbing
            ')
            ->select("
                CASE 
                    WHEN js.nilai IS NULL THEN 'Belum Tes'
                    WHEN js.nilai >= 70 THEN 'Lulus'
                    ELSE 'Belum Lulus'
                END AS status_tes
            ", false) 
            ->join('users', 'users.id = magang.user_id')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id','left')
            ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
            ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
            ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
            ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
            ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
            ->join($subSafety, 'js.magang_id = magang.magang_id', 'left')
            ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
            ->join('feedback', 'feedback.magang_id = magang.magang_id', 'left')
            ->join($subRfid, 'ra.magang_id = magang.magang_id', 'left')
            ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
            ->join('users pembimbing', 'pembimbing.id = magang.pembimbing_id', 'left') 
            ->where('magang.status_akhir', 'magang');

        if (!empty($bulanMasuk)) {
            $builder->where('MONTH(magang.tanggal_masuk)', $bulanMasuk);
        }

        if (!empty($bulanKeluar)) {
            $builder->where('MONTH(magang.tanggal_selesai)', $bulanKeluar);
        }

        if (!empty($tahun)) {
            $builder->groupStart()
                    ->where('YEAR(magang.tanggal_masuk)', $tahun)
                    ->orWhere('YEAR(magang.tanggal_selesai)', $tahun)
                    ->groupEnd();
        }

        $data = $builder->findAll();
        $unitList = $this->unitKerjaModel->findAll();
        $rfid = $this->rfidModel->findAll();
        
        return view('admin/kelola_magang', [
            'data' => $data,
            'unitList' => $unitList,
            'rfidList' => $rfid
        ]);
    }

    // public function pesertaMagang()
    // {
    //     $unitId  = $this->request->getGet('unit_kerja');
    //     $bulanMasuk  = $this->request->getGet('bulan_masuk');
    //     $bulanKeluar = $this->request->getGet('bulan_keluar');
    //     $tahun       = $this->request->getGet('tahun');
    //     $filter = $this->request->getGet('filter');
    //     $tingkat = $this->request->getGet('tingkat');
    //     $today = date('Y-m-d');

    //     // --- Subquery untuk jawaban_safety (ambil percobaan terakhir) ---
    //     $subSafety = "
    //         (
    //             SELECT js1.relasi_id, js1.nilai, js1.created_at, js1.percobaan_ke, js1.tipe
    //             FROM jawaban_safety js1
    //             JOIN (
    //                 SELECT relasi_id, tipe, MAX(created_at) AS max_created
    //                 FROM jawaban_safety
    //                 GROUP BY relasi_id, tipe
    //             ) js2 
    //             ON js1.relasi_id = js2.relasi_id 
    //             AND js1.tipe = js2.tipe
    //             AND js1.created_at = js2.max_created
    //         ) AS js
    //     ";

    //     // --- Subquery untuk rfid_assignment (ambil assignment terakhir) ---
    //     $subRfid = "
    //         (
    //             SELECT r1.*
    //             FROM rfid_assignment r1
    //             JOIN (
    //                 SELECT relasi_id, tipe, MAX(assignment_id) AS max_created
    //                 FROM rfid_assignment
    //                 GROUP BY relasi_id, tipe
    //             ) r2 
    //             ON r1.relasi_id = r2.relasi_id 
    //             AND r1.tipe = r2.tipe
    //             AND r1.assignment_id = r2.max_created
    //         ) AS ra
    //     ";

    //     $mapTingkat = [
    //         'SMK' => ['SMK'],
    //         'Perguruan Tinggi' => ['D3', 'D4/S1', 'S2']
    //     ];

    //     $kuotaAll = $this->magangModel->getSisaKuota();
    //     if (!empty($unitId)) {
    //         $kuotaAll = array_filter($kuotaAll, function($row) use ($unitId) {
    //             return $row->unit_id == $unitId;
    //         });
    //     }

    //     $sum = [
    //         'Perguruan Tinggi' => [
    //             'tingkat_pendidikan' => 'Perguruan Tinggi',
    //             'kuota' => 0,
    //             'jumlah_proses' => 0,
    //             'jumlah_aktif' => 0,
    //             'jumlah_akan_magang' => 0,
    //             'jumlah_belum_selesai' => 0,
    //             'sisa_kuota' => 0
    //         ],
    //         'SMK' => [
    //             'tingkat_pendidikan' => 'SMK',
    //             'kuota' => 0,
    //             'jumlah_proses' => 0,
    //             'jumlah_aktif' => 0,
    //             'jumlah_akan_magang' => 0,
    //             'jumlah_belum_selesai' => 0,
    //             'sisa_kuota' => 0
    //         ]
            
    //     ];

    //     foreach ($kuotaAll as $row) {
    //         $t = $row->tingkat_pendidikan;

    //         $sum[$t]['kuota'] += (int)$row->kuota;
    //         $sum[$t]['jumlah_proses'] += (int)$row->jumlah_proses;
    //         $sum[$t]['jumlah_aktif'] += (int)$row->jumlah_aktif;
    //         $sum[$t]['jumlah_akan_magang'] += (int)$row->jumlah_akan_magang;
    //         $sum[$t]['jumlah_belum_selesai'] += (int)$row->jumlah_belum_selesai;
    //         $sum[$t]['sisa_kuota'] += (int)$row->sisa_kuota;
    //     }

    //     $totalKuota = [];

    //     foreach ($sum as $item) {
    //         $totalKuota[] = (object)$item;
    //     }


    //     $builder = $this->magangModel->select('
    //             magang.magang_id, magang.status_berkas_lengkap, magang.tanggal_setujui_pernyataan,
    //             magang.laporan, magang.absensi, magang.tanggal_masuk, magang.tanggal_selesai, magang.finalisasi,
    //             unit_kerja.unit_id, unit_kerja.unit_kerja,
    //             users.id as user_id, users.fullname, users.nisn_nim, users.tingkat_pendidikan,
    //             js.nilai as nilai_maksimal,
    //             rfid.rfid_no, rfid.id_rfid,
    //             ra.assignment_id, ra.status as status_rfid, ra.tanggal_kembali, ra.tanggal_bayar,
    //             feedback.feedback_id,
    //             penilaian.nilai_disiplin, penilaian.nilai_kerajinan,penilaian.nilai_tingkahlaku, penilaian.nilai_kerjasama,
    //             penilaian.nilai_kreativitas,penilaian.nilai_kemampuankerja,penilaian.nilai_tanggungjawab,penilaian.nilai_penyerapan,
    //             penilaian.tgl_penilaian, penilaian.approve_kaunit,penilaian.tgl_disetujui,penilaian.approve_by,penilaian.catatan, penilaian.catatan_approval,
    //             pembimbing.fullname AS nama_pembimbing, pembimbing.id as pembimbing_id
    //         ')
    //         ->select("
    //             CASE 
    //                 WHEN js.nilai IS NULL THEN 'Belum Tes'
    //                 WHEN js.nilai >= 70 THEN 'Lulus'
    //                 ELSE 'Belum Lulus'
    //             END AS status_tes
    //         ", false) 
    //         ->join('users', 'users.id = magang.user_id')
    //         ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
    //         ->join($subSafety, 'js.relasi_id = magang.magang_id', 'left')
    //         ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
    //         ->join('feedback', 'feedback.magang_id = magang.magang_id', 'left')
    //         ->join($subRfid, 'ra.relasi_id = magang.magang_id', 'left')
    //         ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
    //         ->join('users pembimbing', 'pembimbing.id = magang.pembimbing_id', 'left') 
    //         ->where('magang.status_akhir', 'magang');

    //     if (!empty($bulanMasuk)) {
    //         $builder->where('MONTH(magang.tanggal_masuk)', $bulanMasuk);
    //     }

    //     if (!empty($bulanKeluar)) {
    //         $builder->where('MONTH(magang.tanggal_selesai)', $bulanKeluar);
    //     }

    //     if (!empty($tahun)) {
    //         $builder->groupStart()
    //                 ->where('YEAR(magang.tanggal_masuk)', $tahun)
    //                 ->orWhere('YEAR(magang.tanggal_selesai)', $tahun)
    //                 ->groupEnd();
    //     }

    //     if (!empty($unitId)) {
    //         $builder->where('magang.unit_id', $unitId);
    //     }

    //     if ($tingkat && isset($mapTingkat[$tingkat])) {
    //         $builder->whereIn('users.tingkat_pendidikan', $mapTingkat[$tingkat]);
    //     }

    //     if ($filter == 'aktif') {
    //         // Sedang magang hari ini
    //         $builder->where('magang.tanggal_masuk <=', $today)
    //                 ->where('magang.tanggal_selesai >=', $today);
    //     }

    //     if ($filter == 'akan_magang') {
    //         // Sudah diterima tapi magangnya belum mulai
    //         $builder->where('magang.tanggal_masuk >', $today);
    //     }

    //     if ($filter == 'belum_selesai') {
    //         // Sudah lewat tanggal selesai tapi masih berstatus magang
    //         $builder->where('magang.tanggal_selesai <', $today);
    //     }

    //     $data = $builder->findAll();
    //     $unitList = $this->unitKerjaModel->findAll();
    //     $rfid = $this->rfidModel->findAll();
        
    //     return view('admin/kelola_magang', [
    //         'data' => $data,
    //         'unitList' => $unitList,
    //         'rfidList' => $rfid,
    //         'totalKuota' => $totalKuota
    //     ]);
    // }
    public function pesertaMagang()
    {
        $unitId  = $this->request->getGet('unit_kerja');
        $bulanMasuk  = $this->request->getGet('tanggal_masuk'); 
        $bulanKeluar = $this->request->getGet('tanggal_keluar');
        // $tahun       = $this->request->getGet('tahun');
        $filter = $this->request->getGet('filter');
        $tingkat = $this->request->getGet('tingkat');
        $today = date('Y-m-d');

        // Subquery safety – ambil percobaan terakhir
        $subSafety = "
            (
                SELECT js1.*
                FROM jawaban_safety js1
                JOIN (
                    SELECT relasi_id, MAX(created_at) AS max_created
                    FROM jawaban_safety
                    GROUP BY relasi_id
                ) js2 ON js1.relasi_id = js2.relasi_id AND js1.created_at = js2.max_created
            ) AS js
        ";

        // Subquery RFID – ambil assignment terakhir
        $subRfid = "
            (
                SELECT r1.*
                FROM rfid_assignment r1
                JOIN (
                    SELECT relasi_id, MAX(assignment_id) AS max_assign
                    FROM rfid_assignment
                    GROUP BY relasi_id
                ) r2 ON r1.relasi_id = r2.relasi_id AND r1.assignment_id = r2.max_assign
            ) AS ra
        ";
        
        $mapTingkat = [
            'SMK' => ['SMK'],
            'Perguruan Tinggi' => ['D3', 'D4/S1', 'S2']
        ];

        $builder = $this->magangModel
            ->select('
                magang.*,
                unit_kerja.unit_id, unit_kerja.unit_kerja,
                users.id as user_id, users.fullname, users.nisn_nim, users.tingkat_pendidikan,
                js.nilai as nilai_maksimal,
                rfid.rfid_no, rfid.id_rfid,
                ra.assignment_id, ra.status as status_rfid, ra.tanggal_kembali, ra.tanggal_bayar,
                feedback.feedback_id,
                penilaian.nilai_disiplin, penilaian.nilai_kerajinan, penilaian.nilai_tingkahlaku, penilaian.nilai_kerjasama,
                penilaian.nilai_kreativitas, penilaian.nilai_kemampuankerja, penilaian.nilai_tanggungjawab, penilaian.nilai_penyerapan,
                penilaian.tgl_penilaian, penilaian.approve_kaunit, penilaian.tgl_disetujui, penilaian.approve_by, penilaian.catatan, penilaian.catatan_approval,
                pembimbing.fullname AS nama_pembimbing, pembimbing.id AS pembimbing_id
            ')
            ->select("
                CASE 
                    WHEN js.nilai IS NULL THEN '-'
                    WHEN js.nilai >= 70 THEN 'Lulus'
                    ELSE 'Belum Lulus'
                END AS status_tes
            ", false)
            ->join('users', 'users.id = magang.user_id')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->join($subSafety, 'js.relasi_id = magang.magang_id', 'left')
            ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
            ->join('feedback', 'feedback.magang_id = magang.magang_id', 'left')
            ->join($subRfid, 'ra.relasi_id = magang.magang_id', 'left')
            ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
            ->join('users pembimbing', 'pembimbing.id = magang.pembimbing_id', 'left')
            ->where('magang.status_akhir', 'magang')
            ->groupBy('magang.magang_id');

        // Filter dinamis
        // if (!empty($bulanMasuk)) $builder->where('MONTH(magang.tanggal_masuk)', $bulanMasuk);
        // if (!empty($bulanKeluar)) $builder->where('MONTH(magang.tanggal_selesai)', $bulanKeluar);
        // if (!empty($tahun)) {
        //     $builder->groupStart()
        //             ->where('YEAR(magang.tanggal_masuk)', $tahun)
        //             ->orWhere('YEAR(magang.tanggal_selesai)', $tahun)
        //             ->groupEnd();
        // }

        if ($bulanMasuk) {
            $builder->where('magang.tanggal_masuk >=', $bulanMasuk . '-01');
            $builder->where(
                'magang.tanggal_masuk <',
                date('Y-m-01', strtotime($bulanMasuk . ' +1 month'))
            );
        }
        if ($bulanKeluar) {
            $builder->where('magang.tanggal_selesai >=', $bulanKeluar . '-01');
            $builder->where(
                'magang.tanggal_selesai <',
                date('Y-m-01', strtotime($bulanKeluar . ' +1 month'))
            );
        }
        if (!empty($unitId)) $builder->where('magang.unit_id', $unitId);
        if (!empty($tingkat) && isset($mapTingkat[$tingkat])) {
            $builder->whereIn('users.tingkat_pendidikan', $mapTingkat[$tingkat]);
        }

        if ($filter == 'aktif')
            $builder->where('magang.tanggal_masuk <=', $today)->where('magang.tanggal_selesai >=', $today);
        if ($filter == 'akan_magang')
            $builder->where('magang.tanggal_masuk >', $today);
        if ($filter == 'belum_selesai')
            $builder->where('magang.tanggal_selesai <', $today);

        // Ambil data dalam BENTUK ARRAY
        $data = $builder->asArray()->findAll();

        $unitList = $this->unitKerjaModel->findAll();
        $rfid = $this->rfidModel->findAll();

        $today = date('Y-m-d');

        $chartData = [
                'SMK' => [
                    'proses' => 0,
                    'aktif' => 0,
                    'akan_masuk' => 0,
                    'belum_lulus' => 0
                ],
                'Perguruan Tinggi' => [
                    'proses' => 0,
                    'aktif' => 0,
                    'akan_masuk' => 0,
                    'belum_lulus' => 0
                ]
            ];

        foreach ($data as $row) {

            // Tentukan kelompok tingkat pendidikan
            $tingkat = $row['tingkat_pendidikan'];

            // Normalisasi (karena PT punya D3, D4/S1, S2)
            if (in_array($tingkat, ['D3', 'D4/S1', 'S2'])) {
                $tingkat = 'Perguruan Tinggi';
            } elseif ($tingkat === 'SMK') {
                $tingkat = 'SMK';
            } else {
                continue; // skip jika bukan dua kategori ini
            }

            // 1. PROSES — status_akhir = proses
            if ($row['status_akhir'] === 'proses') {
                $chartData[$tingkat]['proses']++;
            }

            // 2. AKTIF — sedang magang sekarang
            if ($row['tanggal_masuk'] <= $today && $row['tanggal_selesai'] >= $today) {
                $chartData[$tingkat]['aktif']++;
            }

            // 3. AKAN MASUK — tanggal masuk di masa mendatang
            if ($row['tanggal_masuk'] > $today) {
                $chartData[$tingkat]['akan_masuk']++;
            }

            // 4. BELUM LULUS — sudah lewat tapi status akhir masih magang
            if ($row['tanggal_selesai'] < $today && $row['status_akhir'] === 'magang') {
                $chartData[$tingkat]['belum_lulus']++;
            }
        }

        $unitGet = $unitId ?? '';

        // atau pastikan string kosong jika null
        if ($unitGet === null) $unitGet = '';



        return view('admin/kelola_magang', [
            'data' => $data,
            'unitList' => $unitList,
            'rfidList' => $rfid, 
            'chartData' => $chartData,
            'unitGet' => $unitGet,
        ]);
    }
    // public function pesertaMagang()
    // {
    //     $unitId  = $this->request->getGet('unit_kerja');
    //     $bulanMasuk  = $this->request->getGet('bulan_masuk');
    //     $bulanKeluar = $this->request->getGet('bulan_keluar');
    //     $tahun       = $this->request->getGet('tahun');
    //     $filter = $this->request->getGet('filter');
    //     $tingkat = $this->request->getGet('tingkat');
    //     $today = date('Y-m-d');

    //     // Subquery safety – ambil percobaan terakhir
    //     $subSafety = "
    //         (
    //             SELECT js1.*
    //             FROM jawaban_safety js1
    //             JOIN (
    //                 SELECT relasi_id, MAX(created_at) AS max_created
    //                 FROM jawaban_safety
    //                 GROUP BY relasi_id
    //             ) js2 ON js1.relasi_id = js2.relasi_id AND js1.created_at = js2.max_created
    //         ) AS js
    //     ";

    //     // Subquery RFID – ambil assignment terakhir
    //     $subRfid = "
    //         (
    //             SELECT r1.*
    //             FROM rfid_assignment r1
    //             JOIN (
    //                 SELECT relasi_id, MAX(assignment_id) AS max_assign
    //                 FROM rfid_assignment
    //                 GROUP BY relasi_id
    //             ) r2 ON r1.relasi_id = r2.relasi_id AND r1.assignment_id = r2.max_assign
    //         ) AS ra
    //     ";

    //     // Hitung total kuota berdasarkan unit dan tingkat
    //     $mapTingkat = [
    //         'SMK' => ['SMK'],
    //         'Perguruan Tinggi' => ['D3', 'D4/S1', 'S2']
    //     ];

    //     $kuotaAll = $this->magangModel->getSisaKuota();
    //     if (!empty($unitId)) {
    //         $kuotaAll = array_filter($kuotaAll, fn($r) => $r->unit_id == $unitId);
    //     }

    //     $sum = [
    //         'Perguruan Tinggi' => ['tingkat_pendidikan' => 'Perguruan Tinggi', 'kuota' => 0, 'jumlah_proses' => 0, 'jumlah_aktif' => 0, 'jumlah_akan_magang' => 0, 'jumlah_belum_selesai' => 0, 'sisa_kuota' => 0],
    //         'SMK' => ['tingkat_pendidikan' => 'SMK', 'kuota' => 0, 'jumlah_proses' => 0, 'jumlah_aktif' => 0, 'jumlah_akan_magang' => 0, 'jumlah_belum_selesai' => 0, 'sisa_kuota' => 0]
    //     ];

    //     foreach ($kuotaAll as $row) {
    //         $t = $row->tingkat_pendidikan;
    //         foreach (['kuota', 'jumlah_proses', 'jumlah_aktif', 'jumlah_akan_magang', 'jumlah_belum_selesai', 'sisa_kuota'] as $k) {
    //             $sum[$t][$k] += (int)$row->$k;
    //         }
    //     }

    //     $totalKuota = array_map(fn($i) => (object)$i, $sum);

    //     $builder = $this->magangModel
    //         ->select('
    //             magang.magang_id, magang.status_berkas_lengkap, magang.tanggal_setujui_pernyataan,
    //             magang.laporan, magang.absensi, magang.tanggal_masuk, magang.tanggal_selesai, magang.finalisasi,
    //             unit_kerja.unit_id, unit_kerja.unit_kerja,
    //             users.id as user_id, users.fullname, users.nisn_nim, users.tingkat_pendidikan,
    //             js.nilai as nilai_maksimal,
    //             rfid.rfid_no, rfid.id_rfid,
    //             ra.assignment_id, ra.status as status_rfid, ra.tanggal_kembali, ra.tanggal_bayar,
    //             feedback.feedback_id,
    //             penilaian.nilai_disiplin, penilaian.nilai_kerajinan, penilaian.nilai_tingkahlaku, penilaian.nilai_kerjasama,
    //             penilaian.nilai_kreativitas, penilaian.nilai_kemampuankerja, penilaian.nilai_tanggungjawab, penilaian.nilai_penyerapan,
    //             penilaian.tgl_penilaian, penilaian.approve_kaunit, penilaian.tgl_disetujui, penilaian.approve_by, penilaian.catatan, penilaian.catatan_approval,
    //             pembimbing.fullname AS nama_pembimbing, pembimbing.id AS pembimbing_id
    //         ')
    //         ->select("
    //             CASE 
    //                 WHEN js.nilai IS NULL THEN 'Belum Tes'
    //                 WHEN js.nilai >= 70 THEN 'Lulus'
    //                 ELSE 'Belum Lulus'
    //             END AS status_tes
    //         ", false)
    //         ->join('users', 'users.id = magang.user_id')
    //         ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
    //         ->join($subSafety, 'js.relasi_id = magang.magang_id', 'left')
    //         ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
    //         ->join('feedback', 'feedback.magang_id = magang.magang_id', 'left')
    //         ->join($subRfid, 'ra.relasi_id = magang.magang_id', 'left')
    //         ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
    //         ->join('users pembimbing', 'pembimbing.id = magang.pembimbing_id', 'left')
    //         ->where('magang.status_akhir', 'magang')
    //         ->groupBy('magang.magang_id');

    //     // Filter dinamis
    //     if (!empty($bulanMasuk)) $builder->where('MONTH(magang.tanggal_masuk)', $bulanMasuk);
    //     if (!empty($bulanKeluar)) $builder->where('MONTH(magang.tanggal_selesai)', $bulanKeluar);
    //     if (!empty($tahun)) {
    //         $builder->groupStart()
    //                 ->where('YEAR(magang.tanggal_masuk)', $tahun)
    //                 ->orWhere('YEAR(magang.tanggal_selesai)', $tahun)
    //                 ->groupEnd();
    //     }
    //     if (!empty($unitId)) $builder->where('magang.unit_id', $unitId);
    //     if (!empty($tingkat) && isset($mapTingkat[$tingkat])) {
    //         $builder->whereIn('users.tingkat_pendidikan', $mapTingkat[$tingkat]);
    //     }

    //     if ($filter == 'aktif')
    //         $builder->where('magang.tanggal_masuk <=', $today)->where('magang.tanggal_selesai >=', $today);
    //     if ($filter == 'akan_magang')
    //         $builder->where('magang.tanggal_masuk >', $today);
    //     if ($filter == 'belum_selesai')
    //         $builder->where('magang.tanggal_selesai <', $today);

    //     // Ambil data dalam BENTUK ARRAY
    //     $data = $builder->asArray()->findAll();

    //     $unitList = $this->unitKerjaModel->findAll();
    //     $rfid = $this->rfidModel->findAll();
        // $chartData = [
        //     'Perguruan Tinggi' => $sum['Perguruan Tinggi'],
        //     'SMK' => $sum['SMK']
        // ];

    //     return view('admin/kelola_magang', [
    //         'data' => $data,
    //         'unitList' => $unitList,
    //         'rfidList' => $rfid,
    //         'totalKuota' => $totalKuota,
    //         'chartData' => $chartData
    //     ]);
    // }

    public function pesertaMagang3()
    {
        $unitList = $this->unitKerjaModel->findAll();
        $rfid = $this->rfidModel->findAll();

        return view('admin/kelola_magang', [
            'unitList' => $unitList,
            'rfidList' => $rfid
        ]);
    }

    public function getDataPembimbing($unit_id)
    {
        $db = \Config\Database::connect();

        $builder = $db->table('users u');
        $builder->select('u.id, u.fullname, u.email');
        $builder->join('auth_groups_users agu', 'agu.user_id = u.id');
        $builder->join('unit_user uu', 'uu.user_id = u.id');

        $builder->where('agu.group_id', 4);  
        $builder->where('uu.unit_id', $unit_id);

        $result = $builder->get()->getResultArray();

        return $this->response->setJSON($result);
    }

    public function setPembimbing($magang_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $pembimbing_id = $this->request->getPost('pembimbing_id');

        if (empty($pembimbing_id)) {
            return redirect()->back()->with('error', 'Silakan pilih pembimbing.');
        }

        $builder->where('magang_id', $magang_id)
                ->update(['pembimbing_id' => $pembimbing_id]);

        if ($db->affectedRows() > 0) {
            return redirect()->back()->with('success', 'Pembimbing berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan pembimbing.');
        }
    }

    public function updatePembimbing($magang_id)
    {
        $pembimbing_id = $this->request->getPost('pembimbing_id');

        $this->magangModel->update($magang_id, [
            'pembimbing_id' => $pembimbing_id
        ]);

        return redirect()->back()->with('success', 'Pembimbing berhasil diperbarui.');
    }

    // public function getPesertaAjax()
    // {
    //     $request = service('request');
    //     $draw   = $request->getPost('draw');
    //     $start  = (int) $request->getPost('start');
    //     $length = (int) $request->getPost('length');
    //     $search = $request->getPost('search')['value'];

    //     // --- Subquery Tes Safety ---
    //     $subSafety = "
    //         (
    //             SELECT js1.magang_id, js1.nilai
    //             FROM jawaban_safety js1
    //             JOIN (
    //                 SELECT magang_id, MAX(created_at) AS max_created
    //                 FROM jawaban_safety
    //                 GROUP BY magang_id
    //             ) js2 
    //             ON js1.magang_id = js2.magang_id AND js1.created_at = js2.max_created
    //         ) AS js
    //     ";

    //     // --- Subquery RFID ---
    //     $subRfid = "
    //         (
    //             SELECT r1.*
    //             FROM rfid_assignment r1
    //             JOIN (
    //                 SELECT magang_id, MAX(assignment_id) AS max_created
    //                 FROM rfid_assignment
    //                 GROUP BY magang_id
    //             ) r2 
    //             ON r1.magang_id = r2.magang_id AND r1.assignment_id = r2.max_created
    //         ) AS ra
    //     ";

    //     $builder = $this->magangModel
    //         ->select("
    //             magang.magang_id, magang.status_berkas_lengkap, magang.tanggal_setujui_pernyataan,
    //             magang.laporan, magang.absensi,
    //             penilaian.nilai_disiplin, penilaian.nilai_kerajinan, penilaian.nilai_tingkahlaku,
    //             penilaian.nilai_kerjasama, penilaian.nilai_kreativitas, penilaian.nilai_kemampuankerja,
    //             penilaian.nilai_tanggungjawab, penilaian.nilai_penyerapan, penilaian.tgl_disetujui, penilaian.catatan,
    //             users.fullname, users.nisn_nim, unit_kerja.unit_kerja,
    //             magang.tanggal_masuk, magang.tanggal_selesai,
    //             rfid.rfid_no, js.nilai as nilai_tes,
    //             pembimbing.fullname AS nama_pembimbing,
    //             ra.status AS status_rfid,
    //             feedback.feedback_id
    //         ")
    //         ->join('users', 'users.id = magang.user_id')
    //         ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id', 'left')
    //         ->join($subSafety, 'js.relasi_id = magang.magang_id', 'left')
    //         ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
    //         ->join('feedback', 'feedback.magang_id = magang.magang_id', 'left')
    //         ->join($subRfid, 'ra.magang_id = magang.magang_id', 'left')
    //         ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
    //         ->join('users pembimbing', 'pembimbing.id = magang.pembimbing_id', 'left')
    //         ->where('magang.status_akhir', 'magang');

    //     if ($search) {
    //         $builder->groupStart()
    //             ->like('users.fullname', $search)
    //             ->orLike('users.nisn_nim', $search)
    //             ->orLike('unit_kerja.unit_kerja', $search)
    //             ->groupEnd();
    //     }

    //     $totalRecords = $builder->countAllResults(false);
    //     $builder->limit($length, $start);
    //     $data = $builder->get()->getResult();

    //     $result = [];
    //     $no = $start + 1;

    //     foreach ($data as $row) {

    //         // 🔹 Validasi Berkas
    //         $validasiBerkas = !empty($row->status_berkas_lengkap) && $row->status_berkas_lengkap === 'Y'
    //             ? '<span class="badge bg-success text-light">Valid</span>'
    //             : '<a href="' . base_url('admin/manage-kelengkapan-berkas/' . $row->magang_id) . '"><span class="badge bg-danger text-light">Tidak Valid</span></a>';

    //         // 🔹 Setuju Pernyataan
    //         $setujuPernyataan = !empty($row->tanggal_setujui_pernyataan)
    //             ? '<span class="badge bg-success text-light">Disetujui</span>'
    //             : '<span class="badge bg-danger text-light">Belum Setuju</span>';

    //         // 🔹 Laporan
    //         $laporan = !empty($row->laporan)
    //             ? '<a href="' . base_url('uploads/laporan/' . $row->laporan) . '" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i></a>
    //             <button class="btn btn-danger btn-sm btn-tolak-laporan" data-id="' . $row->magang_id . '" data-nama="' . esc($row->fullname) . '">
    //                 <i class="bi bi-x-circle"></i>
    //             </button>'
    //             : '<span class="badge bg-danger text-light">Belum Ada</span>';

    //         // 🔹 Absensi
    //         $absensi = !empty($row->absensi)
    //             ? '<a href="' . base_url('uploads/absensi/' . $row->absensi) . '" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-eye"></i></a>
    //             <button class="btn btn-danger btn-sm btn-tolak-absensi" data-id="' . $row->magang_id . '" data-nama="' . esc($row->fullname) . '">
    //                 <i class="bi bi-x-circle"></i>
    //             </button>'
    //             : '<span class="badge bg-danger text-light">Belum Ada</span>';

    //         // 🔹 Nilai Magang (rata-rata)
    //         $nilai = [
    //             $row->nilai_disiplin, $row->nilai_kerajinan, $row->nilai_tingkahlaku, $row->nilai_kerjasama,
    //             $row->nilai_kreativitas, $row->nilai_kemampuankerja, $row->nilai_tanggungjawab, $row->nilai_penyerapan
    //         ];
    //         $rata = array_sum(array_filter($nilai)) > 0 ? round(array_sum($nilai) / count(array_filter($nilai)), 2) : '-';
    //         $nilaiMagang = '<button class="btn btn-info btn-sm btn-detail-nilai" data-id="' . $row->magang_id . '"><strong>' . $rata . '</strong></button>';

    //         $result[] = [
    //             'no' => $no++,
    //             'fullname' => esc($row->fullname),
    //             'nisn_nim' => esc($row->nisn_nim),
    //             'unit_kerja' => esc($row->unit_kerja),
    //             'tanggal_masuk' => esc(format_tanggal_indonesia($row->tanggal_masuk)),
    //             'tanggal_selesai' => esc(format_tanggal_indonesia($row->tanggal_selesai)),
    //             'validasi_berkas' => $validasiBerkas,
    //             'setuju_pernyataan' => $setujuPernyataan,
    //             'laporan' => $laporan,
    //             'absensi' => $absensi,
    //             'nilai_magang' => $nilaiMagang,
    //             'rfid_no' => esc($row->rfid_no)?? '-',
    //             'nilai_tes' => $row->nilai_tes ?? '<span class="badge bg-danger text-light">Belum Tes</span>',
    //             'nama_pembimbing' => esc($row->nama_pembimbing) ?? '-',
    //             'status_rfid' => $row->status_rfid ?? '-',
    //             'feedback' => $row->feedback_id ? '<span class="badge bg-danger text-light">Sudah</span>' : '<span class="badge bg-danger text-light">Belum</span>',
    //             'aksi' => '<button class="btn btn-sm btn-info btn-detail-peserta" data-id="' . $row->magang_id . '">Detail</button>'
    //         ];
    //     }

    //     return $this->response->setJSON([
    //         'draw' => intval($draw),
    //         'recordsTotal' => $totalRecords,
    //         'recordsFiltered' => $totalRecords,
    //         'data' => $result
    //     ]);
    // }

    public function getDetailMagang($id)
    {
        $data = $this->magangModel
            ->select("
                magang.*,
                unit_kerja.unit_kerja,
                users.id as user_id, users.fullname, users.email,users.user_image,users.nisn_nim, users.no_hp, users.jenis_kelamin, users.alamat,
                users.province_id, users.city_id, users.domisili, users.provinceDom_id, users.cityDom_id,
                users.tingkat_pendidikan, users.instansi_id, users.jurusan_id, users.semester, 
                users.nilai_ipk, users.rfid_no, users.cv, users.proposal, users.surat_permohonan, users.tanggal_surat,
                users.no_surat, users.nama_pimpinan, users.jabatan, users.email_instansi,users.bpjs_kes, users.bpjs_tk, 
                users.buktibpjs_tk, users.ktp_kk, users.status,
                jurusan.nama_jurusan,
                instansi.nama_instansi,
                pembimbing.fullname as nama_pembimbing,
                province_ktp.province AS provinsi_ktp,
                province_dom.province AS provinsi_domisili,
                city_ktp.regency AS kota_ktp, 
                city_ktp.type AS tipe_kota_ktp,
                city_dom.regency AS kota_domisili,
                city_dom.type AS tipe_kota_domisili,
            ")
            ->join('users', 'users.id = magang.user_id')
            ->join('users pembimbing', 'pembimbing.id = magang.pembimbing_id', 'left') 
            ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id', 'left')
            ->join('jurusan', 'users.jurusan_id = jurusan.jurusan_id', 'left')
            ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
            ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
            ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
            ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
            ->join('instansi', 'users.instansi_id = instansi.instansi_id', 'left')
            ->where('magang.magang_id', $id)
            ->first();


        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }


    public function getDetailNilai($magang_id)
    {
        $data = $this->magangModel
            ->select('
                users.fullname, users.nisn_nim,
                penilaian.nilai_disiplin, penilaian.nilai_kerajinan, penilaian.nilai_tingkahlaku,
                penilaian.nilai_kerjasama, penilaian.nilai_kreativitas, penilaian.nilai_kemampuankerja,
                penilaian.nilai_tanggungjawab, penilaian.nilai_penyerapan, penilaian.catatan, penilaian.tgl_penilaian,
                unit_kerja.unit_kerja
            ')
            ->join('users', 'users.id = magang.user_id')
            ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->where('magang.magang_id', $magang_id)
            ->first();

        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }

        if (!empty($data['tgl_penilaian'])) {
            $data['tgl_penilaian_format'] = format_tanggal_indonesia_dengan_jam($data['tgl_penilaian']);
        } else {
            $data['tgl_penilaian_format'] = "-";
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getEditData($id)
    {
        $data = $this->magangModel
            ->select('magang.*, users.fullname')
            ->join('users', 'users.id = magang.user_id')
            ->where('magang.magang_id', $id)
            ->first();

        $unitList = $this->unitKerjaModel->findAll();

        if ($data) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data,
                'unitList' => $unitList
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }

    public function getAvailableRfid()
    {
        $rfidModel = new RfidModel();
        $available = $rfidModel->where('status', 'available')->findAll();
        return $this->response->setJSON($available);
    }

    public function setRFID()
    {
        $magangId = $this->request->getPost('magang_id');
        $rfidId   = $this->request->getPost('rfid_id');

        // Cari data magang
        $magang = $this->magangModel->find($magangId);

        if (!$magang) {
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        if ($magangId && $rfidId) {
            // Simpan ke tabel rfid_assignment
            $this->rfidAssignmentModel->insert([
                'magang_id'      => $magangId,
                'rfid_id'        => $rfidId,
                'tanggal_pinjam' => date('Y-m-d H:i:s'),
                'status'         => 'aktif', 
                'denda_bayar'    => 0
            ]);

            // Update status RFID jadi assigned
            $this->rfidModel->update($rfidId, ['status' => 'assigned']);

            return redirect()->back()->with('success', 'RFID berhasil diberikan.');
        }

        return redirect()->back()->with('error', 'Gagal menyimpan RFID.');
    }

    public function returnRFID()
    {
        $assignmentId = $this->request->getPost('assignment_id');
        $status       = $this->request->getPost('status'); 
        $newRfidId    = $this->request->getPost('new_rfid_id');

        $assignment = $this->rfidAssignmentModel->find($assignmentId);
        if (!$assignment) {
            return redirect()->back()->with('error', 'Data assignment tidak ditemukan.');
        }

        $rfidId   = $assignment['rfid_id'];
        $magangId = $assignment['magang_id'];

        // update assignment lama
       $updateData = [
            'status'          => $status,
            'tanggal_kembali' => date('Y-m-d H:i:s')
        ];
        if ($status === 'lost') {
            $updateData['denda_bayar']  = 1; 
            $updateData['tanggal_bayar'] = date('Y-m-d H:i:s'); 
        } 

        $this->rfidAssignmentModel->update($assignmentId, $updateData);

        // update rfid lama
        if ($status === 'returned') {
            $this->rfidModel->update($rfidId, ['status' => 'available']);
        } elseif ($status === 'lost') {
            $this->rfidModel->update($rfidId, ['status' => 'lost']);
        }

        // kalau hilang dan diganti RFID baru
        if ($status === 'lost'  && !empty($newRfidId)) {
            $this->rfidAssignmentModel->insert([
                'magang_id'     => $magangId,
                'rfid_id'       => $newRfidId,
                'tanggal_pinjam'=> date('Y-m-d H:i:s'),
                'status'        => 'aktif'
            ]);
            $this->rfidModel->update($newRfidId, ['status' => 'assigned']);
        }

        return redirect()->back()->with('success', 'Pengembalian RFID berhasil disimpan.');
    }

    public function updateMagang($id)
    {
        $data = [
            'tanggal_masuk'  => $this->request->getPost('tanggal_masuk'),
            'tanggal_selesai'=> $this->request->getPost('tanggal_selesai'),
            'unit_id'        => $this->request->getPost('unit_id'),
        ];

        $this->magangModel->update($id, $data);

        // Ambil data user + instansi terkait untuk email
        $db = \Config\Database::connect();
        $peserta = $db->table('magang')
            ->select('magang.*, users.email, users.fullname, users.email_instansi, instansi.nama_instansi, unit_kerja.unit_kerja')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('instansi', 'users.instansi_id = instansi.instansi_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->where('magang.magang_id', $id)
            ->get()
            ->getRow();

        if ($peserta) {
            $email = \Config\Services::email();
            $email->setTo($peserta->email);

            if (!empty($peserta->email_instansi) && filter_var($peserta->email_instansi, FILTER_VALIDATE_EMAIL)) {
                $email->setCC($peserta->email_instansi);
            }
            $unit_id = 44;
            $signature = getSignature($unit_id);
            $email->setSubject('Perubahan Jadwal / Unit Magang - PT Semen Padang');
            $email->setMailType('html');
            $email->setMessage(view('emails/perubahan_magang', [
                'nama'           => $peserta->fullname,
                'unit'           => $peserta->unit_kerja,
                'tanggal_masuk'  => $data['tanggal_masuk'],
                'tanggal_selesai'=> $data['tanggal_selesai'],
                'instansi'       => $peserta->nama_instansi,
                'signature' => $signature
            ]));

            if (!$email->send()) {
                log_message('error', "Gagal kirim email perubahan magang ID {$id}: " . print_r($email->printDebugger(), true));
            }
        }

    return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data magang berhasil diperbarui & email pemberitahuan dikirim.'
        ]);        
    }

    public function batalkanMagang()
    {
        $id = $this->request->getPost('id');
        $alasan = $this->request->getPost('alasan');

        if (!$id || !$alasan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        $db = \Config\Database::connect();
        $data = $db->table('magang')
            ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username, unit_kerja.unit_kerja')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
            ->where('magang.magang_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data magang tidak ditemukan']);
        }

        // Update status dan alasan
        $this->magangModel->update($id, [
            'status_akhir' => 'batal',
            'tanggal_selesai' => date('Y-m-d'),
            'alasan_batal' => $alasan,
        ]);

        // Kirim email
        $email = \Config\Services::email();
        $toEmail = $data->email;
        $ccEmail = $data->email_instansi;

        if (!empty($toEmail)) {
            $email->setTo($toEmail);

            if (!empty($ccEmail) && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                $email->setCC($ccEmail);
            }
            $unit_id = 44;
            $signature = getSignature($unit_id);
            $email->setSubject('Pemberitahuan Pembatalan Magang di PT Semen Padang');
            $email->setMailType('html');

            $email->setMessage(view('emails/batalkan_magang', [
                'nama'   => $data->fullname ?? $data->username,
                'unit'   => $data->unit_kerja ?? 'unit terkait',
                'alasan' => $alasan,
                'signature' => $signature
            ]));

            if (!$email->send()) {
                log_message('error', "Gagal kirim email pembatalan magang ID $id: " . print_r($email->printDebugger(), true));
            }
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function tolakLaporan($id)
    {
        // $magangId = $this->request->getPost('magang_id');
        $catatan  = $this->request->getPost('catatan');

        $db = \Config\Database::connect();
        $data = $db->table('magang')
            ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->where('magang.magang_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        // hapus file laporan kalau ada
        if (!empty($data->laporan)) {
            $filePath = FCPATH . 'uploads/laporan/' . $data->laporan;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // update database
        $this->magangModel->update($id, [
            'laporan' => null,
            'url_laporan' => null,
            'catatan_laporan' => $catatan
        ]);

        // kirim email
        $email = \Config\Services::email();
        $toEmail = $data->email;

        if (!empty($toEmail)) {
            $email->setTo($toEmail);
        }
        $unit_id = 44;
        $signature = getSignature($unit_id);
        $email->setSubject('Hasil Validasi Laporan Magang di PT Semen Padang');
        $email->setMailType('html');
        $email->setMessage(view('emails/laporan_tolak', [
            'nama'    => $data->fullname ?? $data->username,
            'catatan' => $catatan,
            'signature' => $signature
        ]));

        if (!$email->send()) {
            log_message('error', "Gagal kirim email tolak laporan ID $id: " . print_r($email->printDebugger(), true));
        }

        return redirect()->back()->with('success', 'Laporan berhasil ditolak dan email notifikasi dikirim.');
    }

    public function tolakAbsensi($id)
    {
        // $magangId = $this->request->getPost('magang_id');
        $catatan  = $this->request->getPost('catatan');

        $db = \Config\Database::connect();
        $data = $db->table('magang')
            ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->where('magang.magang_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        // hapus file absensi kalau ada
        if (!empty($data->absensi)) {
            $filePath = FCPATH . 'uploads/absensi/' . $data->absensi;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // update database
        $this->magangModel->update($id, [
            'absensi' => null,
            'url_absensi' => null,
            'catatan_absensi' => $catatan
        ]);

        // kirim email
        $email = \Config\Services::email();
        $toEmail = $data->email;

        if (!empty($toEmail)) {
            $email->setTo($toEmail);
        }
        $unit_id = 44;
        $signature = getSignature($unit_id);
        $email->setSubject('Hasil Validasi Absensi Magang di PT Semen Padang');
        $email->setMailType('html');
        $email->setMessage(view('emails/absensi_tolak', [
            'nama'    => $data->fullname ?? $data->username,
            'catatan' => $catatan,
            'signature' => $signature
        ]));

        if (!$email->send()) {
            log_message('error', "Gagal kirim email tolak absensi ID $id: " . print_r($email->printDebugger(), true));
        }

        return redirect()->back()->with('success', 'Absensi berhasil ditolak dan email notifikasi dikirim.');
    }

    public function bukaUpload($id)
    {
        $this->magangModel->update($id, [
            'allow_upload' => 1
        ]);

        return redirect()->back()->with('success', 'Akses upload dibuka.');
    }

    public function tutupUpload($id)
    {
        $this->magangModel->update($id, [
            'allow_upload' => 0
        ]);

        return redirect()->back()->with('success', 'Akses upload ditutup.');
    }

    public function finalisasi($magangId)
    {
        $magang = $this->magangModel->find($magangId);

        if (!$magang) {
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        
       $this->magangModel->update($magangId, [
            'finalisasi' => date('Y-m-d H:i:s'), 
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal Finalisasi.');
        }

        return redirect()->back()->with('success', 'Finalisasi Berhasil');
    }


    public function alumniMagang()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');

        $builder = $this->magangModel->select('
                magang.*,
                unit_kerja.unit_kerja,
                users.*,
                jurusan.nama_jurusan,
                instansi.nama_instansi,
                penilaian.*,
                province_ktp.province AS provinsi_ktp,
                province_dom.province AS provinsi_domisili,
                city_ktp.regency AS kota_ktp, 
                city_ktp.type AS tipe_kota_ktp,
                city_dom.regency AS kota_domisili,
                city_dom.type AS tipe_kota_domisili,
                MAX(jawaban_safety.nilai) AS nilai_maksimal,
                MAX(jawaban_safety.created_at) AS tanggal_terakhir,
                MAX(jawaban_safety.percobaan_ke) AS percobaan_terakhir,
                CASE 
                    WHEN MAX(jawaban_safety.nilai) IS NULL THEN "Belum Tes"
                    WHEN MAX(jawaban_safety.nilai) >= 70 THEN "Lulus"
                    ELSE "Belum Lulus"
                END AS status_tes,
                rfid.rfid_no, rfid.id_rfid, 
                ra.assignment_id, ra.status AS status_rfid, ra.tanggal_kembali, ra.tanggal_bayar, 
                feedback.feedback_id
            ')
            ->join('users', 'users.id = magang.user_id')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
            ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
            ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
            ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
            ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
            ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
            ->join('jawaban_safety', 'magang.magang_id = jawaban_safety.relasi_id AND jawaban_safety.tipe = "magang"', 'left')
            ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
            ->join('feedback', 'feedback.magang_id = magang.magang_id', 'left')
            ->join("(
                SELECT r1.*
                FROM rfid_assignment r1
                JOIN (
                    SELECT relasi_id, MAX(tanggal_pinjam) AS max_created
                    FROM rfid_assignment
                    WHERE tipe = 'magang'
                    GROUP BY relasi_id
                ) r2 ON r1.relasi_id = r2.relasi_id AND r1.tanggal_pinjam = r2.max_created
            ) AS ra", 'ra.relasi_id = magang.magang_id', 'left')
            ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
            ->where('magang.status_akhir', 'lulus')
            ->groupBy('magang.magang_id');



        if (!empty($bulan)) {
            $builder->where('MONTH(magang.tanggal_masuk)', $bulan);
        }

        if (!empty($tahun)) {
            $builder->where('YEAR(magang.tanggal_masuk)', $tahun);
        }

        $data = $builder->findAll();
        $unitList = $this->unitKerjaModel->findAll();
        $rfid = $this->rfidModel->findAll();

        return view('admin/kelola_alumni', ['data' => $data, 'unitList' => $unitList, 'rfidList' => $rfid]);
    }

    // public function cetakSertifikat($id, $saveToFile = false)
    // {
    //     $userRow = $this->magangModel->select('user_id')
    //                          ->where('magang_id', $id)
    //                          ->first();

    //     $userId = $userRow['user_id']; 
    //     // $userId = user_id();

    //     // Ambil data user & magang terbaru yang lulus
    //     $user = $this->userModel->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
    //                             ->join('instansi', 'instansi.instansi_id = users.instansi_id')
    //                             ->find($userId);
    //     $magang = $this->magangModel->join('unit_kerja', 'unit_kerja.unit_id=magang.unit_id')
    //         ->where('user_id', $userId)
    //         ->where('status_akhir', 'lulus')
    //         ->orderBy('magang_id', 'DESC')
    //         ->first();

    //     if (!$magang) {
    //         return redirect()->back()->with('error', 'Tidak ada magang aktif untuk dicetak sertifikat.');
    //     }

    //     $penilaian = $this->penilaianModel->where('magang_id', $magang['magang_id'])->first();

    //     if (!$penilaian || $magang['ka_unit_approve'] != 1) {
    //         return redirect()->back()->with('error', 'Sertifikat belum bisa diunduh.');
    //     }

    //     // Hitung total & rata-rata
    //     $totalNilai = $penilaian['nilai_disiplin']
    //         + $penilaian['nilai_kerajinan']
    //         + $penilaian['nilai_tingkahlaku']
    //         + $penilaian['nilai_kerjasama']
    //         + $penilaian['nilai_kreativitas']
    //         + $penilaian['nilai_kemampuankerja']
    //         + $penilaian['nilai_tanggungjawab']
    //         + $penilaian['nilai_penyerapan'];

    //     $rataRata = round($totalNilai / 8, 0); 

    //     // Tentukan kategori
    //     if ($rataRata >= 90) $kategori = 'Baik Sekali';
    //     elseif ($rataRata >= 80) $kategori = 'Baik';
    //     elseif ($rataRata >= 70) $kategori = 'Cukup';
    //     elseif ($rataRata >= 60) $kategori = 'Kurang';
    //     else $kategori = 'Sangat Kurang';

    //     $kepala = $this->unitUserModel
    //     ->select('users.fullname, unit_kerja.unit_kerja, users.eselon, users.tanda_tangan')
    //     ->join('users', 'users.id = unit_user.user_id')
    //     ->join('unit_kerja', 'unit_kerja.unit_id = unit_user.unit_id')
    //     ->where('unit_user.unit_id', 44)
    //     ->where('users.eselon', '2')
    //     ->first();

    //     // --- Rapikan Fullname ---
    //     $fullname = $kepala['fullname'];

    //     // Hilangkan gelar umum (awal / akhir nama)
    //     $fullname = preg_replace('/\b(Drs?|Ir|H|S\.Kom|M\.Kom|M\.Sc|M\.M|PhD|S\.Pd|M\.Pd|S\.T|M\.T)\b\.?/i', '', $fullname);

    //     // Hilangkan gelar belakang seperti: ", S.Kom", ", M.Kom"
    //     $fullname = preg_replace('/,\s*[A-Za-z\.]+$/i', '', $fullname);

    //     // Rapikan spasi
    //     $fullname = trim($fullname);

    //     // Kapital awal setiap kata
    //     $fullname = ucwords(strtolower($fullname));

    //     $namaKepala = $fullname;

    //     $unitkerja = $kepala['unit_kerja'];

    //     // Hilangkan kata "Unit" di awal
    //     $unitkerja = preg_replace('/^Unit\s*/i', '', $unitkerja);

    //     // Kapital awal kata (kalau mau)
    //     $unitkerja = ucwords(strtolower($unitkerja));

    //     $unitKepala = $unitkerja;



    //     // ================== Nomor Sertifikat ==================
    //     $tahunSekarang = date('Y');
    //     $bulanSekarang = date('m');

    //     // cek apakah sudah ada nomor sertifikat untuk magang ini
    //     $sertifikat = $this->sertifikatModel
    //         ->where('magang_id', $magang['magang_id'])
    //         ->first();

    //     if (!$sertifikat) {
    //         // ambil nomor urut terakhir tahun berjalan
    //         $last = $this->sertifikatModel
    //             ->where('tahun', $tahunSekarang)
    //             ->orderBy('nomor', 'DESC')
    //             ->first();

    //         $nextNumber = $last ? intval($last['nomor']) + 1 : 1;

    //         // simpan ke tabel sertifikat
    //         $this->sertifikatModel->insert([
    //             'magang_id' => $magang['magang_id'],
    //             'nomor'     => $nextNumber,
    //             'tahun'     => $tahunSekarang,
    //         ]);
    //     } else {
    //         $nextNumber = $sertifikat['nomor'];
    //     }

    //     // format nomor sertifikat
    //     $noUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    //     $noSertifikat = "{$noUrut}/MAGANG/SP/{$bulanSekarang}.{$tahunSekarang}";


    //     // Inisialisasi TCPDF
    //     $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
    //     $pdf->SetPrintHeader(false);
    //     $pdf->SetPrintFooter(false);
    //     $pdf->SetMargins(0, 0, 0);
    //     $pdf->SetAutoPageBreak(TRUE, 0);

    //     // ================= Halaman 1 =================
    //     $pdf->AddPage();
    //     $cover1 = FCPATH . 'assets/img/page1.png';
    //     $pdf->Image($cover1, 0, 0, 210, 297, '', '', '', false, 300);

    //     // Nomor sertifikat
    //     $pdf->SetFont('times', 'I', 14);
    //     $pdf->SetXY(11, 63.5); 
    //     $pdf->Cell(210, 10,": " .$noSertifikat, 0, 1, 'C');

    //     // Nama peserta
    //     $pdf->SetFont('times', 'B', 24);
    //     $pdf->SetXY(0, 90);
    //     $pdf->Cell(210, 18, $user->fullname ?? $user->username, 0, 1, 'C');

    //     $pdf->SetFont('times', '', 16);
    //     $pdf->Cell(210, 9, ($user->nisn_nim ?? '-'), 0, 1, 'C');
    //     $pdf->Cell(210, 9, ($user->nama_jurusan ?? '-'), 0, 1, 'C');
    //     $pdf->Cell(210, 9, ($user->nama_instansi ?? '-'), 0, 1, 'C');

    //     // Kalimat keterangan
    //     $pdf->Ln(10);
    //     $pdf->SetFont('times', '', 14);
    //     $marginKiri = 25;
    //     $marginKanan = 25;
    //     $halamanLebar = $pdf->GetPageWidth();
    //     $lebarText = $halamanLebar - $marginKiri - $marginKanan;
    //     $pdf->SetX($marginKiri);

    //     $teks = "Telah selesai melakukan kerja praktek di " .
    //         ($magang['unit_kerja'] ?? '-') . " PT Semen Padang " .
    //         "dari tanggal " . format_tanggal_indonesia($magang['tanggal_masuk']) .
    //         " s/d " . format_tanggal_indonesia($magang['tanggal_selesai']) .
    //         " dengan hasil :";

    //     $pdf->MultiCell($lebarText, 8, $teks, 0, 'C');

    //     //Kategori
    //     $pdf->SetFont('times', 'B', 18);
    //     $pdf->SetXY(0, 170);
    //     $pdf->Cell(210, 10, $kategori, 0, 1, 'C');


    //     // Ambil tanggal approve
    //     $tanggalApprove = !empty($magang['tanggal_approve']) 
    //         ? format_tanggal_indonesia($magang['tanggal_approve']) 
    //         : '-';
    //     // Tambah Stempel
    //     $stempelPath = FCPATH . 'assets/img/stempel.png';
    //     if (file_exists($stempelPath)) {
    //         $pdf->Image($stempelPath, 17, 210, 45, 0, 'PNG', '', '', false, 300);
    //     }
    //     // Posisi mulai 
    //     $pdf->SetFont('times', '', 16);
    //     $pdf->SetXY(30, 200);
    //     $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');

    //     $pdf->SetFont('times', 'B', 16);
    //     $pdf->SetX(30);
    //     $pdf->Cell(0, 8, $unitKepala, 0, 1, 'L');

    //     // Tambahkan tanda tangan 
    //     $ttdPath = FCPATH . 'uploads/tanda-tangan/'. $kepala['tanda_tangan']; 
    //     if (file_exists($ttdPath)) {
    //         $pdf->Image($ttdPath, 30, 215, 45, 0, '', '', '', false, 300);

    //     }

    //     // Nama pejabat
    //     $pdf->SetFont('times', 'B', 16);
    //     $pdf->SetXY(30, 235);
    //     $pdf->Cell(0, 8, $namaKepala, 0, 1, 'L');

    //     $pdf->SetFont('times', '', 14);
    //     $pdf->SetX(30);
    //     $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');


    //     // ================= Halaman 2 =================
    //     $pdf->AddPage();
    //     $cover2 = FCPATH . 'assets/img/page2.png';
    //     $pdf->Image($cover2, 0, 0, 210, 297, '', '', '', false, 300);

    //     $pdf->SetFont('times', '', 16);

    //     $startY = 86;
    //     $stepY  = 12.5;

    //     $nilaiList = [
    //         $penilaian['nilai_disiplin'],
    //         $penilaian['nilai_kerajinan'],
    //         $penilaian['nilai_tingkahlaku'],
    //         $penilaian['nilai_kerjasama'],
    //         $penilaian['nilai_kreativitas'],
    //         $penilaian['nilai_kemampuankerja'],
    //         $penilaian['nilai_tanggungjawab'],
    //         $penilaian['nilai_penyerapan'],
    //     ];

    //     // Fungsi terbilang khusus 0 - 100
    //     function terbilang($angka) {
    //         $angka = intval($angka);
    //         if ($angka > 100) return "Seratus"; 

    //         $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima",
    //                 "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

    //         if ($angka == 0) return "Nol";
    //         elseif ($angka < 12) return $baca[$angka];
    //         elseif ($angka < 20) return $baca[$angka - 10] . " Belas";
    //         elseif ($angka < 100) {
    //             $puluh = intval($angka / 10);
    //             $sisa  = $angka % 10;
    //             $hasil = $baca[$puluh] . " Puluh";
    //             if ($sisa > 0) $hasil .= " " . $baca[$sisa];
    //             return $hasil;
    //         } else {
    //             return "Seratus";
    //         }
    //     }

    //     foreach ($nilaiList as $i => $nilai) {
    //         $y = $startY + ($i * $stepY);

    //         // angka
    //         $pdf->SetXY(97, $y);
    //         $pdf->Cell(20, 10, $nilai, 0, 0, 'C');

    //         // huruf
    //         $pdf->SetXY(123, $y);
    //         $pdf->Cell(40, 10, terbilang($nilai), 0, 0, 'L');
    //     }

    //     // Rata-rata + kategori
    //     $pdf->SetXY(97, $startY + (8 * $stepY));
    //     $pdf->Cell(20, 10, $rataRata, 0, 0, 'C');
    //     $pdf->SetXY(123, $startY + (8 * $stepY));
    //     $pdf->Cell(40, 10, terbilang($rataRata), 0, 0, 'L');

    //     // tampilkan kategori full
    //     $pdf->SetXY(123, $startY + (8 * $stepY) + 12.5);
    //     $pdf->Cell(60, 10, $kategori, 0, 0, 'L');

    //     //tambah stempel
    //     if (file_exists($stempelPath)) {
    //         $pdf->Image($stempelPath, 110, 215, 45, 0, 'PNG', '', '', false, 300);
    //     }

    //     //TTD pojok kanan
    //     $pdf->SetFont('times', '', 16);
    //     $pdf->SetXY(105, 215);
    //     $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');
    //     $pdf->SetFont('times', 'B', 16);
    //     $pdf->SetX(105);
    //     $pdf->Cell(0, 8, $unitKepala, 0, 1, 'L');

    //     // Tambahkan tanda tangan 
    //     if (file_exists($ttdPath)) {
    //         $pdf->Image($ttdPath, 105, 228, 45, 0, '', '', '', false, 300);

    //     }

    //     // Nama pejabat
    //     $pdf->SetFont('times', 'B', 16);
    //     $pdf->SetXY(105, 245);
    //     $pdf->Cell(0, 8, $namaKepala, 0, 1, 'L');

    //     $pdf->SetFont('times', '', 14);
    //     $pdf->SetX(105);
    //     $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');

    //     // ================= Output =================
    //     $fileName = 'sertifikat-magang-' . url_title($user->fullname ?? $user->nama, '-', true) . '-' . date('YmdHis') . '.pdf';

    //     if ($saveToFile) {
    //         $filePath = WRITEPATH . 'uploads/' . $fileName;
    //         $pdf->Output($filePath, 'F');
    //         return $filePath;
    //     } else {
    //         $this->response->setContentType('application/pdf');
    //         $pdf->Output($fileName, 'I');
    //         exit;
    //     }
    // }

    public function cetakSertifikat($id)
    {
        $userRow = $this->magangModel->select('user_id')
                             ->where('magang_id', $id)
                             ->first();

        $userId = $userRow['user_id'];

        // ================= DATA USER & MAGANG =================
        $user = $this->userModel
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id')
            ->find($userId);

        $magang = $this->magangModel
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->where('user_id', $userId)
            ->where('status_akhir', 'lulus')
            ->orderBy('magang_id', 'DESC')
            ->first();

        if (!$magang) {
            return redirect()->back()->with('error', 'Tidak ada magang lulus.');
        }

        $penilaian = $this->penilaianModel
            ->where('magang_id', $magang['magang_id'])
            ->first();

        if (!$penilaian || $magang['ka_unit_approve'] != 1) {
            return redirect()->back()->with('error', 'Sertifikat belum bisa dicetak.');
        }

        // ================= HITUNG NILAI =================
        $nilaiList = [
            $penilaian['nilai_disiplin'],
            $penilaian['nilai_kerajinan'],
            $penilaian['nilai_tingkahlaku'],
            $penilaian['nilai_kerjasama'],
            $penilaian['nilai_kreativitas'],
            $penilaian['nilai_kemampuankerja'],
            $penilaian['nilai_tanggungjawab'],
            $penilaian['nilai_penyerapan'],
        ];

        $rataRata = round(array_sum($nilaiList) / count($nilaiList));

        if ($rataRata >= 90) $kategori = 'Baik Sekali';
        elseif ($rataRata >= 80) $kategori = 'Baik';
        elseif ($rataRata >= 70) $kategori = 'Cukup';
        elseif ($rataRata >= 60) $kategori = 'Kurang';
        else $kategori = 'Sangat Kurang';

        // ================= SERTIFIKAT =================
        $tahun = date('Y');
        $bulan = date('m');

        $sertifikat = $this->sertifikatModel
            ->where('magang_id', $magang['magang_id'])
            ->first();

        // === Jika BELUM ADA DATA sertifikat → buat
        if (!$sertifikat) {
            $last = $this->sertifikatModel
                ->where('tahun', $tahun)
                ->orderBy('nomor', 'DESC')
                ->first();

            $nomor = $last ? $last['nomor'] + 1 : 1;
            $qrToken = bin2hex(random_bytes(20));

            $this->sertifikatModel->insert([
                'magang_id' => $magang['magang_id'],
                'nomor'     => $nomor,
                'tahun'     => $tahun,
                'qr_token'  => $qrToken,
            ]);

            $sertifikat = $this->sertifikatModel
                ->where('magang_id', $magang['magang_id'])
                ->first();
        }

        // === Lazy QR token
        if (empty($sertifikat['qr_token'])) {
            $qrToken = bin2hex(random_bytes(20));
            $this->sertifikatModel->update($sertifikat['sertifikat_id'], [
                'qr_token' => $qrToken
            ]);
            $sertifikat['qr_token'] = $qrToken;
        }

        // ================= FILE PATH =================
        $fileName = 'sertifikat-' . $sertifikat['qr_token'] . '.pdf';
        $filePath = FCPATH . 'uploads/sertifikat/pdf/' . $fileName;

        // ================= JIKA FILE SUDAH ADA =================
        if (!empty($sertifikat['file_sertifikat']) && file_exists($filePath)) {
            return $this->response
                ->setHeader('Content-Type', 'application/pdf')
                ->setBody(file_get_contents($filePath));
        }

        $kepala = $this->unitUserModel
            ->select('users.fullname, unit_kerja.unit_kerja, users.eselon, users.tanda_tangan')
            ->join('users', 'users.id = unit_user.user_id')
            ->join('unit_kerja', 'unit_kerja.unit_id = unit_user.unit_id')
            ->where('unit_user.unit_id', 44)
            ->where('users.eselon', '2')
            ->first();

        // --- Rapikan Fullname ---
        $fullname = $kepala['fullname'];

        // Hilangkan gelar umum (awal / akhir nama)
        $fullname = preg_replace('/\b(Drs?|Ir|H|S\.Kom|M\.Kom|M\.Sc|M\.M|PhD|S\.Pd|M\.Pd|S\.T|M\.T)\b\.?/i', '', $fullname);

        // Hilangkan gelar belakang seperti: ", S.Kom", ", M.Kom"
        $fullname = preg_replace('/,\s*[A-Za-z\.]+$/i', '', $fullname);

        // Rapikan spasi
        $fullname = trim($fullname);

        // Kapital awal setiap kata
        $fullname = ucwords(strtolower($fullname));

        $namaKepala = $fullname;

        $unitkerja = $kepala['unit_kerja'];

        // Hilangkan kata "Unit" di awal
        $unitkerja = preg_replace('/^Unit\s*/i', '', $unitkerja);

        // Kapital awal kata (kalau mau)
        $unitkerja = ucwords(strtolower($unitkerja));

        $unitKepala = $unitkerja;


        // ================= NOMOR SERTIFIKAT =================
        $noUrut = str_pad($sertifikat['nomor'], 4, '0', STR_PAD_LEFT);
        $noSertifikat = "{$noUrut}/MAGANG/SP/{$bulan}.{$tahun}";

        // ================= GENERATE QR =================
        $qrUrl  = base_url('sertifikat/verify/' . $sertifikat['qr_token']);
        // $localIp = '192.168.229.8';
        // $qrUrl = "http://{$localIp}/magangSPNew/public/sertifikat/verify/" . $sertifikat['qr_token'];
        $qrPath = FCPATH . 'uploads/sertifikat/qr/' . $sertifikat['qr_token'] . '.png';
        $logoPath = FCPATH . 'assets/img/SP_logo.png';

        if (!file_exists($logoPath)) {
            throw new \RuntimeException('Logo QR tidak ditemukan: ' . $logoPath);
        }
        if (!file_exists($qrPath)) {
            if (!is_dir(dirname($qrPath))) {
                mkdir(dirname($qrPath), 0775, true);
            }
        

            $builder = new QrBuilder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $qrUrl,

                // ❗ TIDAK BOLEH NULL
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10,

                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                foregroundColor: new Color(0, 0, 0),
                backgroundColor: new Color(255, 255, 255),

                labelText: '',
                

                // LOGO
                logoPath: $logoPath,
                logoResizeToWidth: 70,
                logoResizeToHeight: null,
                logoPunchoutBackground: true
            );

            $result = $builder->build();
            $result->saveToFile($qrPath);


        }

        // ================= TCPDF =================
        $pdf = new \TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(TRUE, 0);

        // ================= Halaman 1 =================
            $pdf->AddPage();
            $cover1 = FCPATH . 'assets/img/page1.png';
            $pdf->Image($cover1, 0, 0, 210, 297, '', '', '', false, 300);

            // Nomor sertifikat
            $pdf->SetFont('times', 'I', 14);
            $pdf->SetXY(11, 63.5); 
            $pdf->Cell(210, 10,": " .$noSertifikat, 0, 1, 'C');

            // Nama peserta
            $pdf->SetFont('times', 'B', 24);
            $pdf->SetXY(0, 90);
            $pdf->Cell(210, 18, $user->fullname ?? $user->nama, 0, 1, 'C');

            $pdf->SetFont('times', '', 16);
            $pdf->Cell(210, 9, ($user->nisn_nim ?? '-'), 0, 1, 'C');
            $pdf->Cell(210, 9, ($user->nama_jurusan ?? '-'), 0, 1, 'C');
            $pdf->Cell(210, 9, ($user->nama_instansi ?? '-'), 0, 1, 'C');

            // Kalimat keterangan
            $pdf->Ln(10);
            $pdf->SetFont('times', '', 14);
            $marginKiri = 25;
            $marginKanan = 25;
            $halamanLebar = $pdf->GetPageWidth();
            $lebarText = $halamanLebar - $marginKiri - $marginKanan;
            $pdf->SetX($marginKiri);

            $teks = "Telah selesai melakukan kerja praktek di " .
                ($magang['unit_kerja'] ?? '-') . " PT Semen Padang " .
                "dari tanggal " . format_tanggal_indonesia($magang['tanggal_masuk']) .
                " s/d " . format_tanggal_indonesia($magang['tanggal_selesai']) .
                " dengan hasil :";

            $pdf->MultiCell($lebarText, 8, $teks, 0, 'C');

            //Kategori
            $pdf->SetFont('times', 'B', 18);
            $pdf->SetXY(0, 170);
            $pdf->Cell(210, 10, $kategori, 0, 1, 'C');


            // Ambil tanggal approve
            $tanggalApprove = !empty($magang['tanggal_approve']) 
                ? format_tanggal_indonesia($magang['tanggal_approve']) 
                : '-';
            // Tambah Stempel
            $stempelPath = FCPATH . 'assets/img/stempel.png';
            if (file_exists($stempelPath)) {
                // X, Y, Width
                $pdf->Image($stempelPath, 17, 210, 45, 0, 'PNG', '', '', false, 300);
            }
            // Posisi mulai (pojok kiri bawah, misal 190mm dari atas)
            $pdf->SetFont('times', '', 16);
            $pdf->SetXY(30, 200);
            $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');

            $pdf->SetFont('times', 'B', 16);
            $pdf->SetX(30);
            $pdf->Cell(0, 8, $unitKepala, 0, 1, 'L');

            // Tambahkan tanda tangan (PNG/JPG transparan lebih bagus)
            $ttdPath = FCPATH . 'uploads/tanda-tangan/'. $kepala['tanda_tangan']; // ganti dengan path tanda tanganmu
            if (file_exists($ttdPath)) {
                $pdf->Image($ttdPath, 30, 215, 45, 0, '', '', '', false, 300);

            }

            // Nama pejabat
            $pdf->SetFont('times', 'B', 16);
            $pdf->SetXY(30, 235);
            $pdf->Cell(0, 8, $namaKepala, 0, 1, 'L');

            $pdf->SetFont('times', '', 14);
            $pdf->SetX(30);
            $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');

            // === TEKS DI ATAS QR
            $pdf->SetFont('times', 'I', 9);   // font kecil & miring
            $pdf->SetTextColor(0, 0, 0);

            // Posisi X sama dengan QR, Y sedikit di atas QR
            $pdf->SetXY(160, 245);
            $pdf->Cell(30, 6, 'Scan me for validate', 0, 0, 'C');

            // === QR KANAN BAWAH HALAMAN 1
            $pdf->Image($qrPath, 160, 250, 30, 30, 'PNG');

            // ================= Halaman 2 =================
            $pdf->AddPage();
            $cover2 = FCPATH . 'assets/img/page2.png';
            $pdf->Image($cover2, 0, 0, 210, 297, '', '', '', false, 300);

            $pdf->SetFont('times', '', 16);

            $startY = 86;
            $stepY  = 12.5;

            $nilaiList = [
                $penilaian['nilai_disiplin'],
                $penilaian['nilai_kerajinan'],
                $penilaian['nilai_tingkahlaku'],
                $penilaian['nilai_kerjasama'],
                $penilaian['nilai_kreativitas'],
                $penilaian['nilai_kemampuankerja'],
                $penilaian['nilai_tanggungjawab'],
                $penilaian['nilai_penyerapan'],
            ];

            // Fungsi terbilang khusus 0 - 100
            function terbilang($angka) {
                $angka = intval($angka);
                if ($angka > 100) return "Seratus"; // mentok 100

                $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima",
                        "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

                if ($angka == 0) return "Nol";
                elseif ($angka < 12) return $baca[$angka];
                elseif ($angka < 20) return $baca[$angka - 10] . " Belas";
                elseif ($angka < 100) {
                    $puluh = intval($angka / 10);
                    $sisa  = $angka % 10;
                    $hasil = $baca[$puluh] . " Puluh";
                    if ($sisa > 0) $hasil .= " " . $baca[$sisa];
                    return $hasil;
                } else {
                    return "Seratus";
                }
            }

            foreach ($nilaiList as $i => $nilai) {
                $y = $startY + ($i * $stepY);

                // angka
                $pdf->SetXY(97, $y);
                $pdf->Cell(20, 10, $nilai, 0, 0, 'C');

                // huruf
                $pdf->SetXY(123, $y);
                $pdf->Cell(40, 10, terbilang($nilai), 0, 0, 'L');
            }

            // Rata-rata + kategori
            $pdf->SetXY(97, $startY + (8 * $stepY));
            $pdf->Cell(20, 10, $rataRata, 0, 0, 'C');
            $pdf->SetXY(123, $startY + (8 * $stepY));
            $pdf->Cell(40, 10, terbilang($rataRata), 0, 0, 'L');

            // tampilkan kategori full
            $pdf->SetXY(123, $startY + (8 * $stepY) + 12.5);
            $pdf->Cell(60, 10, $kategori, 0, 0, 'L');

            //tambah stempel
            if (file_exists($stempelPath)) {
                $pdf->Image($stempelPath, 110, 215, 45, 0, 'PNG', '', '', false, 300);
            }
            //TTD pojok kanan
            $pdf->SetFont('times', '', 16);
            $pdf->SetXY(105, 215);
            $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');
            $pdf->SetFont('times', 'B', 16);
            $pdf->SetX(105);
            $pdf->Cell(0, 8, $unitKepala, 0, 1, 'L');

            
            // Tambahkan tanda tangan (PNG/JPG transparan lebih bagus)
            $ttdPath = FCPATH . 'uploads/tanda-tangan/'.$kepala['tanda_tangan']; // ganti dengan path tanda tanganmu
            if (file_exists($ttdPath)) {
                $pdf->Image($ttdPath, 105, 228, 45, 0, '', '', '', false, 300);

            }

            // Nama pejabat
            $pdf->SetFont('times', 'B', 16);
            $pdf->SetXY(105, 245);
            $pdf->Cell(0, 8, $namaKepala, 0, 1, 'L');

            $pdf->SetFont('times', '', 14);
            $pdf->SetX(105);
            $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');

        // ================= SIMPAN PDF =================
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0775, true);
        }

        $pdf->Output($filePath, 'F');

        $this->sertifikatModel->update($sertifikat['sertifikat_id'], [
            'file_sertifikat' => $fileName
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setBody(file_get_contents($filePath));
    }

    public function exportPeserta()
    {
        // ambil filter 
        $bulanMasuk = $this->request->getGet('bulan_masuk');
        $bulanKeluar = $this->request->getGet('bulan_keluar');
        $tahun = $this->request->getGet('tahun');

        // ambil data 
        $builder = $this->magangModel->select('users.fullname, users.nisn_nim, users.tingkat_pendidikan, jurusan.nama_jurusan, instansi.nama_instansi, 
                                            unit_kerja.unit_kerja, magang.tanggal_masuk, magang.tanggal_selesai, magang.durasi')
                                        ->join('users', 'users.id = magang.user_id')
                                        ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
                                        ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id','left')
                                        ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
                                        ->where('magang.status_akhir', 'magang');


        if ($bulanMasuk) {
        $builder->where('MONTH(tanggal_masuk)', $bulanMasuk);
        }
        if ($bulanKeluar) {
            $builder->where('MONTH(tanggal_selesai)', $bulanKeluar);
        }
        if ($tahun) {
            $builder->where('YEAR(tanggal_masuk)', $tahun);
        }
        $data = $builder->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'NIM/NISN');
        $sheet->setCellValue('C1', 'Tingkatan');
        $sheet->setCellValue('D1', 'Jurusan');
        $sheet->setCellValue('E1', 'Nama PT/SMK');
        $sheet->setCellValue('F1', 'Unit Kerja');
        $sheet->setCellValue('G1', 'Tanggal Masuk');
        $sheet->setCellValue('H1', 'Tanggal Selesai');
        $sheet->setCellValue('I1', 'Durasi');

        // Isi data
        $row = 2;
        foreach ($data as $d) {
            $sheet->setCellValue('A' . $row, $d['fullname']);
            $sheet->setCellValue('B' . $row, $d['nisn_nim']);
            $sheet->setCellValue('C' . $row, $d['tingkat_pendidikan']);
            $sheet->setCellValue('D' . $row, $d['nama_jurusan']);
            $sheet->setCellValue('E' . $row, $d['nama_instansi']);
            $sheet->setCellValue('F' . $row, $d['unit_kerja']);
            $sheet->setCellValue('G' . $row, date('d-m-Y', strtotime($d['tanggal_masuk'])));
            $sheet->setCellValue('H' . $row, date('d-m-Y', strtotime($d['tanggal_selesai'])));
            $sheet->setCellValue('I' . $row, $d['durasi']);
            $row++;
        }


        // Download file
        $filename = 'data_peserta_magang_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function exportAlumni()
    {
        // ambil filter 
        $bulanMasuk = $this->request->getGet('bulan_masuk');
        $bulanKeluar = $this->request->getGet('bulan_keluar');
        $tahun = $this->request->getGet('tahun');

        // ambil data 
        $builder = $this->magangModel->select('users.fullname, users.nisn_nim, users.tingkat_pendidikan, jurusan.nama_jurusan, instansi.nama_instansi, 
                                            unit_kerja.unit_kerja, magang.tanggal_masuk, magang.tanggal_selesai, magang.durasi')
                                        ->join('users', 'users.id = magang.user_id')
                                        ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
                                        ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id','left')
                                        ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
                                        ->where('magang.status_akhir', 'lulus');


        if ($bulanMasuk) {
        $builder->where('MONTH(tanggal_masuk)', $bulanMasuk);
        }
        if ($bulanKeluar) {
            $builder->where('MONTH(tanggal_selesai)', $bulanKeluar);
        }
        if ($tahun) {
            $builder->where('YEAR(tanggal_masuk)', $tahun);
        }
        $data = $builder->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'NIM/NISN');
        $sheet->setCellValue('C1', 'Tingkatan');
        $sheet->setCellValue('D1', 'Jurusan');
        $sheet->setCellValue('E1', 'Nama PT/SMK');
        $sheet->setCellValue('F1', 'Unit Kerja');
        $sheet->setCellValue('G1', 'Tanggal Masuk');
        $sheet->setCellValue('H1', 'Tanggal Selesai');
        $sheet->setCellValue('I1', 'Durasi');

        // Isi data
        $row = 2;
        foreach ($data as $d) {
            $sheet->setCellValue('A' . $row, $d['fullname']);
            $sheet->setCellValue('B' . $row, $d['nisn_nim']);
            $sheet->setCellValue('C' . $row, $d['tingkat_pendidikan']);
            $sheet->setCellValue('D' . $row, $d['nama_jurusan']);
            $sheet->setCellValue('E' . $row, $d['nama_instansi']);
            $sheet->setCellValue('F' . $row, $d['unit_kerja']);
            $sheet->setCellValue('G' . $row, date('d-m-Y', strtotime($d['tanggal_masuk'])));
            $sheet->setCellValue('H' . $row, date('d-m-Y', strtotime($d['tanggal_selesai'])));
            $sheet->setCellValue('I' . $row, $d['durasi']);

            $row++;
        }

        // Download file
        $filename = 'data_alumni_magang_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

}
