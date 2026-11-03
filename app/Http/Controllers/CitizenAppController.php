<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use Illuminate\Http\Request;

class CitizenAppController extends Controller
{
    /**
     * Rama 1: Pantalla principal para crear un reporte rápido
     */
    public function home()
    {
        $recentCount = IncidentReport::count();
        $resolvedCount = IncidentReport::where('status', 'resuelto')->count();

        return view('citizen.home', compact('recentCount', 'resolvedCount'));
    }

    /**
     * Rama 2: Mapa comunitario y lista de reportes vecinales
     */
    public function feed()
    {
        $reports = IncidentReport::where('status', '!=', 'rechazado')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('citizen.feed', compact('reports'));
    }

    /**
     * Rama 3: Búsqueda y rastreo de folio ciudadano
     */
    public function track(Request $request)
    {
        $initialFolio = $request->query('folio', '');
        $latestReports = IncidentReport::orderBy('created_at', 'desc')->take(4)->get();

        return view('citizen.track', compact('initialFolio', 'latestReports'));
    }

    /**
     * Rama 4: Directorio Municipal de Contactos (Regidores, Directores, DIF Municipal y Emergencias)
     */
    public function directory(Request $request)
    {
        $contacts = [
            // REGIDORES
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Primera',
                'responsible'   => 'Comisión de Obras Públicas, Comunicaciones y Desarrollo Urbano',
                'phone'         => '7658449001',
                'phone_display' => '765 844 9000 ext. 101',
                'whatsapp'      => '527651002601',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-person-digging'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Segunda',
                'responsible'   => 'Comisión de Hacienda y Patrimonio Municipal',
                'phone'         => '7658449002',
                'phone_display' => '765 844 9000 ext. 102',
                'whatsapp'      => '527651002602',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-scale-balanced'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Tercera',
                'responsible'   => 'Comisión de Educación, Cultura, Recreación y Deportes',
                'phone'         => '7658449003',
                'phone_display' => '765 844 9000 ext. 103',
                'whatsapp'      => '527651002603',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-graduation-cap'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Cuarta',
                'responsible'   => 'Comisión de Salud y Asistencia Pública Municipal',
                'phone'         => '7658449004',
                'phone_display' => '765 844 9000 ext. 104',
                'whatsapp'      => '527651002604',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-heart-pulse'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Quinta',
                'responsible'   => 'Comisión de Comercio, Central de Abastos y Mercados',
                'phone'         => '7658449005',
                'phone_display' => '765 844 9000 ext. 105',
                'whatsapp'      => '527651002605',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-store'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Sexta',
                'responsible'   => 'Comisión de Limpia Pública, Ecología y Medio Ambiente',
                'phone'         => '7658449006',
                'phone_display' => '765 844 9000 ext. 106',
                'whatsapp'      => '527651002606',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-recycle'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Séptima',
                'responsible'   => 'Comisión de Agua Potable, Drenaje y Alcantarillado',
                'phone'         => '7658449007',
                'phone_display' => '765 844 9000 ext. 107',
                'whatsapp'      => '527651002607',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-faucet-drip'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Sindicatura Única',
                'responsible'   => 'Representación Jurídica y Seguridad Pública Municipal',
                'phone'         => '7658449008',
                'phone_display' => '765 844 9000 ext. 108',
                'whatsapp'      => '527651002608',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-building-shield'
            ],

            // DIRECTORES
            [
                'category'      => 'directores',
                'title'         => 'Dirección de Obras Públicas',
                'responsible'   => 'Mantenimiento de calles, pavimentación, bacheo y caminos rurales',
                'phone'         => '7658449010',
                'phone_display' => '765 844 9010',
                'whatsapp'      => '527651103344',
                'location'      => 'Palacio Municipal, Planta Baja',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-road'
            ],
            [
                'category'      => 'directores',
                'title'         => 'Dirección de Alumbrado Público',
                'responsible'   => 'Reparación de circuitos, luminarias y electrificación municipal',
                'phone'         => '7658449011',
                'phone_display' => '765 844 9011',
                'whatsapp'      => '527651103345',
                'location'      => 'Palacio Municipal, Patio de Servicios',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:00 PM',
                'icon'          => 'fa-solid fa-lightbulb'
            ],
            [
                'category'      => 'directores',
                'title'         => 'CAEV Oficina Operadora Álamo',
                'responsible'   => 'Suministro de agua potable, fugas y desazolve de drenajes',
                'phone'         => '7658440250',
                'phone_display' => '765 844 0250',
                'whatsapp'      => '527651103346',
                'location'      => 'Av. Independencia, Zona Centro',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:00 PM',
                'icon'          => 'fa-solid fa-water'
            ],
            [
                'category'      => 'directores',
                'title'         => 'Dirección de Limpia Pública',
                'responsible'   => 'Rutas de camiones recolectores, limpieza de calles y descacharrización',
                'phone'         => '7658449012',
                'phone_display' => '765 844 9012',
                'whatsapp'      => '527651103347',
                'location'      => 'Taller Municipal, Acceso Puente',
                'hours'         => 'Lunes a Sábado 7:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-trash-can'
            ],
            [
                'category'      => 'directores',
                'title'         => 'Dirección de Protección Civil y Bomberos',
                'responsible'   => 'Atención de siniestros, emergencias, rescates y contingencias',
                'phone'         => '7658441122',
                'phone_display' => '765 844 1122',
                'whatsapp'      => '527651142233',
                'location'      => 'Base Operativa Boulevard Quintana Roo',
                'hours'         => 'Atención las 24 horas, 365 días',
                'icon'          => 'fa-solid fa-truck-medical'
            ],
            [
                'category'      => 'directores',
                'title'         => 'Dirección de Seguridad Pública y Tránsito',
                'responsible'   => 'Vigilancia preventiva vecinal, vialidad y rondines comunitarios',
                'phone'         => '7658440099',
                'phone_display' => '765 844 0099',
                'whatsapp'      => '527651109988',
                'location'      => 'Comandancia Municipal',
                'hours'         => 'Atención las 24 horas',
                'icon'          => 'fa-solid fa-shield-halved'
            ],

            // DIF MUNICIPAL
            [
                'category'      => 'dif',
                'title'         => 'DIF Municipal Álamo Temapache',
                'responsible'   => 'Presidencia y Dirección General del Sistema DIF',
                'phone'         => '7658440404',
                'phone_display' => '765 844 0404',
                'whatsapp'      => '527651120055',
                'location'      => 'Calle 5 de Febrero, Col. Heroica Veracruz',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-hand-holding-heart'
            ],
            [
                'category'      => 'dif',
                'title'         => 'Procuraduría de Protección a Niñas, Niños y Familia',
                'responsible'   => 'Defensa legal de menores, mediación familiar y trabajo social',
                'phone'         => '7658440405',
                'phone_display' => '765 844 0405',
                'whatsapp'      => '527651120056',
                'location'      => 'Instalaciones del DIF Municipal',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-children'
            ],
            [
                'category'      => 'dif',
                'title'         => 'Área Médica y Farmacia Comunitaria DIF',
                'responsible'   => 'Consultas médicas gratuitas, odontología y medicamentos básicos',
                'phone'         => '7658440406',
                'phone_display' => '765 844 0406',
                'whatsapp'      => '527651120057',
                'location'      => 'Clínica Comunitaria DIF',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:00 PM',
                'icon'          => 'fa-solid fa-user-doctor'
            ],
            [
                'category'      => 'dif',
                'title'         => 'Centro de Rehabilitación e Inclusión (CRI)',
                'responsible'   => 'Terapias físicas, estimulación temprana y apoyo a discapacidad',
                'phone'         => '7658440407',
                'phone_display' => '765 844 0407',
                'whatsapp'      => '527651120058',
                'location'      => 'Módulo CRI, Col. Heroica Veracruz',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-wheelchair'
            ],
            [
                'category'      => 'dif',
                'title'         => 'Club del Adulto Mayor e INAPAM',
                'responsible'   => 'Credencialización INAPAM, apoyos alimentarios y talleres',
                'phone'         => '7658440408',
                'phone_display' => '765 844 0408',
                'whatsapp'      => '527651120059',
                'location'      => 'Casa de Día del Adulto Mayor',
                'hours'         => 'Lunes a Viernes 8:30 AM - 2:30 PM',
                'icon'          => 'fa-solid fa-users'
            ]
        ];

