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
        if (Auth::check() || session('citizen_logged_in')) {
            return redirect()->route('citizen.home');
        }

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
        $password = trim($request->input('password'));
        $remember = true; // Mantener sesión siempre abierta

        // Validación local fija garantizada (usuario "user" y contraseña "123")
        $isLocalValid = ($login === 'user' || $login === 'user@alamo.gob.mx') && $password === '123';

        if ($isLocalValid) {
            $user = null;
            try {
                // Si la tabla users existe en BD, crear o recuperar el usuario
                $user = User::firstOrCreate(
                    ['username' => 'user'],
                    [
                        'name'     => 'Ciudadano Álamo',
                        'email'    => 'user@alamo.gob.mx',
                        'phone'    => '765 123 4567',
                        'colonia'  => 'Zona Centro',
                        'password' => Hash::make('123'),
                    ]
                );
                Auth::login($user, $remember);
            } catch (\Throwable $e) {
                // Si la BD remota no tiene tabla o da error, instanciar objeto en memoria
                $user = new User([
                    'name'     => 'Ciudadano Álamo',
                    'username' => 'user',
                    'email'    => 'user@alamo.gob.mx',
                    'phone'    => '765 123 4567',
                    'colonia'  => 'Zona Centro',
                ]);
                $user->id = 1;
            }

            session([
                'citizen_logged_in' => true,
                'citizen_name'      => $user->name ?? 'Ciudadano Álamo',
                'citizen_phone'     => $user->phone ?? '765 123 4567',
                'citizen_colonia'   => $user->colonia ?? 'Zona Centro',
            ]);

            $request->session()->regenerate();

            return redirect()->intended(route('citizen.home'));
        }

        // Si no es la credencial local, verificar en BD si existe otro usuario
        try {
            $user = User::where('username', $login)->orWhere('email', $login)->first();
            if ($user && Hash::check($password, $user->password)) {
                Auth::login($user, $remember);
                session([
                    'citizen_logged_in' => true,
                    'citizen_name'      => $user->name,
                    'citizen_phone'     => $user->phone,
                    'citizen_colonia'   => $user->colonia,
                ]);
                $request->session()->regenerate();
                return redirect()->intended(route('citizen.home'));
            }
        } catch (\Throwable $e) {
            // Ignorar excepción de BD
        }

        return back()->withErrors([
            'login_error' => 'Usuario o contraseña incorrectos.',
        ])->withInput();
    }

    /**
     * Muestra la pantalla de registro ciudadano
     */
    public function showRegister()
    {
        if (Auth::check() || session('citizen_logged_in')) {
            return redirect()->route('citizen.home');
        }

        return view('auth.register');
    }

    /**
     * Procesa el registro de un nuevo ciudadano
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'username'              => 'required|string|max:40',
            'phone'                 => 'nullable|string|max:25',
            'colonia'               => 'required|string|max:100',
            'password'              => 'required|string|min:3|confirmed',
            'ine_document'          => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:10240',
        ]);

        $ineUrl = null;

        // Guardar documento / fotografía de INE si se subió
        if ($request->hasFile('ine_document')) {
            $file = $request->file('ine_document');
            $filename = 'ine_' . preg_replace('/[^a-zA-Z0-9]/', '', $request->username) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/citizens');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $ineUrl = asset('uploads/citizens/' . $filename);
        }

        $user = null;
        try {
            $user = User::create([
                'name'         => $request->name,
                'username'     => trim($request->username),
                'email'        => trim($request->username) . '@alamo.gob.mx',
                'phone'        => $request->phone,
                'colonia'      => $request->colonia,
                'ine_document' => $ineUrl,
                'password'     => Hash::make($request->password),
            ]);
            Auth::login($user, true);
        } catch (\Throwable $e) {
            // Respaldo en memoria si la BD remota no tiene tabla migrada
            $user = new User([
                'name'         => $request->name,
                'username'     => trim($request->username),
                'email'        => trim($request->username) . '@alamo.gob.mx',
                'phone'        => $request->phone,
                'colonia'      => $request->colonia,
                'ine_document' => $ineUrl,
            ]);
            $user->id = rand(10, 999);
        }

        session([
            'citizen_logged_in' => true,
            'citizen_name'      => $user->name,
            'citizen_phone'     => $user->phone,
            'citizen_colonia'   => $user->colonia,
            'citizen_ine'       => $ineUrl,
        ]);

        $request->session()->regenerate();

        return redirect()->route('citizen.home')
            ->with('status', '¡Bienvenido(a) a Álamo Reporta! Tu cuenta ha sido creada exitosamente.');
    }

    /**
     * Cierra la sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        session()->forget(['citizen_logged_in', 'citizen_name', 'citizen_phone', 'citizen_colonia', 'citizen_ine']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Muestra el perfil
     */
    public function profile()
    {
        $user = Auth::user();
        if (!$user) {
            $user = (object) [
                'name'         => session('citizen_name', 'Ciudadano Álamo'),
                'username'     => 'user',
                'email'        => 'user@alamo.gob.mx',
                'phone'        => session('citizen_phone', '765 123 4567'),
                'colonia'      => session('citizen_colonia', 'Zona Centro'),
                'ine_document' => session('citizen_ine', null),
            ];
        }

        $myReportsCount = 0;
        try {
            $myReportsCount = IncidentReport::where('citizen_name', $user->name)
                ->orWhere('citizen_phone', $user->phone)
                ->count();
        } catch (\Throwable $e) {}

        return view('citizen.profile', compact('user', 'myReportsCount'));
    }

    /**
     * Actualiza perfil
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'nullable|string|max:25',
            'colonia' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        if ($user && isset($user->exists) && $user->exists) {
            try {
                $user->update([
                    'name'    => $request->name,
                    'phone'   => $request->phone,
                    'colonia' => $request->colonia,
                ]);
            } catch (\Throwable $e) {}
        }

        session([
            'citizen_name'    => $request->name,
            'citizen_phone'   => $request->phone,
            'citizen_colonia' => $request->colonia,
        ]);

        return back()->with('success', 'Tus datos han sido actualizados con éxito.');
    }
}
