<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\KuotaunitModel;
use App\Models\UserModel;
use App\Models\MagangModel;
use App\Models\UnitKerjaModel;

class KuotaUnit extends BaseController
{
    protected $kuotaUnitModel;
    protected $userModel;
    protected $magangModel;
    protected $unitKerjaModel;

    public function __construct()
    {
        $this->kuotaUnitModel = new KuotaunitModel();
        $this->userModel = new UserModel();
        $this->magangModel = new MagangModel();
        $this->unitKerjaModel = new UnitKerjaModel();

    }
    public function index()
    {
        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        // Ambil periode aktif saat ini
        $periode = $db->table('periode_magang')
            ->where('tanggal_buka <=', $today)
            ->where('tanggal_tutup >=', $today)
            ->orderBy('tanggal_buka', 'DESC')
            ->limit(1)
            ->get()
            ->getRow();

           

        // Jika tidak ada periode aktif, tampilkan periode bulan berjalan
        if (!$periode) {
            $firstDay = date('Y-m-01');
            $lastDay  = date('Y-m-t');
            $periode = (object)[
                'tanggal_buka' => $firstDay,
                'tanggal_tutup' => $lastDay
            ];
        }

        $data['kuota_unit'] = $this->magangModel->getSisaKuota();

        $data['periode'] = $periode;
        $data['unit_kerja'] = $this->unitKerjaModel->findAll();
        // dd($data);
   
        return view('admin/kelola_kuota', $data);
    }

    public function save()
    {

        $data = [
            'unit_id' => $this->request->getPost('unit_id'),
            'tingkat_pendidikan' => $this->request->getPost('tingkat_pendidikan'),
            'kuota' => $this->request->getPost('kuota'),
        ];

        $this->kuotaUnitModel->insert($data);

        return redirect()->back()->with('success', 'Kuota Unit berhasil ditambahkan.');
    }
}
