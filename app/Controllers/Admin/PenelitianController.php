<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\UnitKerjaModel;
use App\Models\PenelitianModel;
use App\Models\RfidModel;
use App\Models\RfidAssignmentModel;
use App\Models\KeywordModel;
use App\Models\PenelitianKeywordModel;
use App\Models\PenelitianKeywordModell;
use App\Models\UserModel;

class PenelitianController extends BaseController
{
    protected $unitKerjaModel;
    protected $penelitianModel;
    protected $rfidModel;
    protected $rfidAssignmentModel;
    protected $keywordModel;
    protected $penelitianKeywordModel;
    protected $userModel;

    public function __construct() {
        $this->unitKerjaModel = new UnitKerjaModel();
        $this->penelitianModel = new PenelitianModel();
        $this->rfidModel = new RfidModel();
        $this->rfidAssignmentModel = new RfidAssignmentModel();
        $this->keywordModel = new KeywordModel();
        $this->penelitianKeywordModel = new PenelitianKeywordModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $filterStatus = $this->request->getGet('filter');

        // ================= QUERY LIST =================
        $builder = $db->table('penelitian p')
            ->select('p.*, u.fullname, u.email, j.nama_jurusan, GROUP_CONCAT(k.keyword_nama SEPARATOR ", ") as keywords')
            ->join('users u', 'u.id = p.user_id')
            ->join('jurusan j', 'j.jurusan_id = u.jurusan_id')
            ->join('penelitian_keyword pk','pk.penelitian_id = p.penelitian_id')
            ->join('keyword k','k.keyword_id = pk.keyword_id')
            ->whereIn('p.status_akhir', ['pendaftaran', 'proses'])
            ->groupBy('p.penelitian_id')
            ->orderBy('p.tanggal_daftar', 'ASC');

        if ($filterStatus) {
            switch ($filterStatus) {
                case 'pendaftaran':
                    $builder->where('p.status_akhir', 'pendaftaran');
                    break;

                case 'menunggu-approval':
                    $builder->where('p.status_seleksi', 'diterima')
                            ->where('p.approve_unit IS NULL');
                    break;

                case 'diterima':
                    $builder->where('p.status_seleksi', 'diterima')
                            ->where('p.approve_unit', 'Y')
                            ->where('p.status_konfirmasi IS NULL');
                    break;

                case 'terkonfirmasi':
                    $builder->where('p.status_konfirmasi', 'Y');
                    break;
            }
        }

        $penelitian = $builder->get()->getResult();

        // ================= DATA LAIN =================
        $unitKerja = $this->unitKerjaModel->findAll();

        // ================= PILIHAN TANGGAL =================
        $today = new \DateTime();
        $dates = [];

        for ($i = 1; $i <= 3; $i++) {
            $month = (clone $today)->modify("+{$i} month");
            $year = $month->format('Y');
            $monthNum = $month->format('m');

            $awal   = new \DateTime("$year-$monthNum-01");
            $tengah = new \DateTime("$year-$monthNum-15");

            foreach ([$awal, $tengah] as $tgl) {
                $hari = $tgl->format('N');
                if ($hari == 6) $tgl->modify('+2 days');
                if ($hari == 7) $tgl->modify('+1 day');

                $diff = $today->diff($tgl)->days;
                if ($tgl > $today && $diff >= 7) {
                    $dates[] = $tgl->format('Y-m-d');
                }
            }
        }

        // ================= HITUNG STATUS =================
        $allData = $this->penelitianModel
            ->whereIn('status_akhir', ['pendaftaran', 'proses'])
            ->findAll();

        $statusCount = [
            'Pendaftaran'       => 0,
            'Menunggu Approval' => 0,
            'Diterima'          => 0,
            'Terkonfirmasi'     => 0,
        ];

        foreach ($allData as $p) {

            // 1️⃣ TERKONFIRMASI
            if ($p['status_konfirmasi'] === 'Y') {
                $statusCount['Terkonfirmasi']++;
            }

            // 2️⃣ DITERIMA (approve unit sudah Y, tapi belum konfirmasi)
            elseif (
                strtolower($p['status_seleksi']) === 'diterima' &&
                $p['approve_unit'] === 'Y' &&
                is_null($p['status_konfirmasi'])
            ) {
                $statusCount['Diterima']++;
            }

            // 3️⃣ MENUNGGU APPROVAL UNIT
            elseif (
                strtolower($p['status_seleksi']) === 'diterima' &&
                is_null($p['approve_unit'])
            ) {
                $statusCount['Menunggu Approval']++;
            }

            // 4️⃣ PENDAFTARAN (default)
            else {
                $statusCount['Pendaftaran']++;
            }
        }

        // ================= VIEW =================
        $data = [
            'title'           => 'Data Pendaftaran',
            'penelitian'      => $penelitian,
            'unitKerja'       => $unitKerja,
            'pilihanTanggal'  => $dates,
            'statusCount'     => $statusCount,
            'filterStatus'    => $filterStatus
        ];

        return view('admin/kelola_penelitian', $data);
    }


