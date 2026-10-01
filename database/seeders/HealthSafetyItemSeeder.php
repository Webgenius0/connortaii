<?php

namespace Database\Seeders;

use App\Models\HealthSafetyItem;
use Illuminate\Database\Seeder;

class HealthSafetyItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['key' => 'identifiable',        'label' => 'Identifiable',              'description' => 'I have my uniform and name badge on.',                 'is_hazard' => false, 'order' => 1],
            ['key' => 'phone_tablet',        'label' => 'Phone / Tablet',            'description' => 'My iPad and phone are working and charged.',           'is_hazard' => false, 'order' => 2],
            ['key' => 'tools',               'label' => 'Tools',                     'description' => 'My tools are available and in working condition.',     'is_hazard' => false, 'order' => 3],
            ['key' => 'ppe',                 'label' => 'PPE',                       'description' => 'Required PPE is available and ready to use.',          'is_hazard' => false, 'order' => 4],
            ['key' => 'property_occupants',  'label' => 'Property Occupants',        'description' => 'I am aware of the occupants and have considered their presence.', 'is_hazard' => false, 'order' => 5],
            ['key' => 'height',              'label' => 'Height',                    'description' => 'Required ladder or elevated access can be used safely.', 'is_hazard' => true,  'order' => 6],
            ['key' => 'electricity',         'label' => 'Electricity',               'description' => 'Potential electrical hazards have been considered.',   'is_hazard' => false, 'order' => 7],
            ['key' => 'slips_trips_falls',   'label' => 'Slips, Trips & Falls',      'description' => 'Potential slip, trip or fall hazards have been considered.', 'is_hazard' => false, 'order' => 8],
            ['key' => 'chemicals_hazardous', 'label' => 'Chemicals / Hazardous Substances', 'description' => 'Potential chemical or hazardous substance risks have been considered.', 'is_hazard' => false, 'order' => 9],
        ];

        foreach ($items as $item) {
            HealthSafetyItem::firstOrCreate(['key' => $item['key']], $item);
        }
    }
}
