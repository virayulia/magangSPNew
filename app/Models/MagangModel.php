<?php

namespace App\Models;

use CodeIgniter\Model;

class MagangModel extends Model
{
    protected $table            = 'magang';
    protected $primaryKey       = 'magang_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id','unit_id','periode_id','durasi','tanggal_daftar',
                                    'status_seleksi', 'tanggal_seleksi','status_konfirmasi','tanggal_konfirmasi',
                                    // 'status_approval', 'tanggal_approval',
                                    'status_validasi_berkas','tanggal_validasi_berkas','status_berkas_lengkap',
                                    'tanggal_berkas_lengkap','pembimbing_id', 'tanggal_masuk','tanggal_selesai',
                                    'status_akhir', 'tanggal_setujui_pernyataan', 'cttn_berkas_lengkap', 'alasan_batal',
                                    'laporan','absensi', 'finalisasi', 'ka_unit_approve', 'tanggal_approve',
                                    'catatan_laporan', 'catatan_absensi'
                                    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    //GetSisaKuota Lama
    // public function getSisaKuota()
    // {
    //     $builder = $this->db->query("
    //        SELECT 
    //             ku.kuota_unit_id,
    //             ku.unit_id,
    //             uk.unit_kerja,
    //             ku.tingkat_pendidikan,
    //             ku.kuota,

    //             -- Jumlah diterima (status_akhir = proses)
    //             IFNULL(jumlah_diterima.jumlah, 0) AS jumlah_diterima_atau_magang,

    //             -- Jumlah pendaftar (status_akhir = pendaftaran)
    //             IFNULL(jumlah_pendaftar.jumlah, 0) AS jumlah_pendaftar,

    //             -- Sisa kuota
    //             (ku.kuota - IFNULL(jumlah_diterima.jumlah, 0)) AS sisa_kuota

    //         FROM 
    //             kuota_unit ku
    //         JOIN 
    //             unit_kerja uk ON ku.unit_id = uk.unit_id

    //         -- Subquery untuk jumlah diterima (status proses)
    //         LEFT JOIN (
    //             SELECT 
    //                 mg.unit_id,
    //                 CASE
    //                     WHEN u.tingkat_pendidikan = 'SMK' THEN 'SMK'
    //                     WHEN u.tingkat_pendidikan IN ('D3', 'D4/S1', 'S2') THEN 'Perguruan Tinggi'
    //                     ELSE u.tingkat_pendidikan
    //                 END AS tingkat_pendidikan_mapped,
    //                 COUNT(*) AS jumlah
    //             FROM magang mg
    //             JOIN users u ON mg.user_id = u.id
    //             WHERE mg.status_akhir IN ('proses', 'magang')
    //             GROUP BY mg.unit_id, tingkat_pendidikan_mapped
    //         ) AS jumlah_diterima 
    //             ON jumlah_diterima.unit_id = ku.unit_id 
    //             AND jumlah_diterima.tingkat_pendidikan_mapped = ku.tingkat_pendidikan

    //         -- Subquery untuk jumlah pendaftar (status pendaftaran)
    //         LEFT JOIN (
    //             SELECT 
    //                 mg.unit_id,
    //                 CASE
    //                     WHEN u.tingkat_pendidikan = 'SMK' THEN 'SMK'
    //                     WHEN u.tingkat_pendidikan IN ('D3', 'D4/S1', 'S2') THEN 'Perguruan Tinggi'
    //                     ELSE u.tingkat_pendidikan
    //                 END AS tingkat_pendidikan_mapped,
    //                 COUNT(*) AS jumlah
    //             FROM magang mg
    //             JOIN users u ON mg.user_id = u.id
    //             WHERE mg.status_akhir = 'pendaftaran'
    //             GROUP BY mg.unit_id, tingkat_pendidikan_mapped
    //         ) AS jumlah_pendaftar 
    //             ON jumlah_pendaftar.unit_id = ku.unit_id 
    //             AND jumlah_pendaftar.tingkat_pendidikan_mapped = ku.tingkat_pendidikan

