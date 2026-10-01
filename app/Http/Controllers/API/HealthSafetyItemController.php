<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthSafetyItem;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class HealthSafetyItemController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $items = HealthSafetyItem::orderBy('order')->get();

        return $this->success($items, 'Health & Safety items fetched successfully', 200);
    }

    public function show(int $id)
    {
        $item = HealthSafetyItem::findOrFail($id);

        return $this->success($item, 'Health & Safety item fetched successfully', 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_hazard'   => 'nullable|boolean',
            'order'       => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $key = Str::slug($request->label, '_');

        $originalKey = $key;
        $count = 1;
        while (HealthSafetyItem::where('key', $key)->exists()) {
            $key = $originalKey . '_' . $count++;
        }

        $item = HealthSafetyItem::create([
            'key'         => $key,
            'label'       => $request->label,
            'description' => $request->description,
            'is_hazard'   => $request->boolean('is_hazard'),
            'order'       => $request->order ?? (HealthSafetyItem::max('order') + 1),
        ]);

        return $this->success($item, 'Health & Safety item created successfully', 201);
    }

    public function update(Request $request, int $id)
    {
        $item = HealthSafetyItem::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'label'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_hazard'   => 'nullable|boolean',
            'order'       => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        $item->update([
            'label'       => $request->label ?? $item->label,
            'description' => $request->description ?? $item->description,
            'is_hazard'   => $request->has('is_hazard') ? $request->boolean('is_hazard') : $item->is_hazard,
            'order'       => $request->order ?? $item->order,
        ]);

        return $this->success($item, 'Health & Safety item updated successfully', 200);
    }

    public function destroy(int $id)
    {
        $item = HealthSafetyItem::findOrFail($id);
        $item->delete();

        return $this->success([], 'Health & Safety item deleted successfully', 200);
    }
}
