<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    use HasFactory;

    protected $table = 'incident_reports';

    protected $fillable = [
        'folio',
        'category',
        'department',
        'priority',
        'title',
        'description',
        'colonia',
        'address_reference',
        'latitude',
        'longitude',
        'citizen_name',
        'citizen_phone',
        'evidence_initial',
        'evidence_resolution',
        'resolution_notes',
        'status',
        'upvotes',
        'resolved_at',
    ];

    protected $casts = [
        'latitude'    => 'float',
        'longitude'   => 'float',
        'upvotes'     => 'integer',
        'resolved_at' => 'datetime',
    ];

    /**
     * Sincroniza automáticamente una imagen subida hacia la raíz public_html/images de Hostinger
     * para que ambas plataformas (App Ciudadana y Centro de Mando) puedan consumirla de inmediato.
     */
    public static function syncImageToHostingerPublicHtml(string $filename, string $sourcePath): void
    {
        if (!file_exists($sourcePath)) {
            return;
        }

        $candidateDirs = [
            // Si alamoapp está como subcarpeta dentro de public_html (ej: /public_html/alamoapp):
            dirname(base_path()) . '/images',
            dirname(base_path()) . '/public_html/images',
            // Si alamoapp está al nivel de la raíz de la cuenta (ej: /home/u841520431/alamoapp):
            base_path('../public_html/images'),
            base_path('../../public_html/images'),
            // Rutas absolutas estándar en servidor Hostinger:
            '/home/u841520431/public_html/images',
            '/home/u841520431/domains/gestionmaplingps.com/public_html/images',
        ];

        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $candidateDirs[] = $docRoot . '/images';
            $candidateDirs[] = dirname($docRoot) . '/images';
            $candidateDirs[] = dirname($docRoot) . '/public_html/images';
        }

        $sourceDirReal = realpath(dirname($sourcePath));

        foreach ($candidateDirs as $dir) {
            try {
                if (!file_exists($dir)) {
                    $parent = dirname($dir);
                    if (is_dir($parent) && is_writable($parent)) {
                        @mkdir($dir, 0775, true);
                    }
                }
                if (is_dir($dir)) {
                    $targetReal = realpath($dir);
                    if ($targetReal !== $sourceDirReal) {
                        @copy($sourcePath, $dir . DIRECTORY_SEPARATOR . $filename);
                    }
                }
            } catch (\Throwable $e) {
                // Silencioso para no detener el flujo del usuario
            }
        }
    }

    /**
     * Normaliza cualquier ruta de evidencia para que siempre devuelva una URL válida accesible en el navegador (/images/...)
     */
    public static function formatEvidenceUrl(?string $value): ?string
    {
        if (empty($value) || $value === 'null' || $value === 'undefined') {
            return null;
        }

        $value = trim($value);

        // Si ya es una URL absoluta HTTP/HTTPS
        if (preg_match('/^https?:\/\//i', $value)) {
            return $value;
        }

        // Limpiar diagonales invertidas
        $normalized = str_replace('\\', '/', $value);

        // Si empieza con /images/ o images/
        if (preg_match('/^\/?images\/(.+)$/i', $normalized, $matches)) {
            return '/images/' . ltrim($matches[1], '/');
        }

        // Si es solo el nombre de archivo (ej: res_ALM-2026-0002_1793738183.jpeg)
        $filename = basename($normalized);
        if (!empty($filename)) {
            return '/images/' . $filename;
        }

        return '/' . ltrim($normalized, '/');
    }

    /**
     * Localiza físicamente un archivo de imagen en cualquiera de los directorios de Hostinger
     * (tanto en la carpeta de AlamoApp como en la raíz public_html compartida de la segunda plataforma)
     */
    public static function locateImageOnHostinger(string $filename): ?string
    {
        $filename = basename(str_replace('\\', '/', $filename));
        if (empty($filename)) {
            return null;
        }

        $localDir = public_path('images');
        $localPath = $localDir . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($localPath)) {
            return $localPath;
        }

        // Directorios candidatos donde la segunda plataforma (C2 / Centro de Mando) almacena imágenes en Hostinger
        $candidateDirs = [
            $localDir,
            base_path('images'),
            base_path('../public_html/images'),
            base_path('../../public_html/images'),
            base_path('../images'),
            dirname(base_path()) . '/images',
            dirname(base_path()) . '/public_html/images',
            dirname(dirname(base_path())) . '/public_html/images',
            dirname(dirname(base_path())) . '/images',
            '/home/u841520431/public_html/images',
            '/home/u841520431/domains/gestionmaplingps.com/public_html/images',
            '/home/u841520431/domains/gestionmaplingps.com/public_html/alamoapp/images',
            '/home/u841520431/domains/gestionmaplingps.com/public_html/alamoapp/public/images',
            '/home/u841520431/public_html/alamoapp/images',
            '/home/u841520431/public_html/alamoapp/public/images',
        ];

        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
            $candidateDirs[] = $docRoot . '/images';
            $candidateDirs[] = dirname($docRoot) . '/images';
            $candidateDirs[] = dirname($docRoot) . '/public_html/images';
            $candidateDirs[] = dirname(dirname($docRoot)) . '/public_html/images';
            $candidateDirs[] = dirname(dirname($docRoot)) . '/images';
        }

        foreach (array_unique($candidateDirs) as $dir) {
            if (!$dir || !is_dir($dir)) {
                continue;
            }
            $target = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($target) && is_file($target)) {
                // Si está en otra carpeta de Hostinger, copiar a public/images de AlamoApp para caché ultra-rápido
                try {
                    if (!file_exists($localDir)) {
                        @mkdir($localDir, 0775, true);
                    }
                    if (is_dir($localDir) && realpath($dir) !== realpath($localDir)) {
                        @copy($target, $localPath);
                    }
                } catch (\Throwable $e) {}

                return $target;
            }
        }

        return null;
    }

    /**
     * Accesor para evidencia inicial ciudadana
     */
    public function getEvidenceInitialAttribute($value): ?string
    {
        return self::formatEvidenceUrl($value);
    }

    /**
     * Accesor para evidencia de resolución municipal
     */
    public function getEvidenceResolutionAttribute($value): ?string
    {
        return self::formatEvidenceUrl($value);
    }

    /**
     * Obtiene el placeholder institucional de Álamo si se requiere imagen
     */
    public static function getDefaultImageForCategory(?string $category): string
    {
        return asset('images/placeholder.svg');
    }

    /**
     * Asigna automáticamente la Dirección Municipal y Prioridad según la categoría reportada
     */
    public static function resolveDepartmentAndPriority(string $category): array
    {
        return match (strtolower(trim($category))) {
            'drenaje'   => ['department' => 'Agua Potable y Drenaje (CAEV/Municipal)', 'priority' => 'alta'],
            'bache'     => ['department' => 'Dirección de Obras Públicas', 'priority' => 'media'],
            'alumbrado' => ['department' => 'Servicios Públicos / Alumbrado', 'priority' => 'media'],
            'agua'      => ['department' => 'Agua Potable y Drenaje (CAEV/Municipal)', 'priority' => 'alta'],
            'basura'    => ['department' => 'Limpia Pública y Ecología', 'priority' => 'baja'],
            'accidente' => ['department' => 'Protección Civil y Tránsito Municipal', 'priority' => 'critica'],
            default     => ['department' => 'Atención Ciudadana General', 'priority' => 'media'],
        };
    }

    /**
     * Genera el siguiente folio consecutivo oficial para Álamo Temapache
     */
    public static function generateNextFolio(): string
    {
        $year = date('Y');
        $prefix = "ALM-{$year}-";

        $latest = self::where('folio', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($latest && preg_match('/ALM-\d{4}-(\d+)/', $latest->folio, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('%s%04d', $prefix, $nextNumber);
    }
}
