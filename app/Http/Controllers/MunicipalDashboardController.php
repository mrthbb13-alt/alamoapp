<?php

namespace App\Http\Controllers;

use App\Models\IncidentReport;
use Illuminate\Http\Request;

class MunicipalDashboardController extends Controller
{
    /**
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
        ]);
    }

    /**
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
     */
    public function updateStatus(Request $request, $id)
    {
        $report = IncidentReport::findOrFail($id);

        $request->validate([
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
            $filename = 'res_' . $report->folio . '_' . time() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('uploads/evidences');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $report->evidence_resolution = asset('uploads/evidences/' . $filename);
        }

        if ($request->status === 'resuelto' && !$report->resolved_at) {
            $report->resolved_at = now();
        }

        $report->save();

        return response()->json([
            'success' => true,
            'message' => 'Reporte actualizado exitosamente.',
            'report'  => $report,
        ]);
    }
}

