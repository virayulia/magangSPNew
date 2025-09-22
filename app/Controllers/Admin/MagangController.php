<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KuotaunitModel;
use App\Models\MagangModel;
use App\Models\UnitKerjaModel;
use App\Models\UserModel;
use App\Models\RfidModel;
use App\Models\RfidAssignmentModel;
use App\Models\PenilaianModel;
use App\Models\SertifikatModel;

class MagangController extends BaseController
{
    protected $magangModel;
    protected $userModel;
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
        $this->unitKerjaModel = new UnitKerjaModel();
        $this->kuotaUnitModel = new KuotaunitModel();
        $this->rfidModel = new RfidModel();
        $this->rfidAssignmentModel = new RfidAssignmentModel();
        $this->penilaianModel = new PenilaianModel();
        $this->sertifikatModel = new SertifikatModel();
    }

    public function index()
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
                                        ->whereIn('magang.status_akhir', ['proses', 'pendaftaran'])
                                        ->orderBy('magang.tanggal_daftar', 'asc')
                                        ->findAll();
        $unitList = $this->unitKerjaModel->findAll();
        return view('admin/index', ['pendaftaran' => $pendaftaran, 'unitList' => $unitList]);
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

        // Ambil semua kuota unit, tetapi jangan filter hanya sisa_kuota > 0
        // Karena unit yang kuotanya habis tapi masih ada pendaftar juga harus muncul
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

        // Hitung sisa kuota (tetap ambil dari getSisaKuota)
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
            $email->clear(); // Reset sebelum mengirim email baru
            $email->setTo($emailPeserta);

            $email->setSubject('Penerimaan Magang di PT Semen Padang');
            $email->setMailType('html');
            $email->setMessage(view('emails/penerimaan_magang', [
                'nama'            => $user->fullname ?? $user->username,
                'unit'            => $pendaftar->unit_kerja,
                'tanggal_masuk'   => $start->format('d F Y'),
                'tanggal_selesai' => $end->format('d F Y'),
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

        if (!$ids || !is_array($ids)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada pendaftar yang dipilih.']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($ids as $id) {
            log_message('debug', 'Memproses tolak ID: ' . $id);

            $data = $builder
                ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username, unit_kerja.unit_kerja')
                ->join('users', 'users.id = magang.user_id', 'left')
                ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id', 'left')
                ->where('magang.magang_id', $id)
                ->get()
                ->getRow();

            if (!$data) {
                log_message('error', "Data magang dengan ID $id tidak ditemukan.");
                $failCount++;
                $messages[] = "ID $id: tidak ditemukan.";
                continue;
            }

            $updated = $builder->where('magang_id', $id)->update([
                'status_seleksi'   => 'Ditolak',
                'tanggal_seleksi' => date('Y-m-d H:i:s'),
                'status_akhir' => 'gagal'
            ]);

            if ($updated) {
                log_message('debug', "ID $id: Berhasil ditolak.");
                $successCount++;

                // ===== Kirim Email Penolakan =====
                $email = \Config\Services::email();
                $email->setTo($data->email);

                $email->setSubject('Hasil Seleksi Pendaftaran Magang di PT Semen Padang');
                $email->setMailType('html');
                $email->setMessage(view('emails/penolakan_magang', [
                    'nama' => $data->fullname ?? 'Saudara',
                    'unit' => $data->unit_kerja ?? 'Unit terkait',
                ]));

                if (!$email->send()) {
                    log_message('error', "Gagal kirim email ke ID $id: " . print_r($email->printDebugger(), true));
                }

            } else {
                log_message('error', "ID $id: Gagal update data.");
                $failCount++;
                $messages[] = "ID $id: gagal ditolak.";
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "$successCount berhasil ditolak. $failCount gagal.",
            'details' => $messages
        ]);
    }

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
                $email->clear(true); // reset email sebelum kirim berikutnya
                $email->setTo($toEmail);

                if (!empty($ccEmail) && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                    $email->setCC($ccEmail);
                }

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
                    ]));
                } else {
                    $email->setMessage(view('emails/tidak_approve', [
                        'nama'    => $data->fullname ?? $data->username,
                        'unit'    => $data->unit_kerja ?? 'Unit terkait',
                        'catatan' => $catatan
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
            // kalau ada id, filter sesuai id
            $builder->where('magang.magang_id', $id);
        }

        $data = $builder->findAll();

        return view('admin/kelola_kelengkapan', ['data' => $data]);
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

        $email->setSubject('Hasil Validasi Berkas Magang di PT Semen Padang');
        $email->setMailType('html');

        $email->setMessage(view('emails/berkas_valid', [
            'nama'            => $data->fullname ?? $data->username,
            'unit'            => $data->unit_kerja ?? 'Unit terkait',
            'user_data'       => $data,
            'tanggal_surat'   => $data->tanggal_surat, 
            'tanggal_masuk'   => $data->tanggal_masuk,
            'tanggal_selesai' => $data->tanggal_selesai,
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

        $email->setSubject('Hasil Validasi Berkas Magang di PT Semen Padang');
        $email->setMailType('html');

        $email->setMessage(view('emails/berkas_tidak_valid', [
            'nama'    => $data->fullname ?? $data->username,
            'unit'    => $data->unit_kerja ?? 'Unit terkait',
            'catatan' => $catatan
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
            // Default: bulan dan tahun ini
            $bulan = date('m');
            $tahun = date('Y');
        }

        $hasil = $db->table('jawaban_safety')
            ->select('
                users.fullname,
                users.nisn_nim,
                unit_kerja.unit_kerja,
                jawaban_safety.magang_id,
                MAX(jawaban_safety.nilai) as nilai_maksimal,
                MAX(jawaban_safety.created_at) as tanggal_terakhir,
                MAX(jawaban_safety.percobaan_ke) as percobaan_terakhir,
                (CASE WHEN MAX(jawaban_safety.nilai) >= 70 THEN "Lulus" ELSE "Tidak Lulus" END) as status
            ')
            ->join('magang', 'magang.magang_id = jawaban_safety.magang_id')
            ->join('users', 'users.id = magang.user_id')
            ->join('unit_kerja', 'unit_kerja.unit_id = magang.unit_id')
            ->where('magang.status_akhir','magang')
            ->where("MONTH(jawaban_safety.created_at)", $bulan)
            ->where("YEAR(jawaban_safety.created_at)", $tahun)
            ->groupBy('jawaban_safety.magang_id')
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

    public function pesertaMagang()
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
                penilaian.tgl_penilaian, penilaian.approve_kaunit,penilaian.tgl_disetujui,penilaian.approve_by,penilaian.catatan, penilaian.catatan_approval
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
                'status'         => 'aktif', // aktif, dikembalikan, hilang
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
        $status       = $this->request->getPost('status'); // returned / lost
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

            $email->setSubject('Perubahan Jadwal / Unit Magang - PT Semen Padang');
            $email->setMailType('html');
            $email->setMessage(view('emails/perubahan_magang', [
                'nama'           => $peserta->fullname,
                'unit'           => $peserta->unit_kerja,
                'tanggal_masuk'  => $data['tanggal_masuk'],
                'tanggal_selesai'=> $data['tanggal_selesai'],
                'instansi'       => $peserta->nama_instansi,
            ]));

            if (!$email->send()) {
                log_message('error', "Gagal kirim email perubahan magang ID {$id}: " . print_r($email->printDebugger(), true));
            }
        }

        return redirect()->back()->with('success', 'Data magang berhasil diperbarui & email pemberitahuan dikirim.');
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

            $email->setSubject('Pemberitahuan Pembatalan Magang di PT Semen Padang');
            $email->setMailType('html');

            $email->setMessage(view('emails/batalkan_magang', [
                'nama'   => $data->fullname ?? $data->username,
                'unit'   => $data->unit_kerja ?? 'unit terkait',
                'alasan' => $alasan
            ]));

            if (!$email->send()) {
                log_message('error', "Gagal kirim email pembatalan magang ID $id: " . print_r($email->printDebugger(), true));
            }
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function tolakLaporan()
    {
        $magangId = $this->request->getPost('magang_id');
        $catatan  = $this->request->getPost('catatan');

        $db = \Config\Database::connect();
        $data = $db->table('magang')
            ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->where('magang.magang_id', $magangId)
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
        $this->magangModel->update($magangId, [
            'laporan' => null,
            'catatan_laporan' => $catatan
        ]);

        // kirim email
        $email = \Config\Services::email();
        $toEmail = $data->email;

        if (!empty($toEmail)) {
            $email->setTo($toEmail);
        }

        $email->setSubject('Hasil Validasi Laporan Magang di PT Semen Padang');
        $email->setMailType('html');
        $email->setMessage(view('emails/laporan_tolak', [
            'nama'    => $data->fullname ?? $data->username,
            'catatan' => $catatan
        ]));

        if (!$email->send()) {
            log_message('error', "Gagal kirim email tolak laporan ID $magangId: " . print_r($email->printDebugger(), true));
        }

        return redirect()->back()->with('success', 'Laporan berhasil ditolak dan email notifikasi dikirim.');
    }

    public function tolakAbsensi()
    {
        $magangId = $this->request->getPost('magang_id');
        $catatan  = $this->request->getPost('catatan');

        $db = \Config\Database::connect();
        $data = $db->table('magang')
            ->select('magang.*, users.email, users.email_instansi, users.fullname, users.username')
            ->join('users', 'users.id = magang.user_id', 'left')
            ->where('magang.magang_id', $magangId)
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
        $this->magangModel->update($magangId, [
            'absensi' => null,
            'catatan_absensi' => $catatan
        ]);

        // kirim email
        $email = \Config\Services::email();
        $toEmail = $data->email;

        if (!empty($toEmail)) {
            $email->setTo($toEmail);
        }

        $email->setSubject('Hasil Validasi Absensi Magang di PT Semen Padang');
        $email->setMailType('html');
        $email->setMessage(view('emails/absensi_tolak', [
            'nama'    => $data->fullname ?? $data->username,
            'catatan' => $catatan
        ]));

        if (!$email->send()) {
            log_message('error', "Gagal kirim email tolak absensi ID $magangId: " . print_r($email->printDebugger(), true));
        }

        return redirect()->back()->with('success', 'Absensi berhasil ditolak dan email notifikasi dikirim.');
    }

    public function finalisasi($magangId)
    {
        $magang = $this->magangModel->find($magangId);

        if (!$magang) {
            return redirect()->back()->with('error', 'Data magang tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // Update status akhir jadi lulus
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
                            MAX(jawaban_safety.nilai) as nilai_maksimal,
                            MAX(jawaban_safety.created_at) as tanggal_terakhir,
                            MAX(jawaban_safety.percobaan_ke) as percobaan_terakhir,
                            CASE 
                                WHEN MAX(jawaban_safety.nilai) IS NULL THEN "Belum Tes"
                                WHEN MAX(jawaban_safety.nilai) >= 70 THEN "Lulus"
                                ELSE "Belum Lulus"
                            END as status_tes,
                            rfid.rfid_no, rfid.id_rfid, 
                            ra.assignment_id, ra.status as status_rfid, ra.tanggal_kembali, ra.tanggal_bayar, 
                            feedback.feedback_id,
                        ')
                        ->join('users', 'users.id = magang.user_id')
                        ->join('instansi', 'instansi.instansi_id = users.instansi_id')
                        ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
                        ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
                        ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
                        ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
                        ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
                        ->join('unit_kerja', 'magang.unit_id = unit_kerja.unit_id')
                        ->join('jawaban_safety', 'magang.magang_id = jawaban_safety.magang_id', 'left')
                        ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
                        ->join('feedback', 'feedback.magang_id = magang.magang_id', 'left')
                         ->join('(
                            SELECT r1.*
                            FROM rfid_assignment r1
                            JOIN (
                                SELECT magang_id, MAX(tanggal_pinjam) as max_created
                                FROM rfid_assignment
                                GROUP BY magang_id
                            ) r2 ON r1.magang_id = r2.magang_id AND r1.tanggal_pinjam = r2.max_created
                        ) AS ra', 'ra.magang_id = magang.magang_id', 'left')
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

    public function cetakSertifikat($id, $saveToFile = false)
    {
        $userRow = $this->magangModel->select('user_id')
                             ->where('magang_id', $id)
                             ->first();

        $userId = $userRow['user_id']; 
        // $userId = user_id();

        // Ambil data user & magang terbaru yang lulus
        $user = $this->userModel->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
                                ->join('instansi', 'instansi.instansi_id = users.instansi_id')
                                ->find($userId);
        $magang = $this->magangModel->join('unit_kerja', 'unit_kerja.unit_id=magang.unit_id')
            ->where('user_id', $userId)
            ->where('status_akhir', 'lulus')
            ->orderBy('magang_id', 'DESC')
            ->first();

        if (!$magang) {
            return redirect()->back()->with('error', 'Tidak ada magang aktif untuk dicetak sertifikat.');
        }

        $penilaian = $this->penilaianModel->where('magang_id', $magang['magang_id'])->first();

        if (!$penilaian || $magang['ka_unit_approve'] != 1) {
            return redirect()->back()->with('error', 'Sertifikat belum bisa diunduh.');
        }

        // Hitung total & rata-rata
        $totalNilai = $penilaian['nilai_disiplin']
            + $penilaian['nilai_kerajinan']
            + $penilaian['nilai_tingkahlaku']
            + $penilaian['nilai_kerjasama']
            + $penilaian['nilai_kreativitas']
            + $penilaian['nilai_kemampuankerja']
            + $penilaian['nilai_tanggungjawab']
            + $penilaian['nilai_penyerapan'];

        $rataRata = round($totalNilai / 8, 0); // bulatkan ke integer

        // Tentukan kategori
        if ($rataRata >= 90) $kategori = 'Baik Sekali';
        elseif ($rataRata >= 80) $kategori = 'Baik';
        elseif ($rataRata >= 70) $kategori = 'Cukup';
        elseif ($rataRata >= 60) $kategori = 'Kurang';
        else $kategori = 'Sangat Kurang';

        // ================== Nomor Sertifikat ==================
        $tahunSekarang = date('Y');
        $bulanSekarang = date('m');

        // cek apakah sudah ada nomor sertifikat untuk magang ini
        $sertifikat = $this->sertifikatModel
            ->where('magang_id', $magang['magang_id'])
            ->first();

        if (!$sertifikat) {
            // ambil nomor urut terakhir tahun berjalan
            $last = $this->sertifikatModel
                ->where('tahun', $tahunSekarang)
                ->orderBy('nomor', 'DESC')
                ->first();

            $nextNumber = $last ? intval($last['nomor']) + 1 : 1;

            // simpan ke tabel sertifikat
            $this->sertifikatModel->insert([
                'magang_id' => $magang['magang_id'],
                'nomor'     => $nextNumber,
                'tahun'     => $tahunSekarang,
            ]);
        } else {
            $nextNumber = $sertifikat['nomor'];
        }

        // format nomor sertifikat
        $noUrut = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $noSertifikat = "{$noUrut}/MAGANG/SP/{$bulanSekarang}.{$tahunSekarang}";


        // Inisialisasi TCPDF
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
        $pdf->Cell(210, 18, $user->fullname ?? $user->username, 0, 1, 'C');

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

        // Posisi mulai (pojok kiri bawah, misal 190mm dari atas)
        $pdf->SetFont('times', '', 16);
        $pdf->SetXY(30, 200);
        $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');

        $pdf->SetFont('times', 'B', 16);
        $pdf->SetX(30);
        $pdf->Cell(0, 8, "Training & KM", 0, 1, 'L');

        // Tambahkan tanda tangan (PNG/JPG transparan lebih bagus)
        $ttdPath = FCPATH . 'assets/img/ttd.png'; // ganti dengan path tanda tanganmu
        if (file_exists($ttdPath)) {
            $pdf->Image($ttdPath, 30, 215, 45, 0, '', '', '', false, 300);

        }

        // Nama pejabat
        $pdf->SetFont('times', 'B', 16);
        $pdf->SetXY(30, 235);
        $pdf->Cell(0, 8, "Siska Ayu Soraya", 0, 1, 'L');

        $pdf->SetFont('times', '', 14);
        $pdf->SetX(30);
        $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');


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

        // tampilkan kategori full, bukan A/B/C
        $pdf->SetXY(123, $startY + (8 * $stepY) + 12.5);
        $pdf->Cell(60, 10, $kategori, 0, 0, 'L');

        //TTD pojok kanan
        $pdf->SetFont('times', '', 16);
        $pdf->SetXY(120, 215);
        $pdf->Cell(0, 8, "Padang, " . $tanggalApprove, 0, 1, 'L');
        $pdf->SetFont('times', 'B', 16);
        $pdf->SetX(130);
        $pdf->Cell(0, 8, "Training & KM", 0, 1, 'L');

        // Tambahkan tanda tangan (PNG/JPG transparan lebih bagus)
        $ttdPath = FCPATH . 'assets/img/ttd.png'; // ganti dengan path tanda tanganmu
        if (file_exists($ttdPath)) {
            $pdf->Image($ttdPath, 130, 228, 45, 0, '', '', '', false, 300);

        }

        // Nama pejabat
        $pdf->SetFont('times', 'B', 16);
        $pdf->SetXY(130, 245);
        $pdf->Cell(0, 8, "Siska Ayu Soraya", 0, 1, 'L');

        $pdf->SetFont('times', '', 14);
        $pdf->SetX(130);
        $pdf->Cell(0, 8, "Kepala", 0, 1, 'L');

        // ================= Output =================
        $fileName = 'sertifikat-magang-' . url_title($user->fullname ?? $user->nama, '-', true) . '-' . date('YmdHis') . '.pdf';

        if ($saveToFile) {
            $filePath = WRITEPATH . 'uploads/' . $fileName;
            $pdf->Output($filePath, 'F');
            return $filePath;
        } else {
            $this->response->setContentType('application/pdf');
            $pdf->Output($fileName, 'I');
            exit;
        }
    }




}
