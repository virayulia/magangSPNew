<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SertifikatModel;

class SertifikatController extends BaseController
{
    protected $sertifikatModel;

    public function __construct() {
        $this->sertifikatModel = new SertifikatModel();
    }
    
    // public function verify($token)
    // {
    //     $sertifikat = $this->sertifikatModel
    //         ->where('qr_token', $token)
    //         ->first();

    //     if (!$sertifikat || empty($sertifikat['file_sertifikat'])) {
    //         return view('not_found');
    //     }

    //     return redirect()->to(
    //         base_url('uploads/sertifikat/pdf/' . $sertifikat['file_sertifikat'])
    //     );
    // }

    public function verify($token)
    {
        $sertifikat = $this->sertifikatModel
            ->where('qr_token', $token)
            ->first();

        if (!$sertifikat || empty($sertifikat['file_sertifikat'])) {
            return view('not_found');
        }

        $localIp = '192.168.229.8';

        return $this->response->redirect(
            "http://{$localIp}/magangSPNew/public/uploads/sertifikat/pdf/" 
            . $sertifikat['file_sertifikat']
        );
    }
}
