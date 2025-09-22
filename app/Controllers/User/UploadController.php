<?php

namespace App\Controllers\User;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;


class UploadController extends BaseController
{
    protected $pendaftaranModel;
    protected $userModel;
    protected $instansiModel; 

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function cv($id)
    {
        $user = $this->userModel->find($id);
        $fullname = $user->fullname ?? 'user';

        $file = $this->request->getFile('cv');

        if ($file && $file->isValid()) {
            $namaFile = uploadBerkasUser($file, $fullname, 'cv');

            if ($namaFile) {
                $this->userModel->update($id, ['cv' => $namaFile]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'CV berhasil diupload.',
                    'filename' => $namaFile
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengupload file.'
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
        $file = $this->request->getFile('proposal');
        $user = $this->userModel->find($id);

        if ($file && $file->isValid()) {
            $filename = uploadBerkasUser($file, $user->fullname ?? 'user', 'proposal');

            if ($filename) {
                $this->userModel->update($id, ['proposal' => $filename]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Proposal berhasil diupload.',
                    'filename' => $filename
                ]);
            }
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
        helper('upload'); // pastikan helper dimuat

        $file             = $this->request->getFile('file_surat');
        $noSurat          = $this->request->getPost('no_surat');
        $tanggalSurat     = $this->request->getPost('tanggal_surat');
        $pimpinan         = $this->request->getPost('pimpinan');
        $jabatan          = $this->request->getPost('jabatan');
        $email_instansi   = $this->request->getPost('email_instansi');
        $judul_penelitian = $this->request->getPost('judul_penelitian');

        // Ambil data user
        $user = $this->userModel->find($id);

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $filename = uploadBerkasUser($file, $user->fullname ?? 'user', 'surat-permohonan');

            if ($filename) {
                $this->userModel->update($id, [
                    'surat_permohonan' => $filename,
                    'tanggal_surat'    => $tanggalSurat,
                    'no_surat'         => $noSurat,
                    'nama_pimpinan'    => $pimpinan,
                    'jabatan'          => $jabatan,
                    'email_instansi'   => $email_instansi,
                ]);

                return $this->response->setJSON([
                    'success'  => true,
                    'message'  => 'Surat Permohonan berhasil diupload.',
                    'filename' => $filename
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'File tidak valid atau gagal upload.'
        ]);
    }


    public function deleteSuratPermohonan($id)
    {
        $user = $this->userModel->find($id);

        if (!$user || empty($user->surat_permohonan)) {
            return redirect()->back()->with('error', 'Surat permohonan tidak ditemukan.');
        }

        // Hapus file dari server
        $filePath = ROOTPATH . 'public/uploads/surat-permohonan/' . $user->surat_permohonan;
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
        $user = $this->userModel->find($id);

        if ($file && $file->isValid()) {
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
            $ext = strtolower($file->getClientExtension());

            if (!in_array($ext, $allowed)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format file tidak didukung. Hanya PDF, JPG, JPEG, dan PNG.'
                ]);
            }

            if ($file->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar. Maksimal 2MB.'
                ]);
            }

            $filename = uploadBerkasUser($file, $user->fullname ?? 'user', 'ktp-kk');

            if ($filename) {
                $this->userModel->update($id, ['ktp_kk' => $filename]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'KTP/KK berhasil diupload.'
                ]);
            }
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
        $user = $this->userModel->find($id);

        if ($file && $file->isValid()) {
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
            $ext = strtolower($file->getClientExtension());

            if (!in_array($ext, $allowed)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format file tidak didukung. Hanya PDF, JPG, JPEG, dan PNG.'
                ]);
            }

            if ($file->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar. Maksimal 2MB.'
                ]);
            }

            $filename = uploadBerkasUser($file, $user->fullname ?? 'user', 'bpjs-kes');

            if ($filename) {
                $this->userModel->update($id, ['bpjs_kes' => $filename]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'BPJS Kesehatan berhasil diupload.'
                ]);
            }
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
        $user = $this->userModel->find($id);

        if ($file && $file->isValid()) {
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
            $ext = strtolower($file->getClientExtension());

            if (!in_array($ext, $allowed)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format file tidak didukung. Hanya PDF, JPG, JPEG, dan PNG.'
                ]);
            }

            if ($file->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar. Maksimal 2MB.'
                ]);
            }

            $filename = uploadBerkasUser($file, $user->fullname ?? 'user', 'bpjs-tk');

            if ($filename) {
                $this->userModel->update($id, ['bpjs_tk' => $filename]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'BPJS Ketenagakerjaan berhasil diupload.'
                ]);
            }
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
        $user = $this->userModel->find($id);

        if ($file && $file->isValid()) {
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
            $ext = strtolower($file->getClientExtension());

            if (!in_array($ext, $allowed)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Format file tidak didukung. Hanya PDF, JPG, JPEG, dan PNG.'
                ]);
            }

            if ($file->getSize() > 2 * 1024 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file terlalu besar. Maksimal 2MB.'
                ]);
            }

            $filename = uploadBerkasUser($file, $user->fullname ?? 'user', 'buktibpjs-tk');

            if ($filename) {
                $this->userModel->update($id, ['buktibpjs_tk' => $filename]);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Bukti Pembayaran BPJS Ketenagakerjaan berhasil diupload.'
                ]);
            }
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
