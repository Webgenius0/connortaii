<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionHealthSafetyResponse;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HealthSafetyResponseController extends Controller
{
    use ApiResponse;

    // একটা item চেক/আনচেক করা অথবা concern রিপোর্ট করা
    public function update(Request $request, int $id)
    {
        $response = InspectionHealthSafetyResponse::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'is_checked'      => 'sometimes|boolean',
            'has_concern'     => 'sometimes|boolean',
            'concern_details' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $response->update($validator->validated());

        return $this->success($response, 'Health & Safety item updated', 200);
    }
}
