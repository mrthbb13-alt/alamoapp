<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

            // Drivers independientes de base de datos para no requerir tablas especiales
            'session.driver' => 'file',
            'cache.default'  => 'file',
            'queue.default'  => 'sync',
        ]);

        try {
            DB::purge('sqlite');
            DB::purge('mysql');
            DB::setDefaultConnection('mysql');

            // Salvaguarda: crear tabla 'sessions' si no existe
            if (!Schema::hasTable('sessions')) {
                Schema::create('sessions', function ($table) {
                    $table->string('id')->primary();
                    $table->foreignId('user_id')->nullable()->index();
                    $table->string('ip_address', 45)->nullable();
                    $table->text('user_agent')->nullable();
                    $table->longText('payload');
                    $table->integer('last_activity')->index();
                });
            }

            // 5. Garantizar carpeta public_html/images en Hostinger para compartir fotos entre ambas plataformas
            $candidateImagesDirs = [
                base_path('../public_html/images'),
                base_path('../../public_html/images'),
                dirname(base_path()) . '/images',
                dirname(base_path()) . '/public_html/images',
                '/home/u841520431/public_html/images',
                '/home/u841520431/domains/gestionmaplingps.com/public_html/images',
            ];
            foreach ($candidateImagesDirs as $dir) {
                if (!file_exists($dir)) {
                    $parent = dirname($dir);
                    if (is_dir($parent) && is_writable($parent)) {
                        @mkdir($dir, 0775, true);
                    }
                }
                if (is_dir($dir) && !file_exists($dir . '/placeholder.svg') && file_exists(public_path('images/placeholder.svg'))) {
                    @copy(public_path('images/placeholder.svg'), $dir . '/placeholder.svg');
                }
            }
        } catch (\Throwable $e) {
            // Ignorar excepción en entornos sin extensión PDO MySQL
        }
    }
}
