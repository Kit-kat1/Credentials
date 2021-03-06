<?php

/*
 * This file is part of Laravel Credentials.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Credentials\Http\Controllers;

use GrahamCampbell\Binput\Facades\Binput;
use GrahamCampbell\Credentials\Facades\Credentials;
use GrahamCampbell\Credentials\Facades\UserRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use GrahamCampbell\Credentials\Models\User;

/**
 * This is the reset controller class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ResetController extends AbstractController
{
    /**
     * Display the password reset form.
     *
     * @return \Illuminate\View\View
     */
    public function getReset()
    {
        return View::make('credentials::account.reset');
    }

    /**
     * Queue the sending of the password reset email.
     *
     * @return \Illuminate\Http\Response
     */
    public function postReset()
    {
        $email = Binput::get('email');

        $rules = [
            'email' => 'required|email|exists:users',
        ];

        $val = UserRepository::validate(['email' => $email], $rules, true);

        if ($val->fails()) {
            return Redirect::route('account.reset')->withInput()->withErrors($val->errors());
        }

        $input = [
            'password' => Str::random(),
        ];

        try {
            $user = User::where('email', '=', $email)->first();
            $password = $input['password'];

            $input['password'] = $user->hash($password);

            $user->update($input);

            $mail = [
                'password' => $password,
                'email'    => $user->email,
                'subject'  => Config::get('app.name').' - New Password Information',
            ];

            Mail::queue('credentials::emails.password', $mail, function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['subject']);
            });

            return Redirect::route('account.reset')
                ->with('success', 'Check your email for password reset information.');
        } catch (\Exception $e) {
            return Redirect::route('account.reset')
                ->with('error', 'That user does not exist.');
        }
    }
}