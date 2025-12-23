<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Config\Services;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;
    protected $submenuType;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['auth', 'format','upload'];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');

        $userId = user()->id ?? null;
        $db = \Config\Database::connect();

        // cek punya data magang atau penelitian
        $hasMagang      = $db->table('magang')->where('user_id', $userId)->countAllResults() > 0;
        $hasPenelitian  = $db->table('penelitian')->where('user_id', $userId)->countAllResults() > 0;

        // ambil pendaftaran terakhir keduanya
        $lastMagang = $db->table('magang')
            ->where('user_id', $userId)
            ->orderBy('magang_id', 'DESC')
            ->get()->getRowArray();

        $lastPenelitian = $db->table('penelitian')
            ->where('user_id', $userId)
            ->orderBy('penelitian_id', 'DESC')
            ->get()->getRowArray();

        // tentukan submenu aktif (berbasis mana yang masih berjalan)
        if ($hasMagang && $hasPenelitian) {
            // Prioritas submenu yang masih dalam proses
            if (!empty($lastMagang) && in_array($lastMagang['status_akhir'], ['pendaftaran','magang','proses'])) {
                $submenuType = 'magang';
            } elseif (!empty($lastPenelitian) && in_array($lastPenelitian['status_akhir'], ['pendaftaran','penelitian','proses'])) {
                $submenuType = 'penelitian';
            } else {
                // kalau dua2nya selesai pilih yang terbaru tanggalnya
                $submenuType = ($lastMagang['magang_id'] > $lastPenelitian['penelitian_id']) ? 'magang' : 'penelitian';
            }
        } elseif ($hasMagang) {
            $submenuType = 'magang';
        } elseif ($hasPenelitian) {
            $submenuType = 'penelitian';
        } else {
            $submenuType = 'magang'; // default
        }

        Services::renderer()->setVar('submenuType', $submenuType);
        Services::renderer()->setVar('hasMagang', $hasMagang);
        Services::renderer()->setVar('hasPenelitian', $hasPenelitian);


    }
}
