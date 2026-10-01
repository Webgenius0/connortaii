<?php

namespace App\Services;

use App\Models\Inspection;
use App\Models\InspectionReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InspectionReportService
{
    public function generate(Inspection $inspection): InspectionReport
    {
        $inspection->load([
            'inspector',
            'healthSafetyResponses.item',
            'sections.checkpoints.photos',
        ]);

        $pdf = Pdf::loadView('reports.inspection', compact('inspection'))
            ->setPaper('a4', 'portrait');

        $fileName = 'inspection-report-' . $inspection->id . '-' . Str::random(6) . '.pdf';
        $filePath = 'reports/' . $fileName;

        Storage::disk('public')->put($filePath, $pdf->output());

        return InspectionReport::create([
            'inspection_id' => $inspection->id,
            'file_path'     => $filePath,
            'file_name'     => $fileName,
        ]);
    }
}
