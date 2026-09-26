<?php

// app/Models/InspectionHealthSafetyResponse.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionHealthSafetyResponse extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_checked'  => 'boolean',
        'has_concern' => 'boolean',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(HealthSafetyItem::class, 'health_safety_item_id');
    }
}
