<?php

if (!function_exists('format_tanggal_indonesia')) {
    function format_tanggal_indonesia($tanggal)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $tanggal_angka = date('d', strtotime($tanggal));
        $bulan_angka = (int)date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));

        return $tanggal_angka . ' ' . $bulan[$bulan_angka] . ' ' . $tahun;
    }
}

if (!function_exists('format_tanggal')) {
    function format_tanggal($tanggal)
    {
        $bulan = [
            1 => '01', '02', '03', '04', '05', '06',
            '07', '08', '09', '10', '11', '12'
        ];

        $tanggal_angka = date('d', strtotime($tanggal));
        $bulan_angka = (int)date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));

        return $tanggal_angka . '-' . $bulan[$bulan_angka] . '-' . $tahun;
    }
}
if (!function_exists('format_tanggal_jam')) {
    function format_tanggal_jam($tanggal)
    {
        $bulan = [
            1 => '01', '02', '03', '04', '05', '06',
            '07', '08', '09', '10', '11', '12'
        ];

        $timestamp = strtotime($tanggal);

        $tanggal_angka = date('d', $timestamp);
        $bulan_angka = (int)date('m', $timestamp);
        $tahun = date('Y', $timestamp);
        $jam = date('H:i', $timestamp); 

        return $tanggal_angka . '-' . $bulan[$bulan_angka] . '-' . $tahun . ', ' . $jam;
    }
}

if (!function_exists('format_tanggal_singkat')) {
    function format_tanggal_singkat($tanggal)
    {
        $bulan = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $tanggal_angka = date('d', strtotime($tanggal));
        $bulan_angka = (int)date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));

        return $tanggal_angka . ' ' . $bulan[$bulan_angka] . ' ' . $tahun;
    }
}

if (!function_exists('format_tanggal_indonesia_dengan_jam')) {
    function format_tanggal_indonesia_dengan_jam($tanggal)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $timestamp = strtotime($tanggal);

        $tanggal_angka = date('d', $timestamp);
        $bulan_angka = (int)date('m', $timestamp);
        $tahun = date('Y', $timestamp);
        $jam = date('H:i', $timestamp); 

        return $tanggal_angka . ' ' . $bulan[$bulan_angka] . ' ' . $tahun . ', ' . $jam;
    }
}

if (!function_exists('format_bulan_indonesia')) {
    function format_bulan_indonesia($tanggal)
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $tanggal_angka = date('d', strtotime($tanggal));
        $bulan_angka = (int)date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));

        return $bulan[$bulan_angka] . ' ' . $tahun;
    }
}

if (!function_exists('format_bulan_singkat')) {
    function format_bulan_singkat($tanggal)
    {
        $bulan = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        $tanggal_angka = date('d', strtotime($tanggal));
        $bulan_angka = (int)date('m', strtotime($tanggal));
        $tahun = date('Y', strtotime($tanggal));

        return $bulan[$bulan_angka] . ' ' . $tahun;
    }
}

if (!function_exists('nama_bulan_indonesia')) {
    function nama_bulan_indonesia($bulan)
    {
        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return $bulanIndo[(int)$bulan] ?? '';
    }
}
