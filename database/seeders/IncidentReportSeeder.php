<?php

namespace Database\Seeders;

use App\Models\IncidentReport;
use Illuminate\Database\Seeder;
<<<<<<< HEAD
=======
use Carbon\Carbon;
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c

class IncidentReportSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
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

=======
        $reports = [
            [
                'folio' => 'ALM-2026-0001',
                'category' => 'drenaje',
                'department' => 'Agua Potable y Drenaje (CAEV/Municipal)',
                'priority' => 'alta',
                'title' => 'Alcantarilla sin tapa en zona escolar',
                'description' => 'La tapa del registro de drenaje colapsó a mitad del carril. Riesgo alto para motociclistas y peatones.',
                'colonia' => 'Zona Centro',
                'address_reference' => 'Av. Independencia esq. Calle Benito Juárez, frente a farmacia',
                'latitude' => 20.9118500,
                'longitude' => -97.6749200,
                'citizen_name' => 'Roberto Hernández',
                'citizen_phone' => '7651023344',
                'evidence_initial' => 'https://images.unsplash.com/photo-1584463623578-377269321274?auto=format&fit=crop&w=800&q=80',
                'evidence_resolution' => null,
                'resolution_notes' => null,
                'status' => 'pendiente',
                'upvotes' => 14,
                'created_at' => Carbon::now()->subMinutes(45),
            ],
            [
                'folio' => 'ALM-2026-0002',
                'category' => 'bache',
                'department' => 'Dirección de Obras Públicas',
                'priority' => 'media',
                'title' => 'Hundimiento y bache profundo en Bulevar',
                'description' => 'Con las lluvias recientes se abrió un bache de más de 1 metro de ancho que daña llantas.',
                'colonia' => 'Col. Heroica Veracruz',
                'address_reference' => 'Bulevar Quintana Roo, a 100m de la gasolinera',
                'latitude' => 20.9152000,
                'longitude' => -97.6698000,
                'citizen_name' => 'María Elena Cortés',
                'citizen_phone' => '7658891122',
                'evidence_initial' => 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?auto=format&fit=crop&w=800&q=80',
                'evidence_resolution' => null,
                'resolution_notes' => 'Cuadrilla de bacheo #2 asignada con asfalto en frío.',
                'status' => 'en_proceso',
                'upvotes' => 9,
                'created_at' => Carbon::now()->subHours(3),
            ],
            [
                'folio' => 'ALM-2026-0003',
                'category' => 'accidente',
                'department' => 'Protección Civil y Tránsito Municipal',
                'priority' => 'critica',
                'title' => 'Choque por alcance y derrame de aceite',
                'description' => 'Percance entre camioneta naranjera y motocicleta. Tráfico detenido en acceso al puente.',
                'colonia' => 'Acceso Puente López Portillo',
                'address_reference' => 'Subida al Puente José López Portillo, carril norte',
                'latitude' => 20.9078000,
                'longitude' => -97.6795000,
                'citizen_name' => 'Jorge Luis Méndez',
                'citizen_phone' => '7651109988',
                'evidence_initial' => 'https://images.unsplash.com/photo-1566473965997-3de9c817e938?auto=format&fit=crop&w=800&q=80',
                'evidence_resolution' => null,
                'resolution_notes' => null,
                'status' => 'pendiente',
                'upvotes' => 22,
                'created_at' => Carbon::now()->subMinutes(12),
            ],
            [
                'folio' => 'ALM-2026-0004',
                'category' => 'alumbrado',
                'department' => 'Servicios Públicos / Alumbrado',
                'priority' => 'media',
                'title' => '3 Luminarias apagadas en calle principal',
                'description' => 'Toda la cuadra queda completamente a oscuras después de las 7:00 PM.',
                'colonia' => 'Col. Pantepec',
                'address_reference' => 'Calle Niños Héroes entre Reforma y Allende',
                'latitude' => 20.9139000,
                'longitude' => -97.6782000,
                'citizen_name' => 'Lucía Morales',
                'citizen_phone' => '7654432211',
                'evidence_initial' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?auto=format&fit=crop&w=800&q=80',
                'evidence_resolution' => 'https://images.unsplash.com/photo-1542382257-80dedb725088?auto=format&fit=crop&w=800&q=80',
                'resolution_notes' => 'Se reemplazaron fotoceldas y lámparas LED de 100W por la cuadrilla nocturna.',
                'status' => 'resuelto',
                'upvotes' => 6,
                'resolved_at' => Carbon::now()->subHours(1),
                'created_at' => Carbon::now()->subHours(18),
            ],
            [
                'folio' => 'ALM-2026-0005',
                'category' => 'agua',
                'department' => 'Agua Potable y Drenaje (CAEV/Municipal)',
                'priority' => 'alta',
                'title' => 'Fuga de agua potable en banqueta',
                'description' => 'Tubo roto desperdiciando miles de litros de agua limpia desde la madrugada.',
                'colonia' => 'Col. Unidad y Trabajo',
                'address_reference' => 'Calle Emiliano Zapata #42',
                'latitude' => 20.9091000,
                'longitude' => -97.6712000,
                'citizen_name' => 'Carlos Domínguez',
                'citizen_phone' => '7657765544',
                'evidence_initial' => 'https://images.unsplash.com/photo-1585704032915-c3400ca199e7?auto=format&fit=crop&w=800&q=80',
                'evidence_resolution' => null,
                'resolution_notes' => 'Personal técnico en sitio cerrando válvula de sector para reparación.',
                'status' => 'en_proceso',
                'upvotes' => 11,
                'created_at' => Carbon::now()->subHours(2),
            ],
        ];

        foreach ($reports as $report) {
            IncidentReport::updateOrCreate(['folio' => $report['folio']], $report);
        }
    }
}
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c
