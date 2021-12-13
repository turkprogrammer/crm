<?php

namespace App\Modules\Pub\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller {

    use \Illuminate\Foundation\Auth\AuthenticatesUsers;

    protected $redirectedTo = '/admin/dashboard';

    public function __construct() {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm() {
        $title = __('Login');

        return view('Pub::Auth.login');//простсранство имен/имя модуля/вид
    }

}
