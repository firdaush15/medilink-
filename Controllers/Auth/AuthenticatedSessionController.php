<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // ✅ Redirect based on user role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            
            case 'doctor':
                return redirect()->route('doctor.dashboard');
            
            case 'nurse':
                return redirect()->route('nurse.dashboard');
            
            case 'receptionist':
                return redirect()->route('receptionist.dashboard');
            
            case 'pharmacist': // 🧪 Added this role
                return redirect()->route('pharmacist.dashboard');
            
            case 'patient':
                return redirect('/dashboard')->with('info', 'Patient dashboard coming soon');
            
            default:
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')->withErrors([
                    'email' => 'Unauthorized access. Please contact the administrator.',
                ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
