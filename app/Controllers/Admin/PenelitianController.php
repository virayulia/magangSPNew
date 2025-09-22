<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PenelitianController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Join ke tabel users untuk ambil nama
        $builder = $db->table('penelitian p')
            ->select('p.*, u.fullname, u.email')
            ->join('users u', 'u.id = p.user_id')
            ->orderBy('p.tanggal_daftar', 'ASC');

        $penelitian = $builder->get()->getResult();

        $data = [
            'title' => 'Kelola Penelitian',
            'penelitian' => $penelitian
        ];

        return view('admin/kelola_penelitian', $data);
    }
}
