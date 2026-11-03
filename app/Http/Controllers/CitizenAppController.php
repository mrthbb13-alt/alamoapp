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
     * Datos oficiales de contacto telefónico del H. Ayuntamiento de Álamo Temapache, Ver.
     */
    public function directory(Request $request)
    {
        $contacts = [
            // SINDICATURA Y REGIDORES
            [
                'category'      => 'regidores',
                'title'         => 'Sindicatura Única',
                'responsible'   => 'María del Rosario Escudero Tolentino — Representación Jurídica y Hacienda',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 (Conmutador)',
                'location'      => 'Palacio Municipal s/n, Zona Centro',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-scale-balanced'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Primera',
                'responsible'   => 'Lic. Abimael Cabrera del Ángel — Hacienda y Patrimonio, Asentamientos Humanos y Comercio',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 ext. 101',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-building-columns'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Segunda',
                'responsible'   => 'Profra. Marisol Rosas Reyes — Educación, Cultura, Deporte y Participación Ciudadana',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 ext. 102',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-graduation-cap'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Tercera',
                'responsible'   => 'Ana Silvia Márquez Islas — Parques, Jardines, Alumbrado Público y Desarrollo Económico',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 ext. 103',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-lightbulb'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Cuarta',
                'responsible'   => 'Hilda Yazmín Juárez Galindo — Fomento Agropecuario, Ecología y Medio Ambiente',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 ext. 104',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-seedling'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Quinta',
                'responsible'   => 'Benita Silvia Del Valle Hernández — Salud, Asistencia Pública y Población',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 ext. 105',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-heart-pulse'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Sexta',
                'responsible'   => 'Macedonio Larios Hernández — Tránsito, Vialidad, Registro Civil y Panteones',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 ext. 106',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-traffic-light'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Séptima',
                'responsible'   => 'Elideth Cruz Hernández — Bibliotecas, Fomento a la Lectura, Niñez y Familia',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 ext. 107',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-book-open-reader'
            ],
            [
                'category'      => 'regidores',
                'title'         => 'Regiduría Octava',
                'responsible'   => 'Juventino Gallegos Meneses — Limpia Pública, Agua Potable y Drenaje',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 ext. 108',
                'location'      => 'Palacio Municipal, Planta Alta',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-faucet-drip'
            ],

            // DIRECTORES
            [
                'category'      => 'directores',
                'title'         => 'Dirección de Obras Públicas',
                'responsible'   => 'Mantenimiento de calles, pavimentación, bacheo y caminos rurales',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 (Atención Obras)',
                'location'      => 'Palacio Municipal, Planta Baja',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-person-digging'
            ],
            [
                'category'      => 'directores',
                'title'         => 'Dirección de Alumbrado Público',
                'responsible'   => 'Reparación de circuitos, luminarias y electrificación municipal',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 (Servicios Municipales)',
                'location'      => 'Palacio Municipal, Patio de Servicios',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:30 PM',
                'icon'          => 'fa-solid fa-lightbulb'
            ],
            [
                'category'      => 'directores',
                'title'         => 'CAEV Oficina Operadora Álamo',
                'responsible'   => 'Suministro de agua potable, fugas y desazolve de drenajes',
                'phone'         => '7658440250',
                'phone_display' => '765 844 0250 (CAEV)',
                'location'      => 'Av. Independencia, Zona Centro, Álamo',
                'hours'         => 'Lunes a Viernes 8:00 AM - 4:00 PM',
                'icon'          => 'fa-solid fa-water'
            ],
            [
                'category'      => 'directores',
                'title'         => 'Dirección de Limpia Pública',
                'responsible'   => 'Rutas de camiones recolectores, limpieza de calles y descacharrización',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 (Limpia Pública)',
                'location'      => 'Taller Municipal, Acceso Puente',
                'hours'         => 'Lunes a Sábado 7:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-trash-can'
            ],
            [
                'category'      => 'directores',
                'title'         => 'Dirección de Protección Civil y Bomberos',
                'responsible'   => 'Atención de siniestros, emergencias, rescates y contingencias',
                'phone'         => '7658449041',
                'phone_display' => '765 844 9041 / 911 Emergencias',
                'location'      => 'Base Operativa Boulevard Quintana Roo',
                'hours'         => 'Atención las 24 horas, 365 días',
                'icon'          => 'fa-solid fa-truck-medical'
            ],
            [
                'category'      => 'directores',
                'title'         => 'Seguridad Ciudadana y Tránsito Municipal',
                'responsible'   => 'Vigilancia preventiva vecinal, vialidad y rondines comunitarios',
                'phone'         => '7658440099',
                'phone_display' => '765 844 0099 / 911',
                'location'      => 'Comandancia Municipal',
                'hours'         => 'Atención las 24 horas',
                'icon'          => 'fa-solid fa-shield-halved'
            ],

            // DIF MUNICIPAL
            [
                'category'      => 'dif',
                'title'         => 'DIF Municipal Álamo Temapache',
                'responsible'   => 'Presidencia y Dirección General del Sistema DIF Municipal',
                'phone'         => '7658449857',
                'phone_display' => '765 844 9857 (Central DIF)',
                'location'      => 'Calle 5 de Febrero, Col. Heroica Veracruz, Álamo',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-hand-holding-heart'
            ],
            [
                'category'      => 'dif',
                'title'         => 'Procuraduría de Protección a Niñas, Niños y Familia',
                'responsible'   => 'Defensa legal de menores, mediación familiar y trabajo social',
                'phone'         => '7658449857',
                'phone_display' => '765 844 9857 (Procuraduría)',
                'location'      => 'Instalaciones del DIF Municipal',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-children'
            ],
            [
                'category'      => 'dif',
                'title'         => 'Área Médica y Farmacia Comunitaria DIF',
                'responsible'   => 'Consultas médicas básicas, odontología y medicamentos',
                'phone'         => '7658449441',
                'phone_display' => '765 844 9441 (Atención Médica)',
                'location'      => 'Clínica Comunitaria DIF',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-user-doctor'
            ],
            [
                'category'      => 'dif',
                'title'         => 'Centro de Rehabilitación e Inclusión (CRI)',
                'responsible'   => 'Terapias físicas, estimulación temprana y apoyo a discapacidad',
                'phone'         => '7658449090',
                'phone_display' => '765 844 9090 (Módulo CRI)',
                'location'      => 'Módulo CRI, Col. Heroica Veracruz',
                'hours'         => 'Lunes a Viernes 8:00 AM - 3:00 PM',
                'icon'          => 'fa-solid fa-wheelchair'
            ],
            [
                'category'      => 'dif',
                'title'         => 'Club del Adulto Mayor e INAPAM',
                'responsible'   => 'Credencialización INAPAM, apoyos alimentarios y talleres',
                'phone'         => '7658449857',
                'phone_display' => '765 844 9857 ext. Adulto Mayor',
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

        // Guardar evidencia fotográfica en carpeta images de Hostinger y sincronizar a public_html/images
        $evidenceUrl = null;
        if ($request->hasFile('evidence_initial')) {
            $file = $request->file('evidence_initial');
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'cit_' . $folio . '_' . time() . '.' . $extension;
            $destinationPath = public_path('images');

            if (!file_exists($destinationPath)) {
                @mkdir($destinationPath, 0775, true);
            }

            $file->move($destinationPath, $filename);
            $savedPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;
            $evidenceUrl = '/images/' . $filename;

            // Sincronizar automáticamente hacia la raíz public_html/images de Hostinger
            IncidentReport::syncImageToHostingerPublicHtml($filename, $savedPath);
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