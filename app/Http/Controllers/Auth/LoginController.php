<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $roles = $user->role_id()->pluck('role_name')->toArray();
        session()->put('allowed_roles', $roles);
    }
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback()
    {
        $socialiteUser = Socialite::driver('google')->user();

        // Check if the user exists by email
        $user = User::where('email', $socialiteUser->email)->first();

        if (!$user) {
            // User doesn't exist, create a new user
            return redirect('/login')->with('failed', "You're Account is not Registered!, Make sure you log into a correct account");
        } else {
            // User exists
            // You can update any necessary information here if needed
            // For example, update the user's name from the socialite response
            $user->name = $socialiteUser->name;
            // Update other necessary fields

            $user->save();
        }

        Auth::login($user, true);

        // Redirect to your desired location after successful login
        return redirect()->intended($this->redirectTo);
    }
}
