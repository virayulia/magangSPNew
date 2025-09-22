<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class PembimbingFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        if (!in_groups('pembimbing')) {
            return redirect()->to('/profile')->with('error', 'Anda tidak memiliki akses pembimbing.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak dipakai
    }
}
