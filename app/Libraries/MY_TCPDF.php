<?php

namespace App\Libraries;

use TCPDF;

class MY_TCPDF extends TCPDF
{

    //Page header
    public function Header()
    {
        // Logo
        $logoSIG = ROOTPATH . 'public/assets/img/SIG_logo.png';  
        $logoSP  = ROOTPATH . 'public/assets/img/SP_logo2.png';

        // Ukuran logo
        $logoWidth = 15; // width dalam satuan mm

        // Posisi kiri atas untuk logo SIG
        $this->Image($logoSIG, 5, 5, $logoWidth); // (x=10, y=10)

        // Posisi kanan atas untuk logo SP
        $pageWidth = $this->getPageWidth();
        $this->Image($logoSP, $pageWidth - 5 - $logoWidth, 5, $logoWidth); 
        // (x=lebar halaman - margin kanan - lebar gambar, y=10)
    }
    
    // Page footer
    public function Footer()
    {
        // --- SETTING AREA BAWAH ---
        $yPos = $this->getPageHeight() - 50; // Y 15 mm dari bawah

        // Logo kiri bawah
        $this->Image(
            FCPATH . 'assets/img/go-beyond-lg.png', // Ganti dengan path sesuai
            10,      // X posisi (10mm dari kiri)
            $yPos,   // Y posisi
            20       // Lebar gambar
        );

        // Nama Perusahaan di bawah logo
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(0, 0, 0); // Warna hitam

        // Y posisi text: sedikit di bawah logo (sesuaikan)
        $this->SetXY(10, $yPos + 15);
        $this->Cell(0, 4, 'PT Semen Padang', 0, 1, 'L', false);

        // Alamat perusahaan
        $this->SetXY(10, $yPos + 18);
        $this->Cell(0, 4, 'Jalan Raya Indarung, Padang 25237 Sumatera Barat. Telp. (0751) 815-250 Fax. (0751) 815-590 www.semenpadang.co.id', 0, 1, 'L', false);

        // Nomor halaman di tengah bawah
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}
