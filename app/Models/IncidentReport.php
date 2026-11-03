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
     * Devuelve la imagen de evidencia inicial o null si no se adjuntó foto
     */
    public function getEvidenceInitialAttribute($value): ?string
    {
        if (!empty($value) && $value !== 'null') {
            return $value;
        }

        return null;
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
