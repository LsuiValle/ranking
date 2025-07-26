<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RiotAccount;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function login(Request $request)
    {
        $request->validate([
            'game_name' => 'required|string',
            'tag_line' => 'required|string',
        ]);

        $user = \App\Models\RiotAccount::where('game_name', $request->game_name)
            ->where('tag_line', $request->tag_line)
            ->first();

        if ($user) {
            \Auth::login($user);
            return redirect()->route('tasks.index');
        } else {
            return back()->withErrors(['game_name' => 'Invocador o tag incorrecto.']);
        }
    }
}