        return view('citizen.directory', compact('contacts'));
    }

    /**
     * Guarda un nuevo reporte ciudadano y lo envía en vivo al Panel Municipal
     */
    public function store(Request $request)
    {
        $request->validate([
            'category'          => 'required|in:bache,drenaje,alumbrado,agua,basura,accidente',
            'title'             => 'required|string|max:120',
            'description'       => 'nullable|string|max:1000',
            'colonia'           => 'required|string|max:100',
            'address_reference' => 'nullable|string|max:255',
            'latitude'          => 'required|numeric',
            'longitude'         => 'required|numeric',
            'citizen_name'      => 'nullable|string|max:100',
            'citizen_phone'     => 'nullable|string|max:25',
            'evidence_initial'  => 'nullable|file|max:10240',
        ]);

        $routing = IncidentReport::resolveDepartmentAndPriority($request->category);
        $folio = IncidentReport::generateNextFolio();

        // Guardar evidencia fotográfica o asignar imagen de referencia local si no se tomó foto
        $evidenceUrl = null;
        if ($request->hasFile('evidence_initial')) {
            $file = $request->file('evidence_initial');
            $filename = 'cit_' . $folio . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/evidences');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $evidenceUrl = asset('uploads/evidences/' . $filename);
        } else {
            // Asignar imagen local de referencia según categoría para que nunca quede vacío
            $cat = in_array($request->category, ['bache', 'drenaje', 'alumbrado', 'agua', 'basura', 'accidente'])
                ? $request->category
                : 'bache';
            $evidenceUrl = asset('images/categories/ref_' . $cat . '.jpg');
        }

        $report = IncidentReport::create([
            'folio'             => $folio,
            'category'          => $request->category,
            'department'        => $routing['department'],
            'priority'          => $routing['priority'],
            'title'             => $request->title,
            'description'       => $request->description,
            'colonia'           => $request->colonia,
            'address_reference' => $request->address_reference,
            'latitude'          => $request->latitude,
            'longitude'         => $request->longitude,
            'citizen_name'      => $request->filled('citizen_name') ? $request->citizen_name : 'Ciudadano Anónimo',
            'citizen_phone'     => $request->citizen_phone,
            'evidence_initial'  => $evidenceUrl,
            'status'            => 'pendiente',
            'upvotes'           => 1,
        ]);

        return response()->json([
            'success' => true,
            'folio'   => $report->folio,
            'report'  => $report,
        ]);
    }

    /**
     * Incrementa el contador "+1 Me afecta" de un reporte existente
     */
    public function upvote($id)
    {
        $report = IncidentReport::findOrFail($id);
        $report->increment('upvotes');

        return response()->json([
            'success' => true,
            'upvotes' => $report->upvotes,
            'folio'   => $report->folio,
        ]);
    }

    /**
     * Verifica si ya existe un reporte similar cercano para evitar duplicados
     */
    public function checkNearby(Request $request)
    {
        $lat = (float) $request->query('lat');
        $lng = (float) $request->query('lng');
        $category = $request->query('category');

        if (!$lat || !$lng || !$category) {
            return response()->json(['found' => false]);
        }

        // Búsqueda en un radio aproximado de ~80 metros
        $delta = 0.0008;

        $nearby = IncidentReport::where('category', $category)
            ->whereIn('status', ['pendiente', 'en_proceso'])
            ->whereBetween('latitude', [$lat - $delta, $lat + $delta])
            ->whereBetween('longitude', [$lng - $delta, $lng + $delta])
            ->first();

        if ($nearby) {
            return response()->json([
                'found'  => true,
                'report' => $nearby,
            ]);
        }

        return response()->json(['found' => false]);
    }

    /**
     * Busca un reporte por su código de folio para la Rama 3 (Seguimiento)
     */
    public function searchFolio($folio)
    {
        $cleanFolio = strtoupper(trim($folio));
        $report = IncidentReport::where('folio', $cleanFolio)->first();

        if (!$report) {
            return response()->json(['found' => false], 404);
        }

        return response()->json([
            'found'  => true,
            'report' => [
                'id'                  => $report->id,
                'folio'               => $report->folio,
                'title'               => $report->title,
                'description'         => $report->description ?: 'Sin descripción adicional.',
                'category'            => ucfirst($report->category),
                'department'          => $report->department,
                'colonia'             => $report->colonia,
                'address_reference'   => $report->address_reference ?: 'Ubicación por GPS',
                'status'              => $report->status,
                'upvotes'             => $report->upvotes,
                'evidence_initial'    => $report->evidence_initial,
                'evidence_resolution' => $report->evidence_resolution,
                'resolution_notes'    => $report->resolution_notes ?: 'En espera de dictamen técnico de la cuadrilla.',
                'created_date'        => $report->created_at ? $report->created_at->format('d/m/Y H:i') : 'Reciente',
                'resolved_date'       => $report->resolved_at ? $report->resolved_at->format('d/m/Y H:i') : null,
            ],
        ]);
    }
}