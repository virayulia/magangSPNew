<?php

namespace App\Auth\Resetters;

use Myth\Auth\Entities\User;
use Myth\Auth\Authentication\Resetters\EmailResetter as BaseResetter;
use Config\Auth as CustomAuthConfig;

class EmailResetter extends BaseResetter
{
    protected $config;

    public function __construct($config)
    {
        parent::__construct($config);

        $this->config = new CustomAuthConfig();

    }

    protected function sendResetEmail(User $user): bool
    {
        $view = $this->config->views['emailForgot'];
        $message = view($view, ['user' => $user]);

        return service('email')
            ->setTo($user->email)
            ->setSubject('Reset Password Custom')
            ->setMessage($message)
            ->send();
    }
}
