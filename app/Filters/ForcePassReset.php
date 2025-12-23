<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ForcePassReset implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = service('authentication');

        if (! $auth->check()) {
            return;
        }

        $user = $auth->user();

        // Hanya admin & pembimbing
        if (! array_intersect(['admin', 'pembimbing', 'she'], $user->getRoles())) {
            return;
        }

        if ($user->must_change_password == 1) {
            $path = service('uri')->getPath();

            // Halaman yang boleh diakses
            $allowed = [
                'must-change-password',
                'logout',
            ];

            foreach ($allowed as $allow) {
                if (str_contains($path, $allow)) {
                    return;
                }
            }

            return redirect()
                ->to(site_url('must-change-password'))
                ->with('warning', 'Silakan ganti password Anda terlebih dahulu.');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
