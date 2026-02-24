<?php

namespace App\Controllers\Pembimbing;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PenelitianModel;
use App\Models\UnitKerjaModel;

class PenelitianController extends BaseController
{
    protected $penelitianModel;
    protected $unitKerjaModel;

    public function __construct()
    {
        $this->penelitianModel = new PenelitianModel();
        $this->unitKerjaModel = new UnitKerjaModel();
    }

    public function approveUnit()
    {
        $db = \Config\Database::connect();

        // Ambil semua unit yang dipegang oleh pembimbing yang login
        $userId = user_id();
        // $unitPembimbing = $db->table('unit_user')
        //     ->select('unit_id')
        //     ->where('user_id', $userId)
        //     ->get()
        //     ->getResultArray();

        $unitPembimbing = $db->table('unit_user')
            ->select('unit_kerja.unit_id, unit_kerja.unit_kerja')
            ->join('unit_kerja', 'unit_kerja.unit_id = unit_user.unit_id')
            ->where('unit_user.user_id', $userId)
            ->get()
            ->getResultArray();
        

        $unitIds = array_column($unitPembimbing, 'unit_id');

        // Ambil eselon user login
        $userLogin = $db->table('users')
            ->select('id, fullname, eselon')
            ->where('id', $userId)
            ->get()
            ->getRowArray();

        // Jika tidak ada unit, tampilkan kosong
        if (empty($unitIds)) {
            return view('pembimbing/penilaian', ['peserta' => [], 'pembimbing' => []]);
        }

        // Join ke tabel users untuk ambil nama
        $builder = $db->table('penelitian p')
            ->select('p.*, u.fullname, u.email, j.nama_jurusan, uk.unit_kerja, GROUP_CONCAT(k.keyword_nama SEPARATOR ", ") as keywords')
            ->join('users u', 'u.id = p.user_id')
            ->join('jurusan j', 'j.jurusan_id = u.jurusan_id')
            ->join('unit_kerja uk', 'uk.unit_id = p.unit_id')
            ->join('penelitian_keyword pk','pk.penelitian_id = p.penelitian_id')
            ->join('keyword k','k.keyword_id = pk.keyword_id')
            ->whereIn('p.status_akhir', ['pendaftaran', 'proses'])
            ->where('p.unit_id !=', NULL)
            ->where('p.approve_unit', NULL)
            ->groupBy('p.penelitian_id')
            ->orderBy('p.tanggal_daftar', 'ASC');

        $penelitian = $builder->get()->getResult();

        $unitKerja = $this->unitKerjaModel->findAll();

        $today = new \DateTime();
        $dates = [];

        for ($i = 1; $i <= 3; $i++) {
            // Geser ke bulan ke-i dari sekarang
            $month = (clone $today)->modify("+{$i} month");
            $year = $month->format('Y');
            $monthNum = $month->format('m');

            // Buat dua tanggal: awal dan pertengahan
            $awal = new \DateTime("$year-$monthNum-01");
            $tengah = new \DateTime("$year-$monthNum-15");

            // Cek apakah jatuh di Sabtu atau Minggu, kalau iya geser ke Senin berikutnya
            foreach ([$awal, $tengah] as $tgl) {
                $hari = $tgl->format('N'); // 6=Sabtu, 7=Minggu
                if ($hari == 6) {
                    $tgl->modify('+2 days'); // Sabtu → Senin
                } elseif ($hari == 7) {
                    $tgl->modify('+1 day'); // Minggu → Senin
                }

                $dates[] = $tgl->format('Y-m-d');
            }
        }
        


        $data = [
            'title' => 'Approval Unit Penelitian',
            'penelitian' => $penelitian,
            'unitKerja'  => $unitKerja,
            'pilihanTanggal'  => $dates,
            'userLogin' => $userLogin,
            'unitPembimbing' => $unitPembimbing


        ];

        return view('pembimbing/kelola_approval_unit', $data);
    }

