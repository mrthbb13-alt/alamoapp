<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\MunicipalDashboardController;
use App\Http\Controllers\CitizenAppController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CitizenAuth;

// ====================================================
// AUTENTICACIÓN CIUDADANA Y ACCESO INICIAL
// ====================================================
Route::get('/', function () {
    return (Auth::check() || session('citizen_logged_in'))
        ? redirect()->route('citizen.home')
        : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// ====================================================
// ENTREGA Y SINCRONIZACIÓN DE IMÁGENES ENTRE PLATAFORMAS (HOSTINGER)
// ====================================================
Route::get('/images/{filename}', function ($filename) {
    $cleanFilename = basename($filename);
    $foundPath = \App\Models\IncidentReport::locateImageOnHostinger($cleanFilename);

    if ($foundPath && file_exists($foundPath)) {
        $mime = @mime_content_type($foundPath) ?: 'image/jpeg';
        return response()->file($foundPath, [
            'Content-Type'  => $mime,
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }

    $placeholder = public_path('images/placeholder.svg');
    if (file_exists($placeholder)) {
        return response()->file($placeholder, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    abort(404);
})->where('filename', '[A-Za-z0-9_\-\.]+');

// Diagnóstico de Conexión en Vivo a Hostinger MySQL
Route::get('/api/db-status', function () {
    try {
        $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbName = \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $count = \App\Models\IncidentReport::count();

        // Diagnóstico de rutas e imágenes en el servidor Hostinger
        $candidateDirs = [
            'public_images'      => public_path('images'),
            'base_images'        => base_path('images'),
            'docroot_images'     => isset($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/images' : null,
            'parent_images'      => dirname(base_path()) . '/images',
            'parent_pub_images'  => dirname(base_path()) . '/public_html/images',
            'home_pub_images'    => '/home/u841520431/public_html/images',
            'domains_pub_images' => '/home/u841520431/domains/gestionmaplingps.com/public_html/images',
        ];

        $dirInfo = [];
        $targetFile = 'res_ALM-2026-0002_1793738183.jpeg';
        $foundAt = [];

        foreach ($candidateDirs as $key => $dir) {
            if (!$dir) continue;
            $exists = file_exists($dir);
            $isDir = is_dir($dir);
            $files = [];
            if ($isDir) {
                $scanned = @scandir($dir) ?: [];
                $files = array_values(array_filter($scanned, fn($f) => !in_array($f, ['.', '..'])));
                if (in_array($targetFile, $files)) {
                    $foundAt[] = $dir . '/' . $targetFile;
                }
            }
            $dirInfo[$key] = [
                'path' => $dir,
                'exists' => $exists,
                'is_dir' => $isDir,
                'files' => array_slice($files, 0, 15),
            ];
        }

        return response()->json([
            'status'        => 'connected',
            'driver'        => $driver,
            'database'      => $dbName,
            'reports_count' => $count,
            'doc_root'      => $_SERVER['DOCUMENT_ROOT'] ?? null,
            'base_path'     => base_path(),
            'public_path'   => public_path(),
            'found_target'  => $foundAt,
            'dirs'          => $dirInfo,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
});

// Endpoint opcional para sincronizar Git desde navegador o webhook
Route::get('/api/git-pull', function () {
    if (request('token') !== 'alamo2026') {
        abort(403);
    }
    try {
        $output = shell_exec('git pull origin main 2>&1');
        return response()->json([
            'success' => true,
            'output'  => $output,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error'   => $e->getMessage(),
        ], 500);
    }
});

// ====================================================
// FASE 1: PLATAFORMA WEB GOBIERNO MUNICIPAL (BACKOFFICE / C2)
// ====================================================
Route::prefix('panel-municipal')->group(function () {
    Route::get('/', [MunicipalDashboardController::class, 'index'])->name('municipal.dashboard');
    Route::get('/api/poll', [MunicipalDashboardController::class, 'poll'])->name('municipal.poll');
    Route::post('/reportes/{id}/actualizar', [MunicipalDashboardController::class, 'updateStatus'])->name('municipal.update');
});

// ====================================================
// FASE 2: APLICACIÓN WEB CIUDADANA (PROTEGIDA CON LOGIN)
// ====================================================
Route::middleware(CitizenAuth::class)->prefix('reportar')->group(function () {
    // Vistas principales
    Route::get('/', [CitizenAppController::class, 'home'])->name('citizen.home');
    Route::get('/comunidad', [CitizenAppController::class, 'feed'])->name('citizen.feed');
    Route::get('/seguimiento', [CitizenAppController::class, 'track'])->name('citizen.track');
    Route::get('/directorio', [CitizenAppController::class, 'directory'])->name('citizen.directory');

    // Apartado de Perfil Ciudadano
    Route::get('/perfil', [AuthController::class, 'profile'])->name('citizen.profile');
    Route::post('/perfil/actualizar', [AuthController::class, 'updateProfile'])->name('citizen.profile.update');

    // Endpoints JSON para acciones ciudadanas
    Route::post('/guardar', [CitizenAppController::class, 'store'])->name('citizen.store');
    Route::post('/apoyar/{id}', [CitizenAppController::class, 'upvote'])->name('citizen.upvote');
    Route::get('/api/cercanos', [CitizenAppController::class, 'checkNearby'])->name('citizen.nearby');
    Route::get('/api/folio/{folio}', [CitizenAppController::class, 'searchFolio'])->name('citizen.folio');
});
