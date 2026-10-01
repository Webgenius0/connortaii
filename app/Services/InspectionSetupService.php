<?php

namespace App\Services;

use App\Models\HealthSafetyItem;
use App\Models\Inspection;
use App\Models\InspectionSection;

class InspectionSetupService
{
    public function initialize(Inspection $inspection): void
    {
        // Health & Safety checklist rows auto তৈরি
        foreach (HealthSafetyItem::all() as $item) {
            $inspection->healthSafetyResponses()->create([
                'health_safety_item_id' => $item->id,
            ]);
        }

        // সব section + checkpoint auto তৈরি
        foreach (config('inspection_structure') as $sectionType => $sectionData) {
            $section = $inspection->sections()->create([
                'section_type' => $sectionType,
            ]);

            $order = 1;
            foreach ($sectionData['checkpoints'] as $key => $label) {
                $section->checkpoints()->create([
                    'checkpoint_key' => $key,
                    'label'          => $label,
                    'order'          => $order++,
                ]);
            }
        }
    }
}
