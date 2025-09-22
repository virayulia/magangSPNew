<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class JurusanUnitController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $units = $db->table('unit_kerja')->get()->getResultArray();
        $kuota_units = $db->table('kuota_unit')->get()->getResultArray();
        $jurusans = $db->table('jurusan')->get()->getResultArray();

        // Ambil data jurusan_unit yang sudah join
        $data = $db->table('jurusan_unit ju')
            ->select('ju.jurusan_unit_id, u.unit_kerja, ku.kuota_unit_id, ku.tingkat_pendidikan, ku.kuota, j.nama_jurusan')
            ->join('kuota_unit ku', 'ju.kuota_unit_id = ku.kuota_unit_id')
            ->join('unit_kerja u', 'ku.unit_id = u.unit_id')
            ->join('jurusan j', 'ju.jurusan_id = j.jurusan_id')
            ->orderBy('u.unit_kerja')
            ->orderBy('ku.tingkat_pendidikan')
            ->orderBy('j.nama_jurusan')
            ->get()
            ->getResultArray();

        // Kelompokkan data
        $grouped = [];
        foreach ($data as $row) {
            $key = $row['kuota_unit_id']; // Group berdasarkan kuota_unit_id
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'kuota_unit_id'    => $row['kuota_unit_id'],
                    'unit_kerja'       => $row['unit_kerja'],
                    'tingkat_pendidikan' => $row['tingkat_pendidikan'],
                    'kuota'            => $row['kuota'],
                    'jurusans'         => [],
                ];
            }
            // $grouped[$key]['jurusans'][] = $row['nama_jurusan'];
            $grouped[$key]['jurusans'][] = [
                                            'jurusan_unit_id' => $row['jurusan_unit_id'],
                                            'nama_jurusan' => $row['nama_jurusan']
                                        ];
        }

        $data = [
            'title' => 'Kelola Jurusan Unit',
            'units' => $units,
            'kuota_units' => $kuota_units,
            'jurusans' => $jurusans,
            'grouped_jurusan_unit' => $grouped,
        ];

        return view('admin/kelola_jurusan_unit', $data);
    }

    public function save()
    {
        $db = \Config\Database::connect();

        $kuota_unit_id = $this->request->getPost('kuota_unit_id');
        $jurusan_ids = $this->request->getPost('jurusan_id'); // ini array karena name="jurusan_id[]"

        if (!$kuota_unit_id || !is_array($jurusan_ids)) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        $inserted = 0;

        foreach ($jurusan_ids as $jurusan_id) {
            // Cek apakah kombinasi ini sudah ada di database
            $exists = $db->table('jurusan_unit')
                ->where('kuota_unit_id', $kuota_unit_id)
                ->where('jurusan_id', $jurusan_id)
                ->countAllResults();

            if ($exists == 0) {
                $db->table('jurusan_unit')->insert([
                    'kuota_unit_id' => $kuota_unit_id,
                    'jurusan_id'    => $jurusan_id
                ]);
                $inserted++;
            }
        }

        if ($inserted > 0) {
            return redirect()->back()->with('success', "$inserted jurusan unit berhasil ditambahkan.");
        } else {
            return redirect()->back()->with('error', 'Semua jurusan yang dipilih sudah pernah ditambahkan.');
        }
    }


    public function addJurusan()
    {
        $db = \Config\Database::connect();

        $kuota_unit_id = $this->request->getPost('kuota_unit_id');
        $jurusan_ids = $this->request->getPost('jurusan_id'); // ini array karena name="jurusan_id[]"

        if (!$kuota_unit_id || !is_array($jurusan_ids)) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        $inserted = 0;

        foreach ($jurusan_ids as $jurusan_id) {
            // Cek apakah kombinasi ini sudah ada di database
            $exists = $db->table('jurusan_unit')
                ->where('kuota_unit_id', $kuota_unit_id)
                ->where('jurusan_id', $jurusan_id)
                ->countAllResults();

            if ($exists == 0) {
                $db->table('jurusan_unit')->insert([
                    'kuota_unit_id' => $kuota_unit_id,
                    'jurusan_id'    => $jurusan_id
                ]);
                $inserted++;
            }
        }

        if ($inserted > 0) {
            return redirect()->back()->with('success', "$inserted jurusan unit berhasil ditambahkan.");
        } else {
            return redirect()->back()->with('error', 'Semua jurusan yang dipilih sudah pernah ditambahkan.');
        }
    }

    public function deleteJurusan($id)
    {
        $db = \Config\Database::connect();

        $db->table('jurusan_unit')
            ->where('jurusan_unit_id', $id)
            ->delete();

        return redirect()->back()->with('success', 'Jurusan berhasil dihapus.');
    }
}
