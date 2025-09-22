<?php

namespace App\Controllers\Pembimbing;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\MagangModel;
use App\Models\UserModel;
use App\Models\PenilaianModel;
use App\Models\UnitKerjaModel;
use App\Models\RfidModel;

class MagangController extends BaseController
{
    protected $magangModel;
    protected $userModel;
    protected $unitKerjaModel;
    protected $penilaianModel;
    protected $rfidModel;

    public function __construct()
    {
        $this->magangModel = new MagangModel();
        $this->userModel = new UserModel();
        $this->penilaianModel = new PenilaianModel();
        $this->unitKerjaModel = new UnitKerjaModel();
        $this->rfidModel = new RfidModel();
    }

    public function penilaian()
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

        // Ambil peserta magang dari semua unit tersebut
        $builder = $db->table('magang')
            ->select('magang.magang_id, magang.tanggal_masuk, magang.tanggal_selesai, magang.pembimbing_id,
                    peserta.fullname AS nama_peserta, peserta.nisn_nim, instansi.nama_instansi, 
                    magang.laporan, magang.absensi,
                    penilaian.penilaian_id, penilaian.nilai_disiplin, penilaian.nilai_kerajinan, penilaian.nilai_tingkahlaku,
                    penilaian.nilai_kerjasama, penilaian.nilai_kreativitas, penilaian.nilai_kemampuankerja, 
                    penilaian.nilai_tanggungjawab, penilaian.nilai_penyerapan, penilaian.catatan, penilaian.tgl_penilaian,
                    penilaian.approve_kaunit, penilaian.tgl_disetujui, penilaian.approve_by,
                    pembimbing.fullname AS nama_pembimbing')
            ->join('users peserta', 'peserta.id = magang.user_id') 
            ->join('instansi', 'instansi.instansi_id = peserta.instansi_id')
            ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
            ->join('users pembimbing', 'pembimbing.id = magang.pembimbing_id', 'left') 
            ->where('magang.status_akhir', 'magang')
            ->whereIn('magang.unit_id', $unitIds);

         // 🔹 Filter tambahan kalau bukan eselon 2
        if ($userLogin['eselon'] != 2) {
            $builder->where('magang.pembimbing_id', $userId);
        }

        $peserta = $builder->get()->getResultArray();

        // 🔹 Ambil data pembimbing (users yang terhubung ke unit yang sama)
        $pembimbing = $db->table('users')
            ->select('users.id, users.fullname, users.email, unit_user.unit_id')
            ->join('unit_user', 'unit_user.user_id = users.id')
            ->whereIn('unit_user.unit_id', $unitIds)
            ->get()
            ->getResultArray();

        return view('pembimbing/penilaian', [
            'peserta' => $peserta,
            'pembimbing' => $pembimbing,
            'userLogin' => $userLogin,
            'unitPembimbing' => $unitPembimbing

        ]);

    }

