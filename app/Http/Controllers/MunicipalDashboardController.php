<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MunicipalDashboardController extends Controller
{
    /**
     * Renderiza el Centro de Mando Municipal (Gobierno de Álamo Temapache)
     */
    public function index()
    {
        $reports = IncidentReport::orderBy('created_at', 'desc')->get();
        $stats = $this->calculateStats();

        return view('municipal.dashboard', compact('reports', 'stats'));
    }

    /**
     * Endpoint JSON para actualización automática en tiempo real (Polling)
     */
    public function poll()
    {
        $reports = IncidentReport::orderBy('created_at', 'desc')->get();
        $stats = $this->calculateStats();

        return response()->json([
            'reports'     => $reports,
            'stats'       => $stats,
            'server_time' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Actualiza el estatus del reporte y permite subir la Foto de Evidencia ("Después")
     */
    public function updateStatus(Request $request, $id)
    {
        $report = IncidentReport::findOrFail($id);

        $request->validate([
            'status'              => 'required|in:pendiente,en_proceso,resuelto,rechazado',
            'resolution_notes'    => 'nullable|string|max:1000',
            'evidence_resolution' => 'nullable|image|max:8192',
            'evidence_file'       => 'nullable|image|max:8192',
        ]);

        $report->status = $request->status;

        if ($request->filled('resolution_notes')) {
            $report->resolution_notes = $request->resolution_notes;
        }

        if ($request->status === 'resuelto' && !$report->resolved_at) {
            $report->resolved_at = Carbon::now();
        }

        // Acepta tanto evidence_resolution como evidence_file
        $evidenceFile = $request->file('evidence_resolution') ?? $request->file('evidence_file');

        if ($evidenceFile) {
            $extension = $evidenceFile->getClientOriginalExtension() ?: 'jpg';
            $filename = 'res_' . $report->folio . '_' . time() . '.' . $extension;
            $destinationPath = public_path('images');

            if (!file_exists($destinationPath)) {
                @mkdir($destinationPath, 0775, true);
            }

            $evidenceFile->move($destinationPath, $filename);
            $report->evidence_resolution = '/images/' . $filename;
        }

        $report->save();

        return response()->json([
            'success' => true,
            'message' => "Folio {$report->folio} actualizado correctamente.",
            'report'  => $report,
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
            'total'            => $total,
            'pendientes'       => $pendientes,
            'en_proceso'       => $enProceso,
            'resueltos'        => $resueltos,
            'criticos'         => $criticos,
            'votos_ciudadanos' => $votosCiudadanos,
            'efectividad'      => $total > 0 ? round(($resueltos / $total) * 100) : 0,
        ];
    }
}
