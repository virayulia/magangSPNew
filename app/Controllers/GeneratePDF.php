<?php

namespace App\Controllers;
use Dompdf\Dompdf;
use DateTime;

use App\Models\MagangModel;
use Myth\Auth\Models\UserModel;
use App\Libraries\GenerateSuratPenerimaan;
use App\Libraries\MY_TCPDF as TCPDF;

class GeneratePDF extends BaseController
{
    public function suratPenerimaan($id = null, $saveToFile = false)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('PT. Semen Padang');
        $pdf->SetTitle('PDF Surat Penerimaan Magang');
        $pdf->SetSubject('Surat Penerimaan Magang Semen Padang');
        $pdf->SetKeywords('TCPDF, PDF, surat, semenpadang.online');

        $pdf->SetHeaderData('', '', '', '');
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        $pdf->SetMargins(20, 25, 20);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('times', '', 11);
        $pdf->AddPage();

        // Load model dan data
        $userModel = new \Myth\Auth\Models\UserModel();
        $pendaftaranModel = new \App\Models\MagangModel();

        $pendaftaran = $pendaftaranModel->find($id);
        if (!$pendaftaran) {
            return null;
        }

        $user = $userModel
            ->select('users.*, instansi.nama_instansi, jurusan.nama_jurusan')
            ->join('instansi', 'instansi.instansi_id = users.instansi_id', 'left')
            ->join('jurusan', 'jurusan.jurusan_id = users.jurusan_id', 'left')
            ->where('users.id', $pendaftaran['user_id'])
            ->first();
        $data['user_data'] = $user;
        $data['pendaftaran'] = $pendaftaran;

        $html = view('user/templateSuratPenerimaan', $data);
        $pdf->writeHTML($html);

        $fileName = 'surat-penerimaan-' . $user->fullname . '-' . date('YmdHis') . '.pdf';

        if ($saveToFile) {
            $filePath = WRITEPATH . 'uploads/' . $fileName;
            $pdf->Output($filePath, 'F'); // Save to file
            return $filePath;
        } else {
            $this->response->setContentType('application/pdf');
            $pdf->Output($fileName, 'I'); // Show in browser
            exit;
        }
    }

// public function suratPenerimaan($id = null, $saveToFile = false)
// {
//     $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

//     $pdf->SetCreator(PDF_CREATOR);
//     $pdf->SetAuthor('PT. Semen Padang');
//     $pdf->SetTitle('PDF Surat Penerimaan Magang');
//     $pdf->SetSubject('Surat Penerimaan Magang Semen Padang');
//     $pdf->SetKeywords('TCPDF, PDF, surat, semenpadang.online');

//     // Disable header/footer
//     $pdf->setPrintHeader(false);
//     $pdf->setPrintFooter(false);

//     // Set margin kiri, atas, kanan (atas 40mm)
//     $pdf->SetMargins(20, 40, 20);
//     $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//     $pdf->setFontSubsetting(true);

//     // Set font Times ukuran 10
//     $pdf->SetFont('times', '', 10);

//     $pdf->AddPage();

//     // Load model dan data
//     $userModel = new \Myth\Auth\Models\UserModel();
//     $pendaftaranModel = new \App\Models\MagangModel();

//     $pendaftaran = $pendaftaranModel->find($id);
//     if (!$pendaftaran) {
//         return null;
//     }

//     $user = $userModel->find($pendaftaran['user_id']);
//     $data['user_data'] = $user;
//     $data['pendaftaran'] = $pendaftaran;

//     $html = view('user/templateSuratPenerimaan', $data);
//     $pdf->writeHTML($html);

//     $fileName = 'surat-penerimaan-' . $user->fullname . '-' . date('YmdHis') . '.pdf';

//     if ($saveToFile) {
//         $filePath = WRITEPATH . 'uploads/' . $fileName;
//         $pdf->Output($filePath, 'F'); // Save to file
//         return $filePath;
//     } else {
//         $this->response->setContentType('application/pdf');
//         $pdf->Output($fileName, 'I'); // Show in browser
//         exit;
//     }
// }


public function generateAndSavePDF($userId)
{
    $userModel = new \Myth\Auth\Models\UserModel();
    $pendaftaranModel = new \App\Models\MagangModel();

    $user = $userModel->find($userId);
    $pendaftaran = $pendaftaranModel->where('user_id', $userId)->first();

    if (!$user || !$pendaftaran) {
        return null;
    }

    $data['user_data'] = $user;
    $data['pendaftaran'] = $pendaftaran;

    $html = view('user/templateSuratPenerimaan', $data);

    $pdf = new \App\Libraries\MY_TCPDF();
    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false, '');
    $filename = WRITEPATH . 'surat_penerimaan/surat_' . $user->id . '_' . time() . '.pdf';
    $pdf->Output($filename, 'F'); // Save file

    return $filename;
}


    // public function suratPenerimaan($id = null)
    // {
    //     // create new PDF document
    //     $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    //     // set document information
    //     $pdf->SetCreator(PDF_CREATOR);
    //     $pdf->SetAuthor('PT. Semen Padang');
    //     $pdf->SetTitle('PDF Surat Penerimaan Magang');
    //     $pdf->SetSubject('Surat Penerimaan Magang Semen Padang');
    //     $pdf->SetKeywords('TCPDF, PDF, surat, semenpadang.online');

    //     // set default header data
    //     $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, array(0, 64, 255), array(0, 64, 128));
    //     $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));

    //     // set header and footer fonts
    //     $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    //     $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    //     // set auto page breaks
    //     $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //     // set image scale factor
    //     $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    //     // set default font subsetting mode
    //     $pdf->setFontSubsetting(true);

    //     // Set font
    //     // dejavusans is a UTF-8 Unicode font, if you only need to
    //     // print standard ASCII chars, you can use core fonts like
    //     // helvetica or times to reduce file size.
    //     $pdf->SetFont('helvetica', '', 10, '', true);
    //     $pdf->SetMargins(20, 18, 20);


    //     // Add a page
    //     // This method has several options, check the source code documentation for more information.
    //     $pdf->AddPage();

    //     $userId = session()->get('user_id'); // Ambil ID pengguna dari session

    //     // Inisialisasi model
    //     $userModel = new UserModel();
    //     $pendaftaranModel = new MagangModel();

    //     // Ambil data profil pengguna
    //     $data['user_data'] = $userModel->find($userId);
    //     $fullname = $data['user_data']->fullname;

    //     // Ambil data pendaftaran terkait pengguna
    //     $data['pendaftaran'] = $pendaftaranModel->where('user_id', $userId)->first();

    //     $dateTime = new DateTime('now'); // Waktu sekarang
    //     $datenow = $dateTime->format('Y-m-d-His',);

    //     $html = view('user/templateSuratPenerimaan', $data);

    //     // Print text using writeHTMLCell()
    //     $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

    //     // ---------------------------------------------------------
    //     $this->response->setContentType('application/pdf');
    //     // Close and output PDF document
    //     // This method has several options, check the source code documentation for more information.
    //     $pdf->Output('surat-penerimaan-'.$fullname.'-'. $datenow . '.pdf', 'I');
    // }

}
