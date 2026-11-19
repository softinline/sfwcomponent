<?php

namespace Softinline\SfwComponent\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Softinline\SfwComponent\SfwComponent;
use Softinline\SfwComponent\SfwProtectedMiddleware;

class AuthController extends Controller
{
    var $_sfwconfig;

    /**
     * Display the login screen.
     *
     * @return mixed
     */
    public function loginGet()
    {
        if (!SfwProtectedMiddleware::isSetupComplete()) {
            return redirect('/sfw/setup');
        }
        if (\Session::get('sfw-user-logged')) {
            return redirect('/sfw');
        }

        $this->_sfwconfig = new \Softinline\SfwComponent\SfwConfig();
        $this->_sfwconfig->load(__DIR__.'/../Defines/Auth/login.json');
        $sfwcomponent = new \Softinline\SfwComponent\SfwComponent(get_class());
        return $sfwcomponent->render($this->_sfwconfig->getConfig());
    }

    /**
     * Handle login form submission.
     *
     * @return mixed
     */
    public function loginPost()
    {
        if (!SfwProtectedMiddleware::isSetupComplete()) {
            return redirect('/sfw/setup');
        }

        $username = (string) request()->get('username', request()->get('email', ''));
        $password = (string) request()->get('password', '');

        $file = SfwProtectedMiddleware::setupFile();
        $hash = null;
        if (is_file($file)) {
            $data = json_decode((string) @file_get_contents($file), true);
            $hash = is_array($data) ? ($data['password_hash'] ?? null) : null;
        }

        if ($username === 'admin' && $hash && Hash::check($password, $hash)) {
            \Session::put('sfw-user-logged', true);
            \Session::put('sfw-user-name', 'admin');
            return redirect('/sfw');
        }

        return redirect('/sfw/auth/login')->with('message_error', 'Usuario o contrase�a incorrectos.');
    }

    /**
     * Log out the current user.
     *
     * @return mixed
     */
    public function logoff()
    {
        \Session::forget('sfw-user-logged');
        \Session::forget('sfw-user-name');

        return redirect('/sfw/auth/login');
    }
}