    public function getRekomendasiUnit($penelitian_id)
    {
        $penelitian = $this->penelitianModel
            ->select("penelitian.*, GROUP_CONCAT(k.keyword_nama SEPARATOR ', ') AS keywords,
                        u.fullname, u.surat_permohonan, u.cv, j.nama_jurusan")
            ->join('users u','penelitian.user_id = u.id')
            ->join('jurusan j','j.jurusan_id = u.jurusan_id')
            ->join('penelitian_keyword pk', 'pk.penelitian_id = penelitian.penelitian_id', 'left')
            ->join('keyword k', 'k.keyword_id = pk.keyword_id', 'left')
            ->where('penelitian.penelitian_id', $penelitian_id)
            ->groupBy('penelitian.penelitian_id')
            ->get()->getRowArray();

        if (!$penelitian) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Penelitian tidak ditemukan.']);
        }

        $penelitian['rencana_masuk_formatted'] = format_tanggal_indonesia($penelitian['rencana_masuk']);

        $sql = "
            SELECT
                uk.unit_id,
                uk.unit_kerja,
                COUNT(DISTINCT pkmatch.keyword_id) AS match_count,
                GROUP_CONCAT(DISTINCT CASE WHEN pkmatch.keyword_id IS NOT NULL THEN k.keyword_nama END ORDER BY k.keyword_nama SEPARATOR ', ') AS matched_keywords
            FROM unit_kerja uk
            LEFT JOIN unit_keyword ukw ON uk.unit_id = ukw.unit_id
            LEFT JOIN keyword k ON k.keyword_id = ukw.keyword_id
            -- pkmatch hanya untuk menandai apakah keyword unit tersebut memang ada di penelitian ini
            LEFT JOIN penelitian_keyword pkmatch ON pkmatch.keyword_id = ukw.keyword_id AND pkmatch.penelitian_id = ?
            GROUP BY uk.unit_id
            ORDER BY match_count DESC, uk.unit_kerja ASC
        ";

        $db = \Config\Database::connect();
        $units = $db->query($sql, [$penelitian_id])->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'penelitian' => $penelitian,
            'units' => $units
        ]);
    }

    public function terimaSatu()
    {
        $id = $this->request->getPost('penelitian_id');
        $unit_id = $this->request->getPost('unit_id');
        $tanggalMulai = $this->request->getPost('tanggal_mulai');

        if (!$id || !$unit_id || !$tanggalMulai) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        }

        $penelitian = $this->penelitianModel->find($id);
        if (!$penelitian) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Penelitian tidak ditemukan.']);
        }

        $start = new \DateTime($tanggalMulai);
        $durasi = (int) $penelitian['durasi'];

        // End = start + durasi bulan - 1 hari
        $end = (clone $start)->modify('+' . $durasi . ' months')->modify('-1 day');

        $dataUpdate = [
            'unit_id' => $unit_id,
            'tanggal_masuk' => $start->format('Y-m-d'),
            'tanggal_selesai' => $end->format('Y-m-d'),
            'status_seleksi' => 'Diterima',
            'tanggal_seleksi' => date('Y-m-d H:i:s')
        ];

        // Jika unit_id berbeda dengan sebelumnya dan approve_unit = 'N'
        if (
            isset($penelitian['unit_id']) &&
            $penelitian['unit_id'] != $unit_id &&
            isset($penelitian['approve_unit']) &&
            $penelitian['approve_unit'] === 'N'
        ) {
            // Kosongkan kembali agar pembimbing bisa approve ulang
            $dataUpdate['approve_unit'] = null;
            $dataUpdate['tanggal_approve_unit'] = null;
        }

        // Update penelitian
        $this->penelitianModel->update($id, $dataUpdate);

        $db = \Config\Database::connect();
        $emailPembimbing = $db->table('unit_user')
                    ->select('unit_kerja.unit_id, unit_kerja.unit_kerja, unit_user.user_id, users.email')
                    ->join('unit_kerja', 'unit_kerja.unit_id = unit_user.unit_id')
                    ->join('users', 'users.id = unit_user.user_id')
                    ->where('users.eselon', '3')
                    ->where('unit_user.unit_id', 44)
                    ->where('users.fullname', 'Zamris')
                    ->get()
                    ->getRowArray();

        // kirim email
        $db = \Config\Database::connect();
        $user = $db->table('users')->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
                                   ->join('instansi', 'instansi.instansi_id = users.instansi_id')
                                    ->where('id', $penelitian['user_id'])->get()->getRow();

        $email = \Config\Services::email();
        $unit_id = 44;
        $signature = getSignature($unit_id);
        $email->setTo($emailPembimbing['email']);
        $email->setSubject('Permintaan Approval Unit Penelitian');
        $email->setMailType('html');
        $email->setMessage(view('emails/approval_unit', [
            'nama' => $user->fullname,
            'jurusan' => $user->nama_jurusan,
            'instansi' => $user->nama_instansi,
            'signature' => $signature

        ]));

        $email->send();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Penelitian berhasil diterima dan email telah dikirim.'
        ]);
    }



    public function terimaBanyak()
    {
        $ids = $this->request->getPost('pendaftar_ids');

        if (!$ids) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada pendaftar yang dipilih.']);
        }

        // pastikan dalam bentuk array
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('penelitian');
        $email = \Config\Services::email();

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($ids as $id) {
            // Ambil data pendaftar
            $pendaftar = $builder
                ->select('penelitian.*, unit_kerja.unit_kerja')
                ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
                ->where('penelitian.penelitian_id', $id)
                ->get()->getRow();

            if (!$pendaftar) {
                $failCount++;
                $messages[] = "ID $id: Pendaftar tidak ditemukan.";
                continue;
            }

           $tanggalMulai = $this->request->getPost('tanggal_mulai');

            if (empty($tanggalMulai)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Tanggal mulai tidak boleh kosong.']);
            }

            try {
                $start = new \DateTime($tanggalMulai);
            } catch (\Exception $e) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Format tanggal mulai tidak valid.']);
            }

            $end = clone $start;
            $durasi = $pendaftar->durasi;
            $end->modify('last day of +' . ($durasi - 1) . ' month');

            $unit_id = $this->request->getPost('unit_id');

            // Simpan ke database
            $db->table('penelitian')->where('penelitian_id', $id)->update([
                'unit_id'          => $unit_id,
                'status_seleksi'   => 'Diterima',
                'tanggal_seleksi' => date('Y-m-d H:i:s'),
                'tanggal_masuk'   => $tanggalMulai,
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
            $email->setSubject('Penerimaan Penelitian di PT Semen Padang');
            $email->setMailType('html');
            $email->setMessage(view('emails/penerimaan_penelitian', [
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
        $builder = $db->table('penelitian');

        $successCount = 0;
        $failCount = 0;
        $messages = [];

        foreach ($ids as $id) {
            $data = $builder
                ->select('penelitian.*, users.email, users.fullname, unit_kerja.unit_kerja')
                ->join('users', 'users.id = penelitian.user_id', 'left')
                ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
                ->where('penelitian.penelitian_id', $id)
                ->get()
                ->getRow();

            if (!$data) {
                $failCount++;
                $messages[] = "ID $id tidak ditemukan.";
                continue;
            }

            $updated = $builder->where('penelitian_id', $id)->update([
                'status_seleksi'   => 'Ditolak',
                'tanggal_seleksi' => date('Y-m-d H:i:s'),
                'status_akhir' => 'gagal',
                'alasan_batal' => $catatan 
            ]);

            if ($updated) {
                $successCount++;

                // kirim email
                $email = \Config\Services::email();
                $unit_id = 44;
                $signature = getSignature($unit_id);
                $email->setTo($data->email);
                $email->setSubject('Hasil Seleksi Pendaftaran penelitian di PT Semen Padang');
                $email->setMailType('html');
                $email->setMessage(view('emails/penolakan_penelitian', [
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

    public function validasi()
    {
        
        $data = $this->penelitianModel->select('penelitian.*, users.fullname, users.nisn_nim, users.bpjs_kes, users.bpjs_tk, users.buktibpjs_tk')
                                        ->join('users', 'users.id = penelitian.user_id')
                                        ->where('penelitian.status_konfirmasi', 'Y')
                                        ->where('penelitian.status_akhir', 'proses')
                                        ->orderBy('tanggal_konfirmasi')
                                        ->findAll();

        return view('admin/kelola_validasi_penelitian', ['data' => $data]);
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

        $pesertaList = $db->table('penelitian')
            ->select('penelitian.*, users.*, unit_kerja.unit_kerja, jurusan.nama_jurusan, instansi.nama_instansi')
            ->join('users', 'users.id = penelitian.user_id', 'left')
            ->join('jurusan', 'users.jurusan_id = jurusan.jurusan_id', 'left')
            ->join('instansi', 'users.instansi_id = instansi.instansi_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
            ->whereIn('penelitian.penelitian_id', $ids)
            ->get()
            ->getResult();


        foreach ($pesertaList as $data) {
            $updateData = [
                'status_validasi_konfirmasi'  => ($action === 'approve') ? 'Y' : 'N',
                'tanggal_validasi_konfirmasi' => $tanggal,
            ];

            if ($action === 'approve') {
                $updateData['status_akhir'] = 'penelitian';
            }

            $this->penelitianModel->update($data->penelitian_id, $updateData);

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
                $email->setSubject('Hasil Validasi Penelitian di PT Semen Padang');
                $email->setMailType('html');

                if ($action === 'approve') {
                    $email->setMessage(view('emails/approve_penelitian', [
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
                    log_message('error', "Gagal kirim email validasi ID {$data->penelitian_id}: " . print_r($email->printDebugger(), true));
                }
            }
        }

        return redirect()->back()->with('success', 
            $action === 'approve' 
                ? 'Peserta terpilih berhasil divalidasi dan email penerimaan telah dikirim.'
                : 'Peserta terpilih telah ditandai tidak valid dan email penolakan telah dikirim.'
        );
    }
    
    public function pesertaPenelitian()
    {
        $unitId  = $this->request->getGet('unit_kerja');
        $bulanMasuk  = $this->request->getGet('tanggal_masuk');
        $bulanKeluar = $this->request->getGet('tanggal_keluar');
        $filter = $this->request->getGet('filter');
        $tingkat = $this->request->getGet('tingkat');
        $today = date('Y-m-d');
        // ===============================
        // SUBQUERY SAFETY (terakhir)
        // ===============================
        $subSafety = "
            (
                SELECT js1.*
                FROM jawaban_safety js1
                JOIN (
                    SELECT relasi_id, MAX(created_at) AS max_created
                    FROM jawaban_safety
                    WHERE tipe = 'penelitian'
                    GROUP BY relasi_id
                ) js2
                ON js1.relasi_id = js2.relasi_id
                AND js1.created_at = js2.max_created
            ) AS js
        ";

        // ===============================
        // SUBQUERY RFID (terakhir)
        // ===============================
        $subRfid = "
            (
                SELECT r1.*
                FROM rfid_assignment r1
                JOIN (
                    SELECT relasi_id, MAX(assignment_id) AS max_assign
                    FROM rfid_assignment
                    WHERE tipe = 'penelitian'
                    GROUP BY relasi_id
                ) r2
                ON r1.relasi_id = r2.relasi_id
                AND r1.assignment_id = r2.max_assign
            ) AS ra
        ";

        $mapTingkat = [
            'SMK' => ['SMK'],
            'Perguruan Tinggi' => ['D3', 'D4/S1', 'S2']
        ];

        // ===============================
        // QUERY UTAMA (TANPA GROUP BY)
        // ===============================
        $builder = $this->penelitianModel
            ->select('
                penelitian.*,
                unit_kerja.unit_id,
                unit_kerja.unit_kerja,
                users.id AS user_id,
                users.fullname,
                users.nisn_nim,
                js.nilai AS nilai_maksimal,
                rfid.rfid_no,
                rfid.id_rfid,
                ra.assignment_id,
                ra.status AS status_rfid,
                ra.tanggal_kembali,
                ra.tanggal_bayar,
                feedback_penelitian.feedbackp_id,
                pembimbing.fullname AS nama_pembimbing,
                pembimbing.id AS pembimbing_id
            ')
            ->select("
                CASE 
                    WHEN js.nilai IS NULL THEN '-'
                    WHEN js.nilai >= 70 THEN 'Lulus'
                    ELSE 'Belum Lulus'
                END AS status_tes
            ", false)
            ->join('users', 'users.id = penelitian.user_id', 'left')
            ->join('users pembimbing', 'pembimbing.id = penelitian.pembimbing_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
            ->join($subSafety, 'js.relasi_id = penelitian.penelitian_id', 'left')
            ->join('feedback_penelitian', 'feedback_penelitian.penelitian_id = penelitian.penelitian_id', 'left')
            ->join($subRfid, 'ra.relasi_id = penelitian.penelitian_id', 'left')
            ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
            ->where('penelitian.status_akhir', 'penelitian');

        if ($bulanMasuk) {
            $builder->where('penelitian.tanggal_masuk >=', $bulanMasuk . '-01');
            $builder->where('penelitian.tanggal_masuk <', date('Y-m-01', strtotime($bulanMasuk . ' +1 month')));
        }

        if ($bulanKeluar) {
            $builder->where('penelitian.tanggal_selesai >=', $bulanKeluar . '-01');
            $builder->where('penelitian.tanggal_selesai <', date('Y-m-01', strtotime($bulanKeluar . ' +1 month')));
        }

        if (!empty($unitId)) {
            $builder->where('penelitian.unit_id', $unitId);
        }

        if (!empty($tingkat) && isset($mapTingkat[$tingkat])) {
            $builder->whereIn('users.tingkat_pendidikan', $mapTingkat[$tingkat]);
        }

        if ($filter == 'aktif') {
            $builder->where('penelitian.tanggal_masuk <=', $today)
                    ->where('penelitian.tanggal_selesai >=', $today);
        }

        if ($filter == 'akan_penelitian') {
            $builder->where('penelitian.tanggal_masuk >', $today);
        }

        if ($filter == 'belum_selesai') {
            $builder->where('penelitian.tanggal_selesai <', $today);
        }

        $data = $builder->get()->getResultArray();

        // Chart
        $chartData = ['aktif' => 0, 'akan_masuk' => 0, 'belum_selesai' => 0];

        foreach ($data as $row) {
            if ($row['tanggal_masuk'] <= $today && $row['tanggal_selesai'] >= $today)
                $chartData['aktif']++;

            if ($row['tanggal_masuk'] > $today)
                $chartData['akan_masuk']++;

            if ($row['tanggal_selesai'] < $today && $row['status_akhir'] === 'penelitian')
                $chartData['belum_selesai']++;
        }

        return view('admin/kelola_peserta_penelitian', [
            'data'      => $data,
            'unitList'  => $this->unitKerjaModel->findAll(),
            'rfidList'  => $this->rfidModel->findAll(),
            'chartData' => $chartData,
            'unitGet'   => $unitId ?? '',
        ]);
    }


    //error only_full_group_by
    public function pesertaPenelitianold()
    {
        $unitId       = $this->request->getGet('unit_kerja');
        $bulanMasuk   = $this->request->getGet('bulan_masuk');   // format: Y-m
        $bulanKeluar  = $this->request->getGet('bulan_keluar');  // format: Y-m
        $today        = date('Y-m-d');

        // ===============================
        // SUBQUERY SAFETY – ambil terakhir
        // ===============================
        $subSafety = "
            (
                SELECT js1.*
                FROM jawaban_safety js1
                JOIN (
                    SELECT relasi_id, tipe, MAX(created_at) AS max_created
                    FROM jawaban_safety
                    WHERE tipe = 'penelitian'
                    GROUP BY relasi_id, tipe
                ) js2 
                ON js1.relasi_id = js2.relasi_id
                AND js1.tipe = js2.tipe
                AND js1.created_at = js2.max_created
            ) AS js
        ";

        // ===============================
        // SUBQUERY RFID – assignment terakhir
        // ===============================
        $subRfid = "
            (
                SELECT r1.*
                FROM rfid_assignment r1
                JOIN (
                    SELECT relasi_id, tipe, MAX(assignment_id) AS max_assign
                    FROM rfid_assignment
                    WHERE tipe = 'penelitian'
                    GROUP BY relasi_id, tipe
                ) r2 
                ON r1.relasi_id = r2.relasi_id
                AND r1.tipe = r2.tipe
                AND r1.assignment_id = r2.max_assign
            ) AS ra
        ";

        // ===============================
        // QUERY UTAMA
        // ===============================
        $builder = $this->penelitianModel
            ->select('
                penelitian.*,

                unit_kerja.unit_id,
                unit_kerja.unit_kerja,

                users.id AS user_id,
                users.fullname,
                users.nisn_nim,

                js.nilai AS nilai_maksimal,

                rfid.rfid_no,
                rfid.id_rfid,
                ra.assignment_id,
                ra.status AS status_rfid,
                ra.tanggal_kembali,
                ra.tanggal_bayar,

                feedback_penelitian.feedbackp_id,
                pembimbing.fullname AS nama_pembimbing, pembimbing.id AS pembimbing_id
            ')
            ->select("
                CASE 
                    WHEN js.nilai IS NULL THEN '-'
                    WHEN js.nilai >= 70 THEN 'Lulus'
                    ELSE 'Belum Lulus'
                END AS status_tes
            ", false)
            ->join('users', 'users.id = penelitian.user_id', 'left')
            ->join('users pembimbing', 'pembimbing.id = penelitian.pembimbing_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
            ->join($subSafety, 'js.relasi_id = penelitian.penelitian_id', 'left')
            ->join('feedback_penelitian', 'feedback_penelitian.penelitian_id = penelitian.penelitian_id', 'left')
            ->join($subRfid, 'ra.relasi_id = penelitian.penelitian_id', 'left')
            ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
            ->where('penelitian.status_akhir', 'penelitian')
            ->groupBy('penelitian.penelitian_id');

        // ===============================
        // FILTER BULAN MASUK (Y-m)
        // ===============================
        if ($bulanMasuk) {
            $builder->where('penelitian.tanggal_masuk >=', $bulanMasuk . '-01');
            $builder->where(
                'penelitian.tanggal_masuk <',
                date('Y-m-01', strtotime($bulanMasuk . ' +1 month'))
            );
        }

        // ===============================
        // FILTER BULAN KELUAR (Y-m)
        // ===============================
        if ($bulanKeluar) {
            $builder->where('penelitian.tanggal_selesai >=', $bulanKeluar . '-01');
            $builder->where(
                'penelitian.tanggal_selesai <',
                date('Y-m-01', strtotime($bulanKeluar . ' +1 month'))
            );
        }

        // ===============================
        // FILTER UNIT KERJA
        // ===============================
        if (!empty($unitId)) {
            $builder->where('penelitian.unit_id', $unitId);
        }

        // ===============================
        // AMBIL DATA
        // ===============================
        $data = $builder->asArray()->findAll();

        // ===============================
        // CHART DATA
        // ===============================
        $chartData = [
            'aktif'        => 0,
            'akan_masuk'  => 0,
            'belum_selesai' => 0,
        ];

        foreach ($data as $row) {

            // AKTIF
            if ($row['tanggal_masuk'] <= $today && $row['tanggal_selesai'] >= $today) {
                $chartData['aktif']++;
            }

            // AKAN MASUK
            if ($row['tanggal_masuk'] > $today) {
                $chartData['akan_masuk']++;
            }

            // BELUM SELESAI (sudah lewat tapi status masih penelitian)
            if ($row['tanggal_selesai'] < $today && $row['status_akhir'] === 'penelitian') {
                $chartData['belum_selesai']++;
            }
        }

        return view('admin/kelola_peserta_penelitian', [
            'data'       => $data,
            'unitList'   => $this->unitKerjaModel->findAll(),
            'rfidList'   => $this->rfidModel->findAll(),
            'chartData'  => $chartData,
            'unitGet'    => $unitId ?? '',
        ]);
    }


// public function pesertaPenelitian()
// {
//     $unitId  = $this->request->getGet('unit_kerja');
//     $bulanMasuk  = $this->request->getGet('bulan_masuk');
//     $bulanKeluar = $this->request->getGet('bulan_keluar');
//     $tahun       = $this->request->getGet('tahun');

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

//     $builder = $this->penelitianModel->select('
//             penelitian.*, 
//             unit_kerja.unit_kerja,
//             users.id AS user_id, users.fullname, users.nisn_nim,
//             js.nilai AS nilai_maksimal,
//             rfid.rfid_no, rfid.id_rfid,
//             ra.assignment_id, ra.status AS status_rfid, ra.tanggal_kembali, ra.tanggal_bayar,
//             feedback_penelitian.feedbackp_id
//         ')
//         ->select("
//             CASE 
//                 WHEN js.nilai IS NULL THEN 'Belum Tes'
//                 WHEN js.nilai >= 70 THEN 'Lulus'
//                 ELSE 'Belum Lulus'
//             END AS status_tes
//         ", false)
//         ->join('users', 'users.id = penelitian.user_id', 'left')
//         ->join('unit_kerja', 'penelitian.unit_id = unit_kerja.unit_id','left')
//         ->join($subSafety, 'js.relasi_id = penelitian.penelitian_id', 'left')
//         ->join('feedback_penelitian', 'feedback_penelitian.penelitian_id = penelitian.penelitian_id', 'left')
//         ->join($subRfid, 'ra.relasi_id = penelitian.penelitian_id', 'left')
//         ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
//         ->where('penelitian.status_akhir', 'penelitian')
//         ->groupBy('penelitian.penelitian_id'); // ⬅️ penting untuk menghilangkan duplikasi

//     if (!empty($bulanMasuk)) {
//         $builder->where('MONTH(penelitian.tanggal_masuk)', $bulanMasuk);
//     }

//     if (!empty($bulanKeluar)) {
//         $builder->where('MONTH(penelitian.tanggal_selesai)', $bulanKeluar);
//     }

//     if (!empty($tahun)) {
//         $builder->groupStart()
//                 ->where('YEAR(penelitian.tanggal_masuk)', $tahun)
//                 ->orWhere('YEAR(penelitian.tanggal_selesai)', $tahun)
//                 ->groupEnd();
//     }

//     if (!empty($unitId)) {
//         $builder->where('penelitian.unit_id', $unitId);
//     }

//     $data = $builder->findAll();

//     return view('admin/kelola_peserta_penelitian', [
//         'data' => $data,
//         'unitList' => $this->unitKerjaModel->findAll(),
//         'rfidList' => $this->rfidModel->findAll()
//     ]);
// }

    public function exportPeserta()
    {
        // ambil filter 
        $bulanMasuk = $this->request->getGet('tanggal_masuk');
        $bulanKeluar = $this->request->getGet('tanggal_keluar');
        $unitKerja = $this->request->getGet('unit_kerja');
        $filter = $this->request->getGet('filter');
        $tingkat = $this->request->getGet('tingkat');
        $today = date('Y-m-d');
        
        $mapTingkat = [
                'SMK' => ['SMK'],
                'Perguruan Tinggi' => ['D3', 'D4/S1', 'S2']
        ];
        // ambil data 
        $builder = $this->penelitianModel->select('users.email, users.no_hp , users.fullname, users.nisn_nim, users.tingkat_pendidikan, jurusan.nama_jurusan, instansi.nama_instansi, 
                                            unit_kerja.unit_kerja, penelitian.tanggal_masuk, penelitian.tanggal_selesai, penelitian.durasi')
                                        ->join('users', 'users.id = penelitian.user_id')
                                        ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
                                        ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id','left')
                                        ->join('unit_kerja', 'penelitian.unit_id = unit_kerja.unit_id')
                                        ->where('penelitian.status_akhir', 'penelitian');


        // Filter tanggal masuk
        if ($bulanMasuk) {
            $builder->where('penelitian.tanggal_masuk >=', $bulanMasuk . '-01');
            $builder->where(
                'penelitian.tanggal_masuk <',
                date('Y-m-01', strtotime($bulanMasuk . ' +1 month'))
            );
        }

        // Filter tanggal keluar
        if ($bulanKeluar) {
            $builder->where('penelitian.tanggal_selesai >=', $bulanKeluar . '-01');
            $builder->where(
                'penelitian.tanggal_selesai <',
                date('Y-m-01', strtotime($bulanKeluar . ' +1 month'))
            );
        }
        if ($unitKerja) {
            $builder->where('penelitian.unit_id', $unitKerja);
        }

        if (!empty($tingkat) && isset($mapTingkat[$tingkat])) {
            $builder->whereIn('users.tingkat_pendidikan', $mapTingkat[$tingkat]);
        }

        if ($filter == 'aktif') {
            $builder->where('penelitian.tanggal_masuk <=', $today)
                    ->where('penelitian.tanggal_selesai >=', $today);
        }

        if ($filter == 'akan_penelitian') {
            $builder->where('penelitian.tanggal_masuk >', $today);
        }

        if ($filter == 'belum_selesai') {
            $builder->where('penelitian.tanggal_selesai <', $today);
        }

        $data = $builder->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'Email');
        $sheet->setCellValue('B1', 'No HP');
        $sheet->setCellValue('C1', 'Nama');
        $sheet->setCellValue('D1', 'NIM/NISN');
        $sheet->setCellValue('E1', 'Tingkatan');
        $sheet->setCellValue('F1', 'Jurusan');
        $sheet->setCellValue('G1', 'Nama PT/SMK');
        $sheet->setCellValue('H1', 'Unit Kerja');
        $sheet->setCellValue('I1', 'Tanggal Masuk');
        $sheet->setCellValue('J1', 'Tanggal Selesai');
        $sheet->setCellValue('K1', 'Durasi');

        // Isi data
        $row = 2;
        foreach ($data as $d) {
            $sheet->setCellValue('A' . $row, $d['email']);
            $sheet->setCellValue('B' . $row, $d['no_hp']);
            $sheet->setCellValue('C' . $row, $d['fullname']);
            $sheet->setCellValue('D' . $row, $d['nisn_nim']);
            $sheet->setCellValue('E' . $row, $d['tingkat_pendidikan']);
            $sheet->setCellValue('F' . $row, $d['nama_jurusan']);
            $sheet->setCellValue('G' . $row, $d['nama_instansi']);
            $sheet->setCellValue('H' . $row, $d['unit_kerja']);
            $sheet->setCellValue('I' . $row, date('d-m-Y', strtotime($d['tanggal_masuk'])));
            $sheet->setCellValue('J' . $row, date('d-m-Y', strtotime($d['tanggal_selesai'])));
            $sheet->setCellValue('K' . $row, $d['durasi']);
            $row++;
        }


        // Download file
        $filename = 'data_peserta_penelitian_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
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

    public function setPembimbing($penelitian_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('penelitian');

        $pembimbing_id = $this->request->getPost('pembimbing_id');

        if (empty($pembimbing_id)) {
            return redirect()->back()->with('error', 'Silakan pilih pembimbing.');
        }

        $builder->where('penelitian_id', $penelitian_id)
                ->update(['pembimbing_id' => $pembimbing_id]);

        if ($db->affectedRows() > 0) {
            return redirect()->back()->with('success', 'Pembimbing berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan pembimbing.');
        }
    }

    public function getDetailPenelitian($id)
    {
        $data = $this->penelitianModel
            ->select("
                penelitian.*,
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
            ->join('users', 'users.id = penelitian.user_id')
            ->join('users pembimbing', 'pembimbing.id = penelitian.pembimbing_id', 'left') 
            ->join('unit_kerja', 'penelitian.unit_id = unit_kerja.unit_id', 'left')
            ->join('jurusan', 'users.jurusan_id = jurusan.jurusan_id', 'left')
            ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
            ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
            ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
            ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
            ->join('instansi', 'users.instansi_id = instansi.instansi_id', 'left')
            ->where('penelitian.penelitian_id', $id)
            ->first();


        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function getEditData($id)
    {
        $data = $this->penelitianModel
            ->select('penelitian.*, users.fullname')
            ->join('users', 'users.id = penelitian.user_id')
            ->where('penelitian.penelitian_id', $id)
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

    public function updatePenelitian($id)
    {
        $data = [
            'tanggal_masuk'  => $this->request->getPost('tanggal_masuk'),
            'tanggal_selesai'=> $this->request->getPost('tanggal_selesai'),
            'unit_id'        => $this->request->getPost('unit_id'),
        ];

        $this->penelitianModel->update($id, $data);

        // Ambil data user + instansi terkait untuk email
        $db = \Config\Database::connect();
        $peserta = $db->table('penelitian')
            ->select('penelitian.*, users.email, users.fullname, users.email_instansi, instansi.nama_instansi, unit_kerja.unit_kerja')
            ->join('users', 'users.id = penelitian.user_id', 'left')
            ->join('instansi', 'users.instansi_id = instansi.instansi_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
            ->where('penelitian.penelitian_id', $id)
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
            $email->setSubject('Perubahan Jadwal / Unit Penelitian - PT Semen Padang');
            $email->setMailType('html');
            $email->setMessage(view('emails/perubahan_penelitian', [
                'nama'           => $peserta->fullname,
                'unit'           => $peserta->unit_kerja,
                'tanggal_masuk'  => $data['tanggal_masuk'],
                'tanggal_selesai'=> $data['tanggal_selesai'],
                'instansi'       => $peserta->nama_instansi,
                'signature' => $signature
            ]));

            if (!$email->send()) {
                log_message('error', "Gagal kirim email perubahan penelitian ID {$id}: " . print_r($email->printDebugger(), true));
            }
        }

    return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data penelitian berhasil diperbarui & email pemberitahuan dikirim.'
        ]);        
    }

    public function batalkanPenelitian()
    {
        $id = $this->request->getPost('id');
        $alasan = $this->request->getPost('alasan');

        if (!$id || !$alasan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

        $db = \Config\Database::connect();
        $data = $db->table('penelitian')
            ->select('penelitian.*, users.email, users.email_instansi, users.fullname, users.username, unit_kerja.unit_kerja')
            ->join('users', 'users.id = penelitian.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
            ->where('penelitian.penelitian_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data penelitian tidak ditemukan']);
        }

        // Update status dan alasan
        $this->penelitianModel->update($id, [
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
            $email->setSubject('Pemberitahuan Pembatalan Penelitian di PT Semen Padang');
            $email->setMailType('html');

            $email->setMessage(view('emails/batalkan_penelitian', [
                'nama'   => $data->fullname ?? $data->username,
                'unit'   => $data->unit_kerja ?? 'unit terkait',
                'alasan' => $alasan,
                'signature' => $signature
            ]));

            if (!$email->send()) {
                log_message('error', "Gagal kirim email pembatalan penelitian ID $id: " . print_r($email->printDebugger(), true));
            }
        }

        return $this->response->setJSON(['status' => 'success']);
    }

    public function berkas($id = null)
    {
        $filter = $this->request->getGet('filter'); 

        $builder = $this->penelitianModel->select(
            'penelitian.*, users.fullname, users.nisn_nim, users.bpjs_kes, users.bpjs_tk, users.buktibpjs_tk'
        )->join('users', 'users.id = penelitian.user_id')
        ->where('penelitian.status_validasi_konfirmasi', 'Y')
        ->groupStart()
            ->where('penelitian.status_berkas_lengkap !=', 'Y')
            ->orWhere('penelitian.status_berkas_lengkap IS NULL')
        ->groupEnd()
        ->orderBy('tanggal_validasi_konfirmasi');

        if (!empty($id)) {
            $builder->where('penelitian.penelitian_id', $id);
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

        // return view('admin/kelola_berkas_penelitian', ['data' => $data]);
        return view('admin/kelola_berkas_penelitian', [
            'data' => $filteredData,
            'statusBerkas' => $statusBerkas,
            'filter' => $filter
        ]);
    }


    public function valid($id)
    {
        $catatan = $this->request->getPost('catatan');

        $db = \Config\Database::connect();
        $data = $db->table('penelitian')
            ->select('penelitian.*, users.*, unit_kerja.unit_kerja, jurusan.nama_jurusan, instansi.nama_instansi ')
            ->join('users', 'users.id = penelitian.user_id', 'left')
            ->join('jurusan', 'users.jurusan_id = jurusan.jurusan_id', 'left')
            ->join('instansi', 'users.instansi_id = instansi.instansi_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
            ->where('penelitian.penelitian_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return redirect()->back()->with('error', 'Data penelitian tidak ditemukan.');
        }

        // Update data
        $updateData = [
            'status_berkas_lengkap'    => 'Y',
            'tanggal_berkas_lengkap'   => date('Y-m-d H:i:s'),
            'cttn_berkas_lengkap'      => $catatan,
            'status_akhir'             => 'penelitian'
        ];

        $this->penelitianModel->update($id, $updateData);

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
        $email->setSubject('Hasil Validasi Berkas Penelitian di PT Semen Padang');
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
        $data = $db->table('penelitian')
            ->select('penelitian.*, users.email, users.email_instansi, users.fullname, users.username, unit_kerja.unit_kerja')
            ->join('users', 'users.id = penelitian.user_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
            ->where('penelitian.penelitian_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return redirect()->back()->with('error', 'Data penelitian tidak ditemukan.');
        }

        // Update data
        $updateData = [
            'status_berkas_lengkap'      => 'N',
            'tanggal_berkas_lengkap'     => date('Y-m-d H:i:s'),
            'cttn_berkas_lengkap'        => $catatan,
        ];

        $this->penelitianModel->update($id, $updateData);

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
        $email->setSubject('Hasil Validasi Berkas Penelitian di PT Semen Padang');
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

    public function tolakFormulir($id)
    {
        $catatan  = $this->request->getPost('alasan');
        
        $db = \Config\Database::connect();
        $data = $db->table('penelitian')
            ->select('penelitian.*, users.email, users.email_instansi, users.fullname, users.username')
            ->join('users', 'users.id = penelitian.user_id', 'left')
            ->where('penelitian.penelitian_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return redirect()->back()->with('error', 'Data penelitian tidak ditemukan.');
        }

        // hapus file laporan kalau ada
        if (!empty($data->formulir_penelitian)) {
            $filePath = FCPATH . 'uploads/formpenelitian/' . $data->formulir_penelitian;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // update database
        $this->penelitianModel->update($id, [
            'formulir_penelitian' => null,
            'catatan_formulir' => $catatan
        ]);

        // kirim email
        $email = \Config\Services::email();
        $toEmail = $data->email;

        if (!empty($toEmail)) {
            $email->setTo($toEmail);
        }
        $unit_id = 44;
        $signature = getSignature($unit_id);
        $email->setSubject('Hasil Validasi Formulir Penelitian di PT Semen Padang');
        $email->setMailType('html');
        $email->setMessage(view('emails/formulir_tolak', [
            'nama'    => $data->fullname ?? $data->username,
            'catatan' => $catatan,
            'signature' => $signature
        ]));

        if (!$email->send()) {
            log_message('error', "Gagal kirim email tolak laporan ID $id: " . print_r($email->printDebugger(), true));
        }

        return redirect()->back()->with('success', 'Formulir Penelitian berhasil ditolak dan email notifikasi dikirim.');
    }

     public function tolakAbsensi($id)
    {
        $catatan  = $this->request->getPost('catatan');

        $db = \Config\Database::connect();
        $data = $db->table('penelitian')
            ->select('penelitian.*, users.email, users.email_instansi, users.fullname, users.username')
            ->join('users', 'users.id = penelitian.user_id', 'left')
            ->where('penelitian.penelitian_id', $id)
            ->get()
            ->getRow();

        if (!$data) {
            return redirect()->back()->with('error', 'Data penelitian tidak ditemukan.');
        }

        // hapus file absensi kalau ada
        if (!empty($data->absensi)) {
            $filePath = FCPATH . 'uploads/absensi-penelitian/' . $data->absensi;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // update database
        $this->penelitianModel->update($id, [
            'absensi' => null,
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
        $email->setSubject('Hasil Validasi Absensi Penelitian di PT Semen Padang');
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


    public function setRFID()
    {
        $penelitianId = $this->request->getPost('penelitian_id');
        $rfidId   = $this->request->getPost('rfid_id');
        
        $penelitian = $this->penelitianModel->find($penelitianId);

        if (!$penelitian) {
            return redirect()->back()->with('error', 'Data penelitian tidak ditemukan.');
        }

        if ($penelitianId && $rfidId) {
            // Simpan ke tabel rfid_assignment
            $this->rfidAssignmentModel->insert([
                'tipe'      => 'penelitian',
                'relasi_id'      => $penelitianId,
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
        $penelitianId = $assignment['relasi_id'];

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
                'tipe'     => 'penelitian',
                'relasi_id'     => $penelitianId,
                'rfid_id'       => $newRfidId,
                'tanggal_pinjam'=> date('Y-m-d H:i:s'),
                'status'        => 'aktif'
            ]);
            $this->rfidModel->update($newRfidId, ['status' => 'assigned']);
        }

        return redirect()->back()->with('success', 'Pengembalian RFID berhasil disimpan.');
    }

    public function finalisasi($id)
    {
        $penelitian = $this->penelitianModel->find($id);

        if (!$penelitian) {
            return redirect()->back()->with('error', 'Data penelitian tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        
       $this->penelitianModel->update($id, [
            'finalisasi' => date('Y-m-d H:i:s'), 
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal Finalisasi.');
        }

        return redirect()->back()->with('success', 'Finalisasi Berhasil');
    }

}
