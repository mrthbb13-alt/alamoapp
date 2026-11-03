<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Forzar HTTPS en producción o proxy inverso
        if ($this->app->environment('production') || request()->isSecure() || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        // 2. Eliminar caché de configuración vieja si existe en el hosting
        $cachedConfig = base_path('bootstrap/cache/config.php');
        if (file_exists($cachedConfig)) {
            @unlink($cachedConfig);
        }

        // 3. Neutralizar archivo SQLite residual en el servidor para evitar que use datos viejos
        $sqliteFile = database_path('database.sqlite');
        if (file_exists($sqliteFile)) {
            @rename($sqliteFile, database_path('database.sqlite.bak'));
        }

        // 4. Forzar estrictamente la conexión a MySQL de Hostinger: u841520431_AlamoApp
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.driver'    => 'mysql',
            'database.connections.mysql.host'      => '127.0.0.1',
            'database.connections.mysql.port'      => '3306',
            'database.connections.mysql.database'  => 'u841520431_AlamoApp',
            'database.connections.mysql.username'  => 'u841520431_MaplinGPS',
            'database.connections.mysql.password'  => 'Miche11e@dmin',
            'database.connections.mysql.charset'   => 'utf8mb4',
            'database.connections.mysql.collation' => 'utf8mb4_unicode_ci',
            'database.connections.mysql.prefix'    => '',
            'database.connections.mysql.strict'    => false,
        ]);

        try {
            DB::purge('sqlite');
            DB::purge('mysql');
            DB::setDefaultConnection('mysql');
        } catch (\Throwable $e) {
            // Ignorar excepción en entornos sin extensión PDO MySQL
        }
    }
}
