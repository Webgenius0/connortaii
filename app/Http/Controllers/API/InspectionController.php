<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Services\InspectionSetupService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InspectionController extends Controller
{
    use ApiResponse;

    public function __construct(private InspectionSetupService $setupService)
    {
    }

    // Step 1: Inspection details create
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inspector_name'             => 'required|string|max:255',
            'email'                      => 'nullable|email',
            'mobile_number'              => 'nullable|string|max:20',
            'property_address'           => 'nullable|string|max:255',
            'property_name'              => 'nullable|string|max:255',
            'inspection_date'            => 'nullable|date',
            'property_owner_name'        => 'nullable|string|max:255',
            'property_owner_presence'    => 'nullable|boolean',
            'other_people_presence'      => 'nullable|boolean',
            'house_occupied'             => 'nullable|boolean',
            'property_furnished'         => 'nullable|boolean',
            'building_type'              => 'nullable|string|max:255',
            'status_of_utilities'        => 'nullable|string|max:255',
            'weather_during_inspection'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $inspection = Inspection::create([
            ...$validator->validated(),
            'inspector_id' => Auth::id(),
            'status'       => 'in_progress',
            'current_step' => 1,
        ]);

        $this->setupService->initialize($inspection);

        return $this->success(
            $inspection->load('healthSafetyResponses.item', 'sections.checkpoints'),
            'Inspection created successfully',
            201
        );
    }

    public function show(int $id)
    {
        $inspection = Inspection::with([
            'healthSafetyResponses.item',
            'sections.checkpoints.photos',
        ])->findOrFail($id);

        return $this->success($inspection, 'Inspection fetched successfully', 200);
    }

    public function index()
    {
        $inspections = Inspection::where('inspector_id', Auth::id())->latest()->get();

        return $this->success($inspections, 'Inspections fetched successfully', 200);
    }

    // Inspection stop করা (ছবিতে "Stop Inspection" বাটন)
    public function stop(int $id)
    {
        $inspection = Inspection::findOrFail($id);
        $inspection->update(['status' => 'stopped']);

        return $this->success($inspection, 'Inspection stopped', 200);
    }

    public function complete(int $id)
    {
        $inspection = Inspection::findOrFail($id);
        $inspection->update(['status' => 'completed']);

        return $this->success($inspection, 'Inspection completed', 200);
    }
}
