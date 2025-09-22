<?php

namespace App\Auth\Activators;

use App\Entities\User;
use Myth\Auth\Authentication\Activators\EmailActivator as BaseEmailActivator;

class EmailActivator extends BaseEmailActivator
{
    protected function sendActivationEmail(User $user): bool
    {
        $view = $this->config->views['emailActivation'];

        $message = view($view, ['user' => $user]);

        return service('email')
            ->setTo($user->email)
            ->setSubject(lang('Auth.activationSubject'))
            ->setMessage($message)
            ->send();
    }
}
