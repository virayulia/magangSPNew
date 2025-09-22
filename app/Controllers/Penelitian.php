<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Penelitian extends BaseController
{
    public function daftar()
    {
        if (!logged_in()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'judul'            => 'required|min_length[5]',
            'tanggal_mulai'    => 'required|valid_date',
            'deskripsi'        => 'permit_empty|string',
            'dosen_pembimbing' => 'permit_empty|string',
            'bidang'           => 'permit_empty|string',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $validation->listErrors());
        }

        $data = [
            'user_id'         => user_id(),
            'judul_penelitian'=> $this->request->getPost('judul'),
            'tanggal_masuk'   => $this->request->getPost('tanggal_mulai'),
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'dosen_pembimbing'=> $this->request->getPost('dosen_pembimbing'),
            'bidang'          => $this->request->getPost('bidang'),
            'tanggal_daftar'  => date('Y-m-d H:i:s'),
            'status_akhir'          => 'pendaftaran',
        ];

        $db = \Config\Database::connect();
        $db->table('penelitian')->insert($data);

        return redirect()->to('/')->with('success', 'Pendaftaran penelitian berhasil dikirim. Silakan menunggu konfirmasi.');
    }

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
