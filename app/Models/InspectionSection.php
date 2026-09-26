<?php

// app/Models/InspectionSection.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionSection extends Model
{
    protected $guarded = [];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class);
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(InspectionSectionCheckpoint::class)->orderBy('order');
    }
}
