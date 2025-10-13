<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\RfidModel;
use App\Models\UserModel;

class RfidController extends BaseController
{
    protected $rfidModel;
    protected $userModel;

    public function __construct()
    {
        $this->rfidModel = new RfidModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $status = $this->request->getGet('status');

        $builder = $this->rfidModel->orderBy('rfid_no');

        if(!empty($status)){
            $builder->where('status', $status);
        }

        $rfid = $builder->findAll();
        return view('admin/kelola_rfid', ['rfid' => $rfid]);
    }

    public function export(){
        $status = $this->request->getGet('status');
        $builder = $this->rfidModel->orderBy('rfid_no');

        if(!empty($status)){
            $builder->where('status', $status);
        }

        $rfid = $builder->get()->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Nomor RFID');
        $sheet->setCellValue('B1', 'Status');

        // Isi
        $row = 2;
        foreach ($rfid as $d) {
            $sheet->setCellValue('A' . $row, $d['rfid_no']);
            $sheet->setCellValue('B' . $row, $d['status']);
            $row++;
        }

        // Download file
        $filename = 'data_rfid_'. date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;

    }

    public function save()
    {

        $data = [
            'rfid_no' => $this->request->getPost('rfid_no'),
            'status' => $this->request->getPost('status'),
        ];

        $this->rfidModel->insert($data);

        return redirect()->back()->with('success', 'RFID berhasil ditambahkan.');
    }

    public function update($id)
    {
        $data = [
            'rfid_no'  => $this->request->getPost('rfid_no'),
            'status'  => $this->request->getPost('status'),
        ];

        $this->rfidModel->update($id, $data);

        return redirect()->back()->with('success', 'RFID berhasil diperbarui.');
    }

    public function delete($id)
    {
        $rfid = $this->rfidModel->find($id);
        if (!$rfid) {
            return redirect()->back()->with('error', 'RFID tidak ditemukan.');
        }

        // Hapus data
        $this->rfidModel->delete($id);

        return redirect()->back()->with('success', 'RFID berhasil dihapus.');
    }
}
