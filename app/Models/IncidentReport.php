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
     * Devuelve la imagen de evidencia inicial o la imagen por defecto según su categoría
     */
    public function getEvidenceInitialAttribute($value): string
    {
        if (!empty($value) && $value !== 'null') {
            return $value;
        }

        return self::getDefaultImageForCategory($this->category);
    }

    /**
     * Obtiene la ruta de imagen por defecto según la categoría del reporte
     */
    public static function getDefaultImageForCategory(?string $category): string
    {
        $cat = strtolower(trim((string)$category));
        return match ($cat) {
            'bache'     => asset('images/categories/ref_bache.jpg'),
            'drenaje'   => asset('images/categories/ref_drenaje.jpg'),
            'alumbrado' => asset('images/categories/ref_alumbrado.jpg'),
            'agua'      => asset('images/categories/ref_agua.jpg'),
            'basura'    => asset('images/categories/ref_basura.jpg'),
            'accidente' => asset('images/categories/ref_accidente.jpg'),
            default     => asset('images/categories/ref_bache.jpg'),
        };
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
