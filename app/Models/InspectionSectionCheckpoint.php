<?php

// app/Models/InspectionSectionCheckpoint.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionSectionCheckpoint extends Model
{
    protected $guarded = [];

    public function section(): BelongsTo
    {
        return $this->belongsTo(InspectionSection::class, 'inspection_section_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InspectionCheckpointPhoto::class, 'checkpoint_id');
    }
}
