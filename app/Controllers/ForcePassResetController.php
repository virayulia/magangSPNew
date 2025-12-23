<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Myth\Auth\Password;

class ForcePassResetController extends BaseController
{
     public function index()
    {
        return view('auth/must_change_password');
    }

    public function update()
    {
        $auth = service('authentication');
        $user = $auth->user();

        $rules = [
            'password'     => 'required|min_length[8]',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $newPassword = $this->request->getPost('password');

        /**
         * 🔒 CEK: password baru tidak boleh sama dengan password lama
         */
        $userModel = model('UserModel');

        $userDB = $userModel->find($user->id);

        if (password_verify($newPassword, $userDB->password_hash)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Password baru tidak boleh sama dengan password sebelumnya.');
        }

        // dd($newPassword, $user->password_hash);


        $userModel->update($user->id, [
            'password_hash'        => Password::hash($newPassword),
            'must_change_password' => 0,
        ]);

        if (in_array('admin', $user->getRoles())) {
            $redirectURL = site_url('/admin/dashboard');
        } elseif (in_array('pembimbing', $user->getRoles())) {
            $redirectURL = site_url('/pembimbing/penilaian');
        } else {
            $redirectURL = session('redirect_url') ?? site_url('/');
        }

        return redirect()
            ->to($redirectURL)
            ->with('success', 'Password berhasil diperbarui. Silakan lanjutkan.');
    }

}
