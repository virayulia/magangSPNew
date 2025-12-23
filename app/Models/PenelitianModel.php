<?php

namespace App\Models;

use CodeIgniter\Model;

class PenelitianModel extends Model
{
    protected $table            = 'penelitian';
    protected $primaryKey       = 'penelitian_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id','unit_id','judul_penelitian', 'durasi','tanggal_daftar',
                                    'status_seleksi','tanggal_seleksi', 'approve_unit', 'tanggal_approve_unit','cttn_approve_unit',
                                    'deskripsi', 'dosen_pembimbing','bidang', 'rencana_masuk','pembimbing_id', 'absensi','catatan_absensi',
                                    'tgl_upload_absensi', 'tgl_upload_formulir',
                                    'tanggal_masuk', 'tanggal_selesai', 'status_akhir', 'status_konfirmasi','tanggal_konfirmasi',
                                'status_validasi_konfirmasi', 'tanggal_validasi_konfirmasi', 'status_berkas_lengkap','tanggal_berkas_lengkap',
                            'cttn_berkas_lengkap','tanggal_setujui_pernyataan', 'formulir_penelitian','catatan_formulir',
                            'finalisasi','ka_unit_approve','tanggal_approve', 'status_pembimbing','catatan_pembimbing'];

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
}
