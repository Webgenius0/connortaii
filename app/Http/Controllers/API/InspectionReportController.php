<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\InspectionReport;
use App\Services\InspectionReportService;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InspectionReportController extends Controller
{
    use ApiResponse;

    public function __construct(private InspectionReportService $reportService)
    {
    }

    public function generate(int $inspectionId)
    {
        $inspection = Inspection::findOrFail($inspectionId);

        $report = $this->reportService->generate($inspection);

        return $this->success([
            'id'           => $report->id,
            'download_url' => route('reports.download', $report->id),
        ], 'Report generated successfully', 201);
    }

    public function index(int $inspectionId)
    {
        $reports = InspectionReport::where('inspection_id', $inspectionId)
            ->latest()
            ->get()
            ->map(fn ($report) => [
                'id'           => $report->id,
                'file_name'    => $report->file_name,
                'download_url' => route('reports.download', $report->id), 
                'created_at'   => $report->created_at,
            ]);

        return $this->success($reports, 'Reports fetched successfully', 200);
    }

    public function download(int $reportId)
    {
        $report = InspectionReport::findOrFail($reportId);

        if (!Storage::disk('public')->exists($report->file_path)) {
            return $this->error([], 'Report file not found', 404);
        }

        return Storage::disk('public')->download($report->file_path, $report->file_name);
    }
    // InspectionReportController এ যোগ করুন
    public function myReports()
    {
        $reports = InspectionReport::whereHas('inspection', function ($query) {
            $query->where('inspector_id', Auth::id());
        })
            ->with('inspection:id,property_name,property_address,inspection_date')
            ->latest()
            ->get()
            ->map(fn ($report) => [
            'id'              => $report->id,
            'file_name'       => $report->file_name,
            'download_url'    => route('reports.download', $report->id),
            'inspection_id'   => $report->inspection_id,
            'property_name'   => $report->inspection->property_name,
            'property_address' => $report->inspection->property_address,
            'inspection_date' => $report->inspection->inspection_date,
            'created_at'      => $report->created_at,
        ]);

        return $this->success($reports, 'Reports fetched successfully', 200);
    }
}
