<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use Illuminate\Http\Request;
<<<<<<< HEAD
=======
use Carbon\Carbon;
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c

class MunicipalDashboardController extends Controller
{
    /**
<<<<<<< HEAD
     * Rama 1: Vista principal del Panel Municipal (Centro de Mando)
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query = IncidentReport::query()->orderBy('created_at', 'desc');

        if ($status && in_array($status, ['pendiente', 'en_proceso', 'resuelto', 'rechazado'])) {
            $query->where('status', $status);
        }

        $reports = $query->get();
        $metrics = [
            'total'      => IncidentReport::count(),
            'pendientes' => IncidentReport::where('status', 'pendiente')->count(),
            'en_proceso' => IncidentReport::where('status', 'en_proceso')->count(),
            'resueltos'  => IncidentReport::where('status', 'resuelto')->count(),
            'rechazados' => IncidentReport::where('status', 'rechazado')->count(),
        ];

        if (view()->exists('municipal.dashboard')) {
            return view('municipal.dashboard', compact('reports', 'metrics', 'status'));
        }

        return response()->json([
            'status'  => 'ok',
            'message' => 'Panel Municipal Activo',
            'metrics' => $metrics,
            'reports' => $reports,
=======
     * Renderiza el Centro de Mando Municipal (Gobierno de Álamo Temapache)
     */
    public function index()
    {
        $reports = IncidentReport::orderBy('created_at', 'desc')->get();
        $stats = $this->calculateStats();

        return view('municipal.dashboard', compact('reports', 'stats'));
    }

    /**
     * Endpoint JSON para actualización automática en tiempo real (Polling cada 4 seg)
     */
    public function poll()
    {
        $reports = IncidentReport::orderBy('created_at', 'desc')->get();
        $stats = $this->calculateStats();

        return response()->json([
            'reports' => $reports,
            'stats' => $stats,
            'server_time' => now()->format('H:i:s'),
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c
        ]);
    }

    /**
<<<<<<< HEAD
     * Sondeo en vivo para el Panel Municipal
     */
    public function poll(Request $request)
    {
        $since = $request->query('since');
        $query = IncidentReport::query()->orderBy('created_at', 'desc');

        if ($since) {
            $query->where('updated_at', '>', $since);
        }

        $reports = $query->take(50)->get();

        return response()->json([
            'timestamp' => now()->toIso8601String(),
            'count'     => $reports->count(),
            'reports'   => $reports,
        ]);
    }

    /**
     * Actualiza el estatus y agrega evidencia de resolución
=======
     * Actualiza el estatus del reporte y permite subir la Foto de Evidencia ("Después")
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c
     */
    public function updateStatus(Request $request, $id)
    {
        $report = IncidentReport::findOrFail($id);

        $request->validate([
<<<<<<< HEAD
            'status'           => 'required|in:pendiente,en_proceso,resuelto,rechazado',
            'resolution_notes' => 'nullable|string|max:1000',
            'evidence_file'    => 'nullable|image|max:8192',
        ]);

        $report->status = $request->status;

        if ($request->filled('resolution_notes')) {
            $report->resolution_notes = $request->resolution_notes;
        }

        if ($request->hasFile('evidence_file')) {
            $file = $request->file('evidence_file');
=======
            'status' => 'required|in:pendiente,en_proceso,resuelto,rechazado',
            'resolution_notes' => 'nullable|string|max:1000',
            'evidence_resolution' => 'nullable|image|max:8192', // Hasta 8MB
        ]);

        $report->status = $request->status;
        $report->resolution_notes = $request->resolution_notes;

        if ($request->status === 'resuelto' && !$report->resolved_at) {
            $report->resolved_at = Carbon::now();
        }

        // Guardado directo en public/uploads/evidences (100% compatible con Hostinger sin problemas de symlink)
        if ($request->hasFile('evidence_resolution')) {
            $file = $request->file('evidence_resolution');
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c
            $filename = 'res_' . $report->folio . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/evidences');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $report->evidence_resolution = asset('uploads/evidences/' . $filename);
        }

<<<<<<< HEAD
        if ($request->status === 'resuelto' && !$report->resolved_at) {
            $report->resolved_at = now();
        }

=======
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c
        $report->save();

        return response()->json([
            'success' => true,
<<<<<<< HEAD
            'message' => 'Reporte actualizado exitosamente.',
            'report'  => $report,
        ]);
    }
}

=======
            'message' => "Folio {$report->folio} actualizado correctamente.",
            'report' => $report,
        ]);
    }

    /**
     * Calcula los indicadores ejecutivos (KPIs)
     */
    private function calculateStats(): array
    {
        $total = IncidentReport::count();
        $pendientes = IncidentReport::where('status', 'pendiente')->count();
        $enProceso = IncidentReport::where('status', 'en_proceso')->count();
        $resueltos = IncidentReport::where('status', 'resuelto')->count();
        $criticos = IncidentReport::where('priority', 'critica')->where('status', '!=', 'resuelto')->count();
        $votosCiudadanos = IncidentReport::sum('upvotes');

        return [
            'total' => $total,
            'pendientes' => $pendientes,
            'en_proceso' => $enProceso,
            'resueltos' => $resueltos,
            'criticos' => $criticos,
            'votos_ciudadanos' => $votosCiudadanos,
            'efectividad' => $total > 0 ? round(($resueltos / $total) * 100) : 0,
        ];
    }
}
>>>>>>> 07f4414d6cc612454c057473736c3da37896928c
