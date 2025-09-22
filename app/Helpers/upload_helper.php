<?php

use CodeIgniter\HTTP\Files\UploadedFile;

if (!function_exists('uploadBerkasUser')) {
    /**
     * Upload file user ke folder berdasarkan jenis berkas.
     *
     * @param UploadedFile $file
     * @param string $fullname - nama lengkap user
     * @param string $jenisBerkas - contoh: 'cv', 'proposal'
     * @return string|null - nama file baru jika sukses, null jika gagal
     */
    function uploadBerkasUser(UploadedFile $file, string $fullname, string $jenisBerkas): ?string
    {
        if ($file->isValid() && !$file->hasMoved()) {
            $namaUser = url_title($fullname, '-', true);
            $typeSlug = url_title($jenisBerkas, '-', true);
            $ext = $file->getExtension();
            $random = random_int(1000, 9999);
            $newName = "{$namaUser}-{$typeSlug}-{$random}.{$ext}";

            $targetPath = ROOTPATH . "public/uploads/{$typeSlug}/";
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }

            $file->move($targetPath, $newName);
            return $newName;
        }

        return null;
    }
}
