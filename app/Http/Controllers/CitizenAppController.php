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
            'evidence_initial'  => 'required|image|max:8192',
        ]);

        $routing = IncidentReport::resolveDepartmentAndPriority($request->category);
        $folio = IncidentReport::generateNextFolio();

        // Guardar evidencia fotográfica en public/uploads/evidences (compatible con Hostinger)
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