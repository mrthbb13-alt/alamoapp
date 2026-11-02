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
     * Resuelve el departamento asignado y la prioridad según la categoría seleccionada
     */
    public static function resolveDepartmentAndPriority(string $category): array
    {
        return match (strtolower(trim($category))) {
            'bache' => [
                'department' => 'Obras Públicas',
                'priority'   => 'media',
            ],
            'drenaje' => [
                'department' => 'CAEV / Drenaje',
                'priority'   => 'alta',
            ],
            'alumbrado' => [
                'department' => 'Alumbrado Público',
                'priority'   => 'media',
            ],
            'agua' => [
                'department' => 'Agua Potable',
                'priority'   => 'alta',
            ],
            'basura' => [
                'department' => 'Limpia Pública',
                'priority'   => 'baja',
            ],
            'accidente' => [
                'department' => 'Protección Civil',
                'priority'   => 'urgente',
            ],
            default => [
                'department' => 'Atención Ciudadana',
                'priority'   => 'media',
            ],
        };
    }

    /**
     * Genera el siguiente folio consecutivo con formato ALM-{AÑO}-{0001}
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