    public function assignPembimbing($magang_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('magang');

        $pembimbing_id = $this->request->getPost('pembimbing_id');

        if (empty($pembimbing_id)) {
            return redirect()->back()->with('error', 'Silakan pilih pembimbing.');
        }

        // Update pembimbing_id pada magang
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

    public function save()
    {
        helper(['form']);

        $validation = \Config\Services::validation();
        $validation->setRules([
            'magang_id'        => 'required|is_natural_no_zero',
            'disiplin'         => 'required|in_list[60,70,80,90,100]',
            'kerajinan'        => 'required|in_list[60,70,80,90,100]',
            'tingkahlaku'      => 'required|in_list[60,70,80,90,100]',
            'kerjasama'        => 'required|in_list[60,70,80,90,100]',
            'kreativitas'      => 'required|in_list[60,70,80,90,100]',
            'kemampuankerja'   => 'required|in_list[60,70,80,90,100]',
            'tanggungjawab'    => 'required|in_list[60,70,80,90,100]',
            'penyerapan'       => 'required|in_list[60,70,80,90,100]',
            'catatan'          => 'permit_empty|string|max_length[1000]'
        ]);

        // Jalankan validasi
        if (! $validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->with('error', implode('<br>', $validation->getErrors()))
                ->withInput();
        }

        // Pastikan pembimbing login
        $pembimbingId = user_id();
        if (! $pembimbingId) {
            return redirect()->back()->with('error', 'Anda belum login sebagai pembimbing.');
        }

        // Ambil data dari form
        $data = [
            'magang_id'            => (int) $this->request->getPost('magang_id'),
            'pembimbing_id'        => $pembimbingId,
            'nilai_disiplin'       => (int) $this->request->getPost('disiplin'),
            'nilai_kerajinan'      => (int) $this->request->getPost('kerajinan'),
            'nilai_tingkahlaku'    => (int) $this->request->getPost('tingkahlaku'),
            'nilai_kerjasama'      => (int) $this->request->getPost('kerjasama'),
            'nilai_kreativitas'    => (int) $this->request->getPost('kreativitas'),
            'nilai_kemampuankerja' => (int) $this->request->getPost('kemampuankerja'),
            'nilai_tanggungjawab'  => (int) $this->request->getPost('tanggungjawab'),
            'nilai_penyerapan'     => (int) $this->request->getPost('penyerapan'),
            'catatan'              => $this->request->getPost('catatan'),
        ];

        // Hitung rata-rata otomatis
        $total = $data['nilai_disiplin'] + $data['nilai_kerajinan'] + $data['nilai_tingkahlaku'] +
                $data['nilai_kerjasama'] + $data['nilai_kreativitas'] + $data['nilai_kemampuankerja'] +
                $data['nilai_tanggungjawab'] + $data['nilai_penyerapan'];
        $data['nilai_rata2'] = $total / 8;

        $db = \Config\Database::connect();
        $db->transStart();

        // Cek apakah sudah ada penilaian untuk magang ini
        $penilaian = $this->penilaianModel
            ->where('magang_id', $data['magang_id'])
            ->first();

        if ($penilaian) {
            // Update penilaian
            $this->penilaianModel->update($penilaian['penilaian_id'], $data);
        } else {
            // Insert penilaian baru
            $data['tgl_penilaian'] = date('Y-m-d H:i:s');
            $this->penilaianModel->insert($data);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan penilaian.');
        }

        return redirect()->back()->with('success', 'Penilaian berhasil disimpan.');
    }

    public function bulkApprove()
    {
        $magangIds = $this->request->getPost('magang_ids');
        $status    = $this->request->getPost('status');
        $catatan   = $this->request->getPost('catatan_reject');

        if (empty($magangIds)) {
            return redirect()->back()->with('error', 'Tidak ada peserta yang dipilih.');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('penilaian');

        $data = [
            'tgl_disetujui' => date('Y-m-d H:i:s'),
            'approve_by'    => user_id(),
        ];

        if ($status === 'approve') {
            $data['approve_kaunit'] = 1;
            $data['catatan_approval'] = null;
        } elseif ($status === 'reject') {
            $data['approve_kaunit'] = 2;
            $data['catatan_approval'] = $catatan;
        } else {
            return redirect()->back()->with('error', 'Aksi tidak valid.');
        }

        $builder->whereIn('magang_id', $magangIds)->update($data);

        return redirect()->back()->with('success', 'Penilaian berhasil diproses untuk '.count($magangIds).' peserta.');
    }



    public function approve()
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
        $builder = $db->table('magang')
            ->select('magang.magang_id, magang.tanggal_masuk, magang.tanggal_selesai, magang.pembimbing_id,
                    magang.laporan, magang.absensi,
                    peserta.fullname AS nama_peserta, peserta.nisn_nim, instansi.nama_instansi, 
                    penilaian.penilaian_id, penilaian.nilai_disiplin, penilaian.nilai_kerajinan, penilaian.nilai_tingkahlaku,
                    penilaian.nilai_kerjasama, penilaian.nilai_kreativitas, penilaian.nilai_kemampuankerja, 
                    penilaian.nilai_tanggungjawab, penilaian.nilai_penyerapan, penilaian.catatan, penilaian.tgl_penilaian,
                    penilaian.approve_kaunit, penilaian.tgl_disetujui, penilaian.approve_by, penilaian.catatan_approval,
                    pembimbing.fullname AS nama_pembimbing')
            ->join('users peserta', 'peserta.id = magang.user_id') 
            ->join('instansi', 'instansi.instansi_id = peserta.instansi_id')
            ->join('penilaian', 'penilaian.magang_id = magang.magang_id', 'left')
            ->join('users pembimbing', 'pembimbing.id = magang.pembimbing_id', 'left') 
            ->where('magang.status_akhir', 'magang')
            ->whereIn('magang.unit_id', $unitIds)
            ->where('penilaian.approve_kaunit !=', 1); // Belum di-approve

        $peserta = $builder->get()->getResultArray();

        return view('pembimbing/approve', ['peserta' => $peserta,'unitPembimbing' => $unitPembimbing]);
    }

    public function saveApprove()
    {
        $magangId = $this->request->getPost('magang_id');
        $status   = $this->request->getPost('status');
        $catatan  = $this->request->getPost('catatan_reject');

        if (!$magangId) {
            return redirect()->back()->with('error', 'ID Magang tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        if ($status === 'approve') {
            $db->table('penilaian')
                ->where('magang_id', $magangId)
                ->update([
                    'approve_kaunit' => 1,
                    'tgl_disetujui'  => date('Y-m-d H:i:s'),
                    'approve_by'     => user_id(),
                    'catatan_approval' => null, // reset jika sebelumnya ada
                ]);
            return redirect()->back()->with('success', 'Penilaian berhasil diapprove.');
        } elseif ($status === 'reject') {
            $db->table('penilaian')
                ->where('magang_id', $magangId)
                ->update([
                    'approve_kaunit' => 2,
                    'tgl_disetujui'  => date('Y-m-d H:i:s'),
                    'approve_by'     => user_id(),
                    'catatan_approval' => $catatan,
                ]);
            return redirect()->back()->with('success', 'Penilaian ditolak dengan catatan.');
        }

        return redirect()->back()->with('error', 'Aksi tidak valid.');
    }

    public function approveMagang()
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
                        ->where('magang.status_akhir', 'magang')
                        ->where('magang.finalisasi !=', null)
                        ->groupBy('magang.magang_id');

        $peserta = $builder->get()->getResultArray();


        return view('pembimbing/approve-magang', ['peserta' => $peserta,'unitPembimbing' => $unitPembimbing, ]);
    }

    public function setApproveMagang()
    {
        $magangIds = $this->request->getPost('magang_ids');
        $userId    = user_id();

        if (!empty($magangIds)) {
            $this->magangModel->whereIn('magang_id', $magangIds)
                ->set([
                    'ka_unit_approve' => 1,
                    'tanggal_approve' => date('Y-m-d H:i:s'),
                    'status_akhir'    => 'lulus'
                ])
                ->update();

            return redirect()->back()->with('success', count($magangIds).' peserta berhasil di-approve.');
        }

        return redirect()->back()->with('error', 'Tidak ada peserta yang dipilih untuk approve.');
    }

    public function alumniMagang()
    {
        $bulan = $this->request->getGet('bulan');
        $tahun = $this->request->getGet('tahun');

        $db = \Config\Database::connect();
        $userId = user_id();
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
                        ->orderBy('magang.tanggal_approve')
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

        return view('admin/kelola_alumni', ['data' => $data, 'unitList' => $unitList, 'rfidList' => $rfid, 'unitPembimbing' => $unitPembimbing]);
    }

    // public function setApproveMagang()
    // {
    //     $magangId = $this->request->getPost('magang_id');
    //     $userId   = user_id(); // ID pembimbing ka unit A

    //     $this->magangModel->update($magangId, [
    //         'ka_unit_approve' => 1,
    //         'tanggal_approve' => date('Y-m-d H:i:s')
    //     ]);

    //     return redirect()->back()->with('success', 'Magang berhasil di-approve.');
    // }




}
