<?php

// app/Models/InspectionCheckpointPhoto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionCheckpointPhoto extends Model
{
    protected $guarded = [];

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(InspectionSectionCheckpoint::class, 'inspection_section_checkpoint_id');
    }
}
