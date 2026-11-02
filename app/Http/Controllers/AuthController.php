<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\IncidentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Muestra la pantalla de inicio de sesión
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('citizen.home');
        }

        // Credenciales locales predefinidas para acceso directo
        $defaultUser = 'user';
        $defaultPass = '123';

        return view('auth.login', compact('defaultUser', 'defaultPass'));
    }

    /**
     * Procesa el inicio de sesión
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = trim($request->input('username'));
        $password = $request->input('password');
        $remember = true; // El login se mantiene abierto como solicitó el usuario

        // Permite autenticarse tanto por nombre de usuario como por correo
        $user = User::where('username', $login)
            ->orWhere('email', $login)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('citizen.home'))
                ->with('status', '¡Bienvenido(a) a Álamo Reporta!');
        }

        return back()->withErrors([
            'username' => 'Credenciales inválidas. Usa el usuario "user" y contraseña "123".',
        ])->withInput();
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Muestra el perfil del ciudadano autenticado
     */
    public function profile()
    {
        $user = Auth::user();
        
        // Conteo de reportes realizados o vinculados al usuario
        $myReportsCount = IncidentReport::where('citizen_name', $user->name)
            ->orWhere('citizen_phone', $user->phone)
            ->count();

        return view('citizen.profile', compact('user', 'myReportsCount'));
    }

    /**
     * Actualiza los datos del perfil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'nullable|string|max:25',
            'colonia' => 'nullable|string|max:100',
        ]);

        $user->update([
            'name'    => $request->name,
            'phone'   => $request->phone,
            'colonia' => $request->colonia,
        ]);

        return back()->with('success', 'Tus datos de perfil han sido actualizados con éxito.');
    }
}

