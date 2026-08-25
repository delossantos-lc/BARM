<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login page.
     */
    public function showLoginForm(): View
    {
        return view('login');
    }

    /**
     * Process the login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'employeeid' => ['required', 'string'],
            'password'   => ['required', 'string'],
        ], [
            'employeeid.required' => 'Please enter your Employee ID.',
            'password.required'   => 'Please enter your password.',
        ]);

        // Only active users are allowed to log in.
        $credentials['status'] = 'active';

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors([
                    'employeeid' => 'The Employee ID or password is incorrect, or the account is inactive.',
                ])
                ->onlyInput('employeeid');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        /*
         * Allow only recognized access levels.
         */
        $accessLevel = strtolower(trim($user->access_level ?? ''));

        if (!in_array($accessLevel, ['admin', 'staff'], true)) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('error', 'Your account does not have a valid access level.');
        }

        /*
         * Save the logged-in user's information.
         */
        $request->session()->put([
            'session_id'           => $user->id,
            'session_employeeid'   => $user->employeeid,
            'session_name'         => trim(
                ($user->firstname ?? '') . ' ' .
                ($user->lastname ?? '')
            ),
            'session_email'        => $user->email,
            'session_access_level' => $accessLevel,
            'loggedin'             => true,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Login successful.');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out.');
    }
}