    //         ORDER BY uk.unit_kerja, ku.tingkat_pendidikan;


    //     ");
    //     return $builder->getResult();
    // }

    public function getSisaKuota()
    {
        // Hitung tanggal cutoff otomatis (awal bulan dua bulan ke depan)
        $cutoffDate = (new \DateTime())
            ->modify('+2 months')
            ->modify('first day of this month')
            ->format('Y-m-d');

        $sql = "
            SELECT 
                ku.kuota_unit_id,
                ku.unit_id,
                uk.unit_kerja,
                ku.tingkat_pendidikan,
                ku.kuota,

                -- Jumlah diterima (yang belum selesai sebelum cutoff)
                IFNULL(jumlah_diterima.jumlah, 0) AS jumlah_diterima_atau_magang,

                -- Jumlah pendaftar saat ini
                IFNULL(jumlah_pendaftar.jumlah, 0) AS jumlah_pendaftar,

                -- Sisa kuota
                (ku.kuota - IFNULL(jumlah_diterima.jumlah, 0)) AS sisa_kuota

            FROM 
                kuota_unit ku
            JOIN 
                unit_kerja uk ON ku.unit_id = uk.unit_id

            -- Subquery: peserta aktif sampai cutoff
            LEFT JOIN (
                SELECT 
                    mg.unit_id,
                    CASE
                        WHEN u.tingkat_pendidikan = 'SMK' THEN 'SMK'
                        WHEN u.tingkat_pendidikan IN ('D3', 'D4/S1', 'S2') THEN 'Perguruan Tinggi'
                        ELSE u.tingkat_pendidikan
                    END AS tingkat_pendidikan_mapped,
                    COUNT(*) AS jumlah
                FROM magang mg
                JOIN users u ON mg.user_id = u.id
                WHERE 
                    mg.status_akhir IN ('proses', 'magang')
                    AND mg.tanggal_selesai >= ?
                GROUP BY mg.unit_id, tingkat_pendidikan_mapped
            ) AS jumlah_diterima 
                ON jumlah_diterima.unit_id = ku.unit_id 
                AND jumlah_diterima.tingkat_pendidikan_mapped = ku.tingkat_pendidikan

            -- Subquery: pendaftar sekarang
            LEFT JOIN (
                SELECT 
                    mg.unit_id,
                    CASE
                        WHEN u.tingkat_pendidikan = 'SMK' THEN 'SMK'
                        WHEN u.tingkat_pendidikan IN ('D3', 'D4/S1', 'S2') THEN 'Perguruan Tinggi'
                        ELSE u.tingkat_pendidikan
                    END AS tingkat_pendidikan_mapped,
                    COUNT(*) AS jumlah
                FROM magang mg
                JOIN users u ON mg.user_id = u.id
                WHERE mg.status_akhir = 'pendaftaran'
                GROUP BY mg.unit_id, tingkat_pendidikan_mapped
            ) AS jumlah_pendaftar 
                ON jumlah_pendaftar.unit_id = ku.unit_id 
                AND jumlah_pendaftar.tingkat_pendidikan_mapped = ku.tingkat_pendidikan

            ORDER BY uk.unit_kerja, ku.tingkat_pendidikan;
        ";

        // Jalankan query dengan binding cutoffDate
        $builder = $this->db->query($sql, [$cutoffDate]);
        return $builder->getResult(); // Hanya hasil kuota, seperti semula
    }

    public function getCutoffDate()
    {
        return (new \DateTime())
            ->modify('+2 months')
            ->modify('first day of this month')
            ->format('Y-m-d');
    }



    // public function getSisaKuotaPerUnit($unitId, $pendidikan)
    // {
    //     foreach ($this->getSisaKuota() as $k) {
    //         if ($k->unit_id == $unitId && strtolower($k->tingkat_pendidikan) == strtolower($pendidikan)) {
    //             return $k->sisa_kuota;
    //         }
    //     }
    //     return 0;
    // }
}
