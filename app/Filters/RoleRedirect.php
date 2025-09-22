<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleRedirect implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (logged_in()) {

            if (in_groups('admin')) {
                return redirect()->to('/admin/manage-pendaftaran');
            } elseif (in_groups('peserta')) {
                return redirect()->to('/');
            }
        }

        // Jika belum login, tetap ke halaman Home::index
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