    public function getRekomendasiUnit($penelitian_id)
    {
        $penelitian = $this->penelitianModel
            ->select("penelitian.*, GROUP_CONCAT(k.keyword_nama SEPARATOR ', ') AS keywords,
                        u.fullname, j.nama_jurusan")
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


        // Ambil semua unit kerja yang punya keyword cocok
        $sql = "
                SELECT 
                    uk.unit_id, 
                    uk.unit_kerja,
                    COUNT(k.keyword_id) AS match_count
                FROM unit_kerja uk
                LEFT JOIN unit_keyword ukw ON uk.unit_id = ukw.unit_id
                LEFT JOIN keyword k 
                    ON k.keyword_id = ukw.keyword_id 
                    AND k.keyword_nama IN (
                        SELECT k2.keyword_nama
                        FROM penelitian_keyword pk2
                        JOIN keyword k2 ON k2.keyword_id = pk2.keyword_id
                        WHERE pk2.penelitian_id = ?
                    )
                GROUP BY uk.unit_id
                ORDER BY match_count DESC
            ";


        $db = \Config\Database::connect();
        $units = $db->query($sql, [$penelitian_id])->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'penelitian' => $penelitian,
            'units' => $units
        ]);
    }

    public function approveUnitSave()
    {
        $ids = $this->request->getPost('penelitian_ids');
        $status = $this->request->getPost('status');
        $catatanReject = $this->request->getPost('catatan_reject');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($ids as $id) {
            // Pastikan data penelitian ada
            $penelitian = $this->penelitianModel->find($id);
            if (!$penelitian) {
                continue;
            }

            if ($status === 'approve') {
                // Update status penelitian
                $this->penelitianModel
                    ->where('penelitian_id', $id)
                    ->update(null, [
                        'status_seleksi' => 'Diterima',
                        'tanggal_seleksi' => date('Y-m-d H:i:s'),
                        'status_akhir' => 'proses',
                        'approve_unit' => 'Y',
                        'tanggal_approve_unit' => date('Y-m-d H:i:s')
                    ]);

                // Ambil data lengkap untuk pengiriman email
                $dataPenelitian = $db->table('penelitian')
                    ->select('penelitian.*, unit_kerja.unit_kerja, users.email, users.fullname')
                    ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
                    ->join('users', 'users.id = penelitian.user_id', 'left')
                    ->where('penelitian.penelitian_id', $id)
                    ->get()
                    ->getRow();

                if ($dataPenelitian && !empty($dataPenelitian->email)) {
                    // Kirim email notifikasi
                    $email = \Config\Services::email();
                    $unit_id = 44;
                    $signature = getSignature($unit_id);
                    $email->setTo($dataPenelitian->email);
                    $email->setSubject('Penerimaan Penelitian di PT Semen Padang');
                    $email->setMailType('html');
                    $email->setMessage(view('emails/penerimaan_penelitian', [
                        'nama' => $dataPenelitian->fullname,
                        'unit' => $dataPenelitian->unit_kerja,
                        'tanggal_masuk' => $dataPenelitian->tanggal_masuk,
                        'tanggal_selesai' => $dataPenelitian->tanggal_selesai,
                        'signature' => $signature
                    ]));
                    $email->send();
                }

            } elseif ($status === 'reject') {
                // Update penolakan dengan catatan
                $this->penelitianModel
                    ->where('penelitian_id', $id)
                    ->update(null, [
                        'approve_unit' => 'N',
                        'cttn_approve_unit' => $catatanReject,
                        'tanggal_approve_unit' => date('Y-m-d H:i:s'),
                    ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }

        $pesan = $status === 'approve'
            ? 'Penelitian berhasil disetujui dan email telah dikirim ke mahasiswa.'
            : 'Penelitian berhasil ditolak dengan catatan.';

        return redirect()->back()->with('success', $pesan);
    }

    public function approvePenelitian()
    {
        $db = \Config\Database::connect();

        // Ambil semua unit yang dipegang oleh pembimbing yang login
        $userId = user_id();
        // $unitPembimbing = $db->table('unit_user')
        //     ->select('unit_id')
        //     ->where('user_id', $userId)
        //     ->get()
        //     ->getResultArray();
        $unitPembimbing = $db->table('unit_user')
            ->select('unit_kerja.unit_id, unit_kerja.unit_kerja')
            ->join('unit_kerja', 'unit_kerja.unit_id = unit_user.unit_id')
            ->where('unit_user.user_id', $userId)
            ->get()
            ->getResultArray();

        $unitIds = array_column($unitPembimbing, 'unit_id');

        // Jika tidak ada unit, tampilkan kosong
        if (empty($unitIds)) {
            return view('pembimbing/approve_nilai', ['peserta' => []]);
        }

        // Ambil peserta magang dari semua unit tersebut yang sudah dinilai, tapi belum approve
        $builder = $this->penelitianModel->select('
                            penelitian.*,
                            unit_kerja.unit_kerja,
                            users.*,
                            jurusan.nama_jurusan,
                            instansi.nama_instansi,
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
                            feedback_penelitian.feedbackp_id, 
                        ')
                        ->join('users', 'users.id = penelitian.user_id')
                        ->join('instansi', 'instansi.instansi_id = users.instansi_id')
                        ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id')
                        ->join('provinces AS province_ktp', 'province_ktp.id = users.province_id', 'left')
                        ->join('provinces AS province_dom', 'province_dom.id = users.provinceDom_id', 'left')
                        ->join('regencies AS city_ktp', 'city_ktp.id = users.city_id', 'left')
                        ->join('regencies AS city_dom', 'city_dom.id = users.cityDom_id', 'left')
                        ->join('unit_kerja', 'penelitian.unit_id = unit_kerja.unit_id')
                        ->join('jawaban_safety', 'penelitian.penelitian_id = jawaban_safety.relasi_id AND jawaban_safety.tipe = "penelitian"', 'left')
                        ->join('feedback_penelitian', 'feedback_penelitian.penelitian_id = penelitian.penelitian_id', 'left')
                        ->join("(
                                    SELECT r1.*
                                    FROM rfid_assignment r1
                                    JOIN (
                                        SELECT relasi_id, tipe, MAX(assignment_id) AS max_assign
                                        FROM rfid_assignment
                                        WHERE tipe = 'penelitian'
                                        GROUP BY relasi_id, tipe
                                    ) r2 ON r1.relasi_id = r2.relasi_id AND r1.tipe = r2.tipe AND r1.assignment_id = r2.max_assign
                                ) AS ra", 'ra.relasi_id = penelitian.penelitian_id', 'left')
                        ->join('rfid', 'rfid.id_rfid = ra.rfid_id', 'left')
                        ->where('penelitian.status_akhir', 'penelitian')
                        ->where('penelitian.finalisasi !=', null)
                        ->groupBy('penelitian.penelitian_id');

        $peserta = $builder->get()->getResultArray();


        return view('pembimbing/approve-penelitian', ['peserta' => $peserta,'unitPembimbing' => $unitPembimbing, ]);
    }

    public function setApprovePenelitian()
    {
        $penelitianIds = $this->request->getPost('penelitian_ids');
        $userId    = user_id();

        if (empty($penelitianIds)) {
            return redirect()->back()->with('error', 'Tidak ada peserta yang dipilih untuk approve.');
        }

        $db      = \Config\Database::connect();
        $email   = \Config\Services::email();
        $tanggal = date('Y-m-d H:i:s');

        // Ambil data peserta
        $pesertaList = $db->table('penelitian')
            ->select('penelitian.*, users.*, unit_kerja.unit_kerja, jurusan.nama_jurusan, instansi.nama_instansi')
            ->join('users', 'users.id = penelitian.user_id', 'left')
            ->join('jurusan', 'users.jurusan_id = jurusan.jurusan_id', 'left')
            ->join('instansi', 'users.instansi_id = instansi.instansi_id', 'left')
            ->join('unit_kerja', 'unit_kerja.unit_id = penelitian.unit_id', 'left')
            ->whereIn('penelitian.penelitian_id', $penelitianIds)
            ->get()
            ->getResult();

        foreach ($pesertaList as $data) {
            // Update data approve
            $updateData = [
                'ka_unit_approve' => 1,
                'tanggal_approve' => $tanggal,
                'status_akhir'    => 'lulus',
            ];
            $this->penelitianModel->update($data->penelitian_id, $updateData);

            // Kirim email
            $toEmail = $data->email;
            if (!empty($toEmail)) {
                $email->clear(true);
                $email->setTo($toEmail);
                $unit_id = 44;
                $signature = getSignature($unit_id);    
                $email->setSubject('Surat Keterangan Penelitian Anda Sudah Tersedia');
                $email->setMailType('html');

                $email->setMessage(view('emails/approve_surket', [
                    'nama'            => $data->fullname ?? $data->username,
                    'unit'            => $data->unit_kerja ?? 'Unit terkait',
                    'jurusan'         => $data->nama_jurusan,
                    'instansi'        => $data->nama_instansi,
                    'tanggal_masuk'   => $data->tanggal_masuk,
                    'tanggal_selesai' => $data->tanggal_selesai,
                    'signature' => $signature
                ]));

                if (!$email->send()) {
                    log_message('error', "Gagal kirim email surat keterangan ID {$data->penelitian_id}: " . print_r($email->printDebugger(), true));
                }
            }
        }

        return redirect()->back()->with('success', count($penelitianIds).' peserta berhasil di-approve dan email sudah dikirim.');
    }

    public function pesertaPenelitian()
    {
        $bulanMasuk  = $this->request->getGet('bulan_masuk');
        $bulanKeluar = $this->request->getGet('bulan_keluar');
        $tahun       = $this->request->getGet('tahun');

        $db = \Config\Database::connect();
        $userId = user_id();

        $unitPembimbing = $db->table('unit_user')
            ->select('unit_kerja.unit_id, unit_kerja.unit_kerja')
            ->join('unit_kerja', 'unit_kerja.unit_id = unit_user.unit_id')
            ->where('unit_user.user_id', $userId)
            ->get()
            ->getResultArray();
        

        $unitIds = array_column($unitPembimbing, 'unit_id');

        $builder = $this->penelitianModel->select('
                penelitian.*, 
                unit_kerja.unit_kerja,
                peserta.fullname AS nama_peserta, peserta.nisn_nim,
                pembimbing.fullname AS nama_pembimbing,
                jurusan.nama_jurusan, instansi.nama_instansi
            ')
            ->join('users peserta', 'peserta.id = penelitian.user_id') 
            ->join('users pembimbing', 'pembimbing.id = penelitian.pembimbing_id', 'left') 
            ->join('jurusan', 'jurusan.jurusan_id = peserta.jurusan_id')
            ->join('instansi', 'instansi.instansi_id = peserta.instansi_id')
            ->join('unit_kerja', 'penelitian.unit_id = unit_kerja.unit_id')
            ->where('penelitian.status_akhir', 'penelitian')
            ->whereIn('penelitian.unit_id', $unitIds);

        if (!empty($bulanMasuk)) {
            $builder->where('MONTH(penelitian.tanggal_masuk)', $bulanMasuk);
        }

        if (!empty($bulanKeluar)) {
            $builder->where('MONTH(penelitian.tanggal_selesai)', $bulanKeluar);
        }

        if (!empty($tahun)) {
            $builder->groupStart()
                    ->where('YEAR(penelitian.tanggal_masuk)', $tahun)
                    ->orWhere('YEAR(penelitian.tanggal_selesai)', $tahun)
                    ->groupEnd();
        }

        $data = $builder->findAll();
       
        $unitList = $this->unitKerjaModel->findAll();


        // Ambil semua unit yang dipegang oleh pembimbing yang login
       
        // $unitPembimbing = $db->table('unit_user')
        //     ->select('unit_id')
        //     ->where('user_id', $userId)
        //     ->get()
        //     ->getResultArray();

        

        // Ambil eselon user login
        $userLogin = $db->table('users')
            ->select('id, fullname, eselon')
            ->where('id', $userId)
            ->get()
            ->getRowArray();

        // Jika tidak ada unit, tampilkan kosong
        if (empty($unitIds)) {
            return view('pembimbing/penilaian', ['peserta' => [], 'pembimbing' => []]);
        }

        $pembimbing = $db->table('users')
            ->select('users.id, users.fullname, users.email, unit_user.unit_id')
            ->join('unit_user', 'unit_user.user_id = users.id')
            ->whereIn('unit_user.unit_id', $unitIds)
            ->get()
            ->getResultArray();
        
        return view('pembimbing/peserta_penelitian', [
            'data' => $data,
            'unitList' => $unitList,
            'pembimbing' => $pembimbing,
            'userLogin' => $userLogin,
            'unitPembimbing' => $unitPembimbing
        ]);
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

    public function assignPembimbing($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('penelitian');

        $pembimbing_id = $this->request->getPost('pembimbing_id');

        if (empty($pembimbing_id)) {
            return redirect()->back()->with('error', 'Silakan pilih pembimbing.');
        }

        // Update pembimbing_id pada magang
        $builder->where('penelitian_id', $id)
                ->update(['pembimbing_id' => $pembimbing_id]);

        if ($db->affectedRows() > 0) {
            return redirect()->back()->with('success', 'Pembimbing berhasil ditambahkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan pembimbing.');
        }
    }

    public function updatePembimbing($id)
    {
        $pembimbing_id = $this->request->getPost('pembimbing_id');

        $this->penelitianModel->update($id, [
            'pembimbing_id' => $pembimbing_id
        ]);

        return redirect()->back()->with('success', 'Pembimbing berhasil diperbarui.');
    }

    public function approveFormulir()
    {
        $id = $this->request->getPost('id');

        $this->penelitianModel->update($id, [
            'status_pembimbing' => 'Disetujui',
            'catatan_pembimbing' => null
        ]);

        // return redirect()->back()->with('success', 'Form penelitian telah disetujui.');


        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Form penelitian telah disetujui.'
        ]);
    }

    public function rejectFormulir()
    {
        $id = $this->request->getPost('penelitian_id');
        $catatan = $this->request->getPost('catatan');

        $penelitian = $this->penelitianModel->find($id);
        $this->penelitianModel->update($id, [
            'status_pembimbing' => 'Ditolak',
            'catatan_pembimbing' => $catatan
        ]);

        // Kirim email ke peserta
        $email = \Config\Services::email();
        $email->setTo($penelitian['email_peserta']);
        $email->setSubject('Penolakan Form Penelitian');
        $email->setMessage("
            Halo {$penelitian['nama_peserta']},<br><br>
            Form penelitian Anda telah <b>ditolak</b> oleh pembimbing.<br>
            Catatan: <i>{$catatan}</i><br><br>
            Silakan perbaiki dan ajukan kembali.
        ");
        $email->send();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Form penelitian ditolak dan email telah dikirim ke peserta.'
        ]);
    }



}
