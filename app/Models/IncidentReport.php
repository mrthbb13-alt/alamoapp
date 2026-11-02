<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $table = 'incident_reports';

=======
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c
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
<<<<<<< HEAD
        'latitude'    => 'float',
        'longitude'   => 'float',
        'upvotes'     => 'integer',
=======
        'latitude' => 'float',
        'longitude' => 'float',
        'upvotes' => 'integer',
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c
        'resolved_at' => 'datetime',
    ];

    /**
<<<<<<< HEAD
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
=======
     * Asigna automáticamente la Dirección Municipal y Prioridad según la categoría reportada
     */
    public static function resolveDepartmentAndPriority(string $category): array
    {
        return match ($category) {
            'drenaje'   => ['department' => 'Agua Potable y Drenaje (CAEV/Municipal)', 'priority' => 'alta'],
            'bache'     => ['department' => 'Dirección de Obras Públicas', 'priority' => 'media'],
            'alumbrado' => ['department' => 'Servicios Públicos / Alumbrado', 'priority' => 'media'],
            'agua'      => ['department' => 'Agua Potable y Drenaje (CAEV/Municipal)', 'priority' => 'alta'],
            'basura'    => ['department' => 'Limpia Pública y Ecología', 'priority' => 'baja'],
            'accidente' => ['department' => 'Protección Civil y Tránsito Municipal', 'priority' => 'critica'],
            default     => ['department' => 'Atención Ciudadana General', 'priority' => 'media'],
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c
        };
    }

    /**
<<<<<<< HEAD
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

=======
     * Genera el siguiente folio consecutivo oficial para Álamo Temapache
     */
    public static function generateNextFolio(): string
    {
        $lastId = self::max('id') ?? 0;
        return 'ALM-' . date('Y') . '-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
    }
}
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c
