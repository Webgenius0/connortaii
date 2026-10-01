<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InspectionSectionCheckpoint;
use App\Services\AvatarUploadService; // অথবা আলাদা PhotoUploadService বানাতে পারেন
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InspectionCheckpointController extends Controller
{
    use ApiResponse;

    public function __construct(private AvatarUploadService $uploadService)
    {
    }

    public function update(Request $request, int $id)
    {
        $checkpoint = InspectionSectionCheckpoint::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type'              => 'nullable|string|max:255',
            'level_of_concern'  => 'nullable|in:green,orange,red,na',
            'observations'      => 'nullable|string',
            'photo'             => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $checkpoint->update($request->only('type', 'level_of_concern', 'observations'));

        if ($request->hasFile('photo')) {
            $path = $this->uploadService->upload($request->file('photo'));
            $checkpoint->photos()->create(['photo_path' => $path]);
        }

        return $this->success($checkpoint->load('photos'), 'Checkpoint updated successfully', 200);
    }
    public function show(int $id)
    {
        $checkpoint = InspectionSectionCheckpoint::with('photos')->findOrFail($id);

        return $this->success($checkpoint, 'Checkpoint fetched successfully', 200);
    }
    public function destroyPhoto(int $photoId)
    {
        $photo = \App\Models\InspectionCheckpointPhoto::findOrFail($photoId);
        $photo->delete();

        return $this->success([], 'Photo removed successfully', 200);
    }
}
