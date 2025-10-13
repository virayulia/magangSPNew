<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\FeedbackModel;
use App\Models\UserModel;

class FeedbackController extends BaseController
{
    protected $feedbackModel;
    protected $userModel;

    public function __construct()
    {
        $this->feedbackModel = new FeedbackModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $feedback = $this->feedbackModel->join('magang', 'magang.magang_id = feedback.magang_id')
                                        ->join('users', 'users.id = magang.user_id')
                                        ->join('unit_kerja','unit_kerja.unit_id = magang.unit_id')
                                        ->orderBy('feedback_id')->findAll();

        return view('admin/kelola_feedback', ['feedback' => $feedback]);
    }
}
