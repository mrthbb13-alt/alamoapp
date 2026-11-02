<?php

namespace Database\Seeders;

use App\Models\IncidentReport;
use Illuminate\Database\Seeder;

class IncidentReportSeeder extends Seeder
{
    public function run(): void
    {
        $placeholder = asset('images/placeholder.svg');

        $reports = [
            [
                'folio'             => 'ALM-2026-0001',
                'category'          => 'bache',
                'department'        => 'Obras Públicas',
                'priority'          => 'media',
                'title'             => 'Bache profundo en Av. Independencia',
                'description'       => 'Bache de aproximadamente 1 metro de diámetro que daña suspensiones.',
                'colonia'           => 'Zona Centro',
                'address_reference' => 'Frente al parque Benito Juárez',
                'latitude'          => 20.9114,
                'longitude'         => -97.6744,
                'citizen_name'      => 'Juan Pérez',
                'citizen_phone'     => '7651000001',
                'evidence_initial'  => $placeholder,
                'evidence_resolution' => null,
                'resolution_notes'  => null,
                'status'            => 'pendiente',
                'upvotes'           => 14,
                'resolved_at'       => null,
                'created_at'        => now()->subHours(5),
            ],
            [
                'folio'             => 'ALM-2026-0002',
                'category'          => 'drenaje',
                'department'        => 'CAEV / Drenaje',
                'priority'          => 'alta',
                'title'             => 'Tapa de alcantarilla fracturada',
                'description'       => 'Peligro para peatones y motociclistas por rejilla rota.',
                'colonia'           => 'Col. Heroica Veracruz',
                'address_reference' => 'Esq. con calle 5 de Mayo',
                'latitude'          => 20.9135,
                'longitude'         => -97.6710,
                'citizen_name'      => 'María Hernández',
                'citizen_phone'     => '7651000002',
                'evidence_initial'  => $placeholder,
                'evidence_resolution' => null,
                'resolution_notes'  => 'Cuadrilla programada para inspección el día de mañana.',
                'status'            => 'en_proceso',
                'upvotes'           => 8,
                'resolved_at'       => null,
                'created_at'        => now()->subDays(1),
            ],
            [
                'folio'             => 'ALM-2026-0003',
                'category'          => 'alumbrado',
                'department'        => 'Alumbrado Público',
                'priority'          => 'media',
                'title'             => 'Lámpara LED sin encender por las noches',
                'description'       => 'La calle queda totalmente a oscuras desde hace 4 días.',
                'colonia'           => 'Col. Unidad y Trabajo',
                'address_reference' => 'Poste número 14 frente a la tienda La Palma',
                'latitude'          => 20.9080,
                'longitude'         => -97.6780,
                'citizen_name'      => 'Carlos Domínguez',
                'citizen_phone'     => '7651000003',
                'evidence_initial'  => $placeholder,
                'evidence_resolution' => $placeholder,
                'resolution_notes'  => 'Se sustituyó la fotocelda y foco LED de 100W.',
                'status'            => 'resuelto',
                'upvotes'           => 19,
                'resolved_at'       => now()->subHours(2),
                'created_at'        => now()->subDays(2),
            ],
        ];

        foreach ($reports as $item) {
            IncidentReport::updateOrCreate(['folio' => $item['folio']], $item);
        }
    }
}

