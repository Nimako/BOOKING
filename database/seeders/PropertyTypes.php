<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Seeder;

class PropertyTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PropertyTypes::CreatePropertyTypes();
    }

    public static function CreatePropertyTypes() {
        $propertyList = [
            ['name' => "Apartment", 'description' => "Furnished and self catering accommodation where guests went the entire place.", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Homes", 'description' => "Properties like apartments, holiday inns homes and villas.", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Hotel, B&B's, & More", 'description' => "Properties like Hotel's, B&B's, hostels guest houses, apartholes, etc.", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Alternative Places", 'description' => "Properties like boats, campsites, luxury tents, etc", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
        ];

        PropertyType::insert($propertyList);
    }
}
