<?php

// app/Models/Inspection.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inspection extends Model
{
    protected $guarded = [];

    protected $casts = [
        'inspection_date'         => 'date',
        'property_owner_presence' => 'boolean',
        'other_people_presence'   => 'boolean',
        'house_occupied'          => 'boolean',
        'property_furnished'      => 'boolean',
    ];

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function healthSafetyResponses(): HasMany
    {
        return $this->hasMany(InspectionHealthSafetyResponse::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(InspectionSection::class);
    }
    public function reports(): HasMany
    {
        return $this->hasMany(InspectionReport::class);
    }
}
