<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class Upload extends BaseController
{
    protected $pendaftaranModel;
    protected $userModel;
    protected $instansiModel;

    public function __construct()
    {
        // Inisialisasi model
        $this->userModel = new UserModel();

    }

    // public function cv($id)
    // {
    //     $file = $this->request->getFile('cv');

    //     if ($file && $file->isValid() && !$file->hasMoved()) {
    //         $newName = $file->getRandomName();
    //         $file->move(ROOTPATH . 'public/uploads/cv/', $newName);

    //         // Simpan nama file ke database
    //         $this->userModel->update($id, ['cv' => $newName]);

    //         return $this->response->setJSON([
    //             'success' => true,
    //             'message' => 'CV berhasil diupload.'
    //         ]);
    //     }

    //     return $this->response->setJSON([
    //         'success' => false,
    //         'message' => 'File tidak valid.'
    //     ]);
    // }

    // public function cv($id, $jenisBerkas = 'cv')
    // {
    //     $file = $this->request->getFile('cv');

    //     if ($file && $file->isValid() && !$file->hasMoved()) {
    //         // Ambil nama user dari database
    //         $user = $this->userModel->find($id);
    //         $namaUser = strtolower(str_replace(' ', '_', $user->fullname ?? 'user'));

    //         // Ambil ekstensi file
    //         $ext = $file->getExtension();

    //         // Generate nama file baru
    //         $random = rand(1000, 9999);
    //         $newName = "{$namaUser}-{$jenisBerkas}-{$random}.{$ext}";

    //         // Tentukan folder tujuan
    //         $targetPath = ROOTPATH . "public/uploads/{$jenisBerkas}/";
    //         if (!is_dir($targetPath)) {
    //             mkdir($targetPath, 0755, true); // Buat folder jika belum ada
    //         }

    //         // Pindahkan file
    //         $file->move($targetPath, $newName);

    //         // Simpan nama file ke kolom yang sesuai
    //         $this->userModel->update($id, [$jenisBerkas => $newName]);

    //         return $this->response->setJSON([
    //             'success' => true,
    //             'message' => ucfirst($jenisBerkas) . ' berhasil diupload.',
    //             'filename' => $newName
    //         ]);
    //     }

    //     return $this->response->setJSON([
    //         'success' => false,
    //         'message' => 'File tidak valid.'
    //     ]);
    // }

    public function cv($id)
    {
        // Ambil semua file yang dikirim
        $allFiles = $this->request->getFiles();

        // Ambil nama field file pertama yang valid (misalnya 'cv', 'ktp', 'proposal', dll)
        foreach ($allFiles as $fieldName => $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $jenisBerkas = $fieldName;

                // Ambil data user
                $user = $this->userModel->find($id);
                $namaBersih = preg_replace('/[^a-zA-Z0-9]/', '', $user->fullname ?? 'user'); // buang semua simbol kecuali huruf & angka
                $namaUser = strtolower($namaBersih);

                // Buat nama file baru
                $ext = $file->getExtension();
                $random = rand(1000, 9999);
                $newName = "{$namaUser}-{$jenisBerkas}-{$random}.{$ext}";

                // Folder tujuan
                $targetPath = ROOTPATH . "public/uploads/{$jenisBerkas}/";
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }

                // Pindahkan file
                $file->move($targetPath, $newName);

                // Simpan ke kolom sesuai nama input
                $this->userModel->update($id, [$jenisBerkas => $newName]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => ucfirst($jenisBerkas) . ' berhasil diupload.',
                    'filename' => $newName
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Tidak ada file yang valid untuk diupload.'
        ]);
    }


    public function deletecv($id)
    {
        $user = $this->userModel->find($id);

        if (!$user || empty($user->cv)) {
            return redirect()->back()->with('error', 'CV tidak ditemukan.');
        }

        $cvPath = ROOTPATH . 'public/uploads/cv/' . $user->cv;

        // Hapus file dari server jika ada
        if (file_exists($cvPath)) {
            unlink($cvPath);
        }

        // Kosongkan kolom cv di database
        $this->userModel->update($id, ['cv' => null]);

        return redirect()->back()->with('success', 'CV berhasil dihapus.');
    }

    public function proposal($id)
    {
        // Ambil file dengan field name 'proposal'
        $file = $this->request->getFile('proposal');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $jenisBerkas = 'proposal';

            // Ambil data user
            $user = $this->userModel->find($id);
            $namaBersih = preg_replace('/[^a-zA-Z0-9]/', '', $user->fullname ?? 'user'); // hanya huruf & angka
            $namaUser = strtolower($namaBersih);

            // Buat nama file baru
            $ext = $file->getExtension();
            $random = rand(1000, 9999);
            $newName = "{$namaUser}-{$jenisBerkas}-{$random}.{$ext}";

            // Folder tujuan
            $targetPath = ROOTPATH . "public/uploads/{$jenisBerkas}/";
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }

            // Pindahkan file
            $file->move($targetPath, $newName);

            // Simpan ke kolom 'proposal'
            $this->userModel->update($id, [$jenisBerkas => $newName]);

            return $this->response->setJSON([
                'success' => true,
                'message' => ucfirst($jenisBerkas) . ' berhasil diupload.',
                'filename' => $newName
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'File tidak valid atau tidak ditemukan.'
        ]);
    }

    public function deleteproposal($id)
    {
        $user = $this->userModel->find($id);

        if (!$user || empty($user->proposal)) {
            return redirect()->back()->with('error', 'Proposal tidak ditemukan.');
        }

        $proposalPath = ROOTPATH . 'public/uploads/proposal/' . $user->proposal;

        // Hapus file dari server jika ada
        if (file_exists($proposalPath)) {
            unlink($proposalPath);
        }

        // Kosongkan kolom cv di database
        $this->userModel->update($id, ['proposal' => null]);

        return redirect()->back()->with('success', 'Proposal berhasil dihapus.');
    }

    public function suratPermohonan($id)
    {
        $file = $this->request->getFile('file_surat');
        $noSurat = $this->request->getPost('no_surat');
        $tanggalSurat = $this->request->getPost('tanggal_surat');
        $pimpinan = $this->request->getPost('pimpinan');
        $jabatan = $this->request->getPost('jabatan');
        $email_instansi = $this->request->getPost('email_instansi');
        $judul_penelitian = $this->request->getPost('judul_penelitian');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $jenisBerkas = 'surat';
             // Ambil data user
            $user = $this->userModel->find($id);
            $namaBersih = preg_replace('/[^a-zA-Z0-9]/', '', $user->fullname ?? 'user'); // hanya huruf & angka
            $namaUser = strtolower($namaBersih);

            // Buat nama file baru
            $ext = $file->getExtension();
            $random = rand(1000, 9999);
            $newName = "{$namaUser}-{$jenisBerkas}-{$random}.{$ext}";

            // Folder tujuan
            $targetPath = ROOTPATH . "public/uploads/{$jenisBerkas}/";
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }


            // Simpan ke DB
            $this->userModel->update($id, [
                'surat_permohonan' => $newName,
                'tanggal_surat'    => $tanggalSurat,
                'no_surat'         => $noSurat,
                'nama_pimpinan'    => $pimpinan,
                'jabatan'          => $jabatan,
                'email_instansi'  => $email_instansi,
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Surat Permohonan berhasil diupload.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'File tidak valid.'
        ]);
    }

    public function deleteSuratPermohonan($id)
    {
        $user = $this->userModel->find($id);

        if (!$user || empty($user->surat_permohonan)) {
            return redirect()->back()->with('error', 'Surat permohonan tidak ditemukan.');
        }

        // Hapus file dari server
        $filePath = ROOTPATH . 'public/uploads/surat_permohonan/' . $user->surat_permohonan;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Kosongkan kolom-kolom terkait di database
        $this->userModel->update($id, [
            'surat_permohonan' => null,
            'tanggal_surat'    => null,
            'no_surat'         => null,
            'nama_pimpinan'    => null,
        ]);

        return redirect()->back()->with('success', 'Surat permohonan berhasil dihapus.');
    }

    public function ktp_kk($id)
    {
        $file = $this->request->getFile('ktp');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $jenisBerkas = 'ktp-kk';

            // Validasi ekstensi file
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $extension = $file->getClientExtension();

            if (!in_array(strtolower($extension), $allowedExtensions)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format file tidak didukung. Hanya PDF, JPG, JPEG, dan PNG.'
                ]);
            }

            // Validasi ukuran maksimal (2MB)
            if ($file->getSize() > 2 * 1024 * 1024) { // 2MB
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar. Maksimal 2MB.'
                ]);
            }

            // Ambil data user
            $user = $this->userModel->find($id);
            $namaBersih = preg_replace('/[^a-zA-Z0-9]/', '', $user->fullname ?? 'user'); // hanya huruf & angka
            $namaUser = strtolower($namaBersih);

            // Buat nama file baru
            $ext = $file->getExtension();
            $random = rand(1000, 9999);
            $newName = "{$namaUser}-{$jenisBerkas}-{$random}.{$ext}";

            // Folder tujuan
            $targetPath = ROOTPATH . "public/uploads/{$jenisBerkas}/";
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }

            // Pindahkan file
            $file->move($targetPath, $newName);

            // Simpan nama file ke database
            $this->userModel->update($id, ['ktp_kk' => $newName]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'KTP/KK berhasil diupload.'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'File tidak valid.'
        ]);
    }

    public function deletektp($id)
    {
        $user = $this->userModel->find($id);

        if (!$user || empty($user->ktp_kk)) {
            return redirect()->back()->with('error', 'KTP/KK tidak ditemukan.');
        }

        $ktpPath = ROOTPATH . 'public/uploads/ktp-kk/' . $user->ktp_kk;

        // Hapus file dari server jika ada
        if (file_exists($ktpPath)) {
            unlink($ktpPath);
        }

        // Kosongkan kolom cv di database
        $this->userModel->update($id, ['ktp_kk' => null]);

        return redirect()->back()->with('success', 'KTP/KK berhasil dihapus.');
    }

    public function bpjsKes($id)
    {
        $file = $this->request->getFile('bpjs_kes');

        if ($file && $file->isValid() && !$file->hasMoved()) {

            // Validasi ekstensi file
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $extension = $file->getClientExtension();

            if (!in_array(strtolower($extension), $allowedExtensions)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format file tidak didukung. Hanya PDF, JPG, JPEG, dan PNG.'
                ]);
            }

            // Validasi ukuran maksimal (2MB)
            if ($file->getSize() > 2 * 1024 * 1024) { // 2MB
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar. Maksimal 2MB.'
                ]);
            }

            $jenisBerkas = 'bpjs-kes';
             // Ambil data user
            $user = $this->userModel->find($id);
            $namaBersih = preg_replace('/[^a-zA-Z0-9]/', '', $user->fullname ?? 'user'); // hanya huruf & angka
            $namaUser = strtolower($namaBersih);

            // Buat nama file baru
            $ext = $file->getExtension();
            $random = rand(1000, 9999);
            $newName = "{$namaUser}-{$jenisBerkas}-{$random}.{$ext}";

            // Folder tujuan
            $targetPath = ROOTPATH . "public/uploads/{$jenisBerkas}/";
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }

            // Simpan nama file ke database
            $this->userModel->update($id, ['bpjs_kes' => $newName]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'BPJS Kesehatan berhasil diupload.'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'File tidak valid.'
        ]);
    }

    public function deleteBPJSKes($id)
    {
        $user = $this->userModel->find($id);

        if (!$user || empty($user->ktp_kk)) {
            return redirect()->back()->with('error', 'BPJS Kesehatan tidak ditemukan.');
        }

        $ktpPath = ROOTPATH . 'public/uploads/bpjs-kes/' . $user->bpjs_kes;

        // Hapus file dari server jika ada
        if (file_exists($ktpPath)) {
            unlink($ktpPath);
        }

        // Kosongkan kolom cv di database
        $this->userModel->update($id, ['bpjs_kes' => null]);

        return redirect()->back()->with('success', 'BPJS Kesehatan berhasil dihapus.');
    }

    public function bpjsTK($id)
    {
        $file = $this->request->getFile('bpjs_tk');

        if ($file && $file->isValid() && !$file->hasMoved()) {

            // Validasi ekstensi file
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $extension = $file->getClientExtension();

            if (!in_array(strtolower($extension), $allowedExtensions)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format file tidak didukung. Hanya PDF, JPG, JPEG, dan PNG.'
                ]);
            }

            // Validasi ukuran maksimal (2MB)
            if ($file->getSize() > 2 * 1024 * 1024) { // 2MB
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar. Maksimal 2MB.'
                ]);
            }

            $jenisBerkas = 'bpjs-tk';
             // Ambil data user
            $user = $this->userModel->find($id);
            $namaBersih = preg_replace('/[^a-zA-Z0-9]/', '', $user->fullname ?? 'user'); // hanya huruf & angka
            $namaUser = strtolower($namaBersih);

            // Buat nama file baru
            $ext = $file->getExtension();
            $random = rand(1000, 9999);
            $newName = "{$namaUser}-{$jenisBerkas}-{$random}.{$ext}";

            // Folder tujuan
            $targetPath = ROOTPATH . "public/uploads/{$jenisBerkas}/";
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
            // Simpan nama file ke database
            $this->userModel->update($id, ['bpjs_tk' => $newName]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'BPJS Ketenagakerjaan berhasil diupload.'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'File tidak valid.'
        ]);
    }

    public function deleteBPJSTK($id)
    {
        $user = $this->userModel->find($id);

        if (!$user || empty($user->bpjs_tk)) {
            return redirect()->back()->with('error', 'BPJS Ketenagakerjaantidak ditemukan.');
        }

        $ktpPath = ROOTPATH . 'public/uploads/bpjs-tk/' . $user->bpjs_tk;

        // Hapus file dari server jika ada
        if (file_exists($ktpPath)) {
            unlink($ktpPath);
        }

        // Kosongkan kolom cv di database
        $this->userModel->update($id, ['bpjs_tk' => null]);

        return redirect()->back()->with('success', 'BPJS Ketenagakerjaan berhasil dihapus.');
    }

    public function buktibpjsTK($id)
    {
        $file = $this->request->getFile('buktibpjs_tk');

        if ($file && $file->isValid() && !$file->hasMoved()) {

            // Validasi ekstensi file
            $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
            $extension = $file->getClientExtension();

            if (!in_array(strtolower($extension), $allowedExtensions)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format file tidak didukung. Hanya PDF, JPG, JPEG, dan PNG.'
                ]);
            }

            // Validasi ukuran maksimal (2MB)
            if ($file->getSize() > 2 * 1024 * 1024) { // 2MB
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar. Maksimal 2MB.'
                ]);
            }

            $jenisBerkas = 'buktibpjs-tk';
             // Ambil data user
            $user = $this->userModel->find($id);
            $namaBersih = preg_replace('/[^a-zA-Z0-9]/', '', $user->fullname ?? 'user'); // hanya huruf & angka
            $namaUser = strtolower($namaBersih);

            // Buat nama file baru
            $ext = $file->getExtension();
            $random = rand(1000, 9999);
            $newName = "{$namaUser}-{$jenisBerkas}-{$random}.{$ext}";

            // Folder tujuan
            $targetPath = ROOTPATH . "public/uploads/{$jenisBerkas}/";
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
            // Simpan nama file ke database
            $this->userModel->update($id, ['buktibpjs_tk' => $newName]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'BUkti Pembayaran BPJS Ketenagakerjaan berhasil diupload.'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'File tidak valid.'
        ]);
    }

    public function deletebuktiBPJSTK($id)
    {
        $user = $this->userModel->find($id);

        if (!$user || empty($user->buktibpjs_tk)) {
            return redirect()->back()->with('error', 'Bukti Pembayaran BPJS Ketenagakerjaan tidak ditemukan.');
        }

        $ktpPath = ROOTPATH . 'public/uploads/buktibpjs-tk/' . $user->buktibpjs_tk;

        // Hapus file dari server jika ada
        if (file_exists($ktpPath)) {
            unlink($ktpPath);
        }

        // Kosongkan kolom cv di database
        $this->userModel->update($id, ['buktibpjs_tk' => null]);

        return redirect()->back()->with('success', 'Bukti Pembayaran BPJS Ketenagakerjaan berhasil dihapus.');
    }

    




}
