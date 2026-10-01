<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionSection;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InspectionSectionController extends Controller
{
    use ApiResponse;

    public function show(int $id)
    {
        $section = InspectionSection::with('checkpoints.photos')->findOrFail($id);

        return $this->success($section, 'Section fetched successfully', 200);
    }

    // "Section Accessibility" — Yes, Accessible / No, Restricted
    public function updateAccessibility(Request $request, int $id)
    {
        $section = InspectionSection::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'accessibility_status'    => 'required|in:accessible,restricted',
            'restriction_reason'      => 'required_if:accessibility_status,restricted|in:limited_access,safety_concern,other',
            'restriction_description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $section->update($validator->validated());

        return $this->success($section, 'Section accessibility updated', 200);
    }
    // InspectionSectionController এ যোগ করুন
    public function indexByInspection(int $inspectionId)
    {
        $sections = InspectionSection::where('inspection_id', $inspectionId)
            ->with('checkpoints.photos')
            ->get();

        return $this->success($sections, 'Sections fetched successfully', 200);
    }
    // InspectionCheckpointController এ যোগ করুন

}
