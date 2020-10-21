<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        FacilitiesSeeder::CreateFacilities();
    }

    public static function CreateFacilities() {
        $facilityList = [
            ['name' => "Bar", 'icon_class' => "", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Suana", 'icon_class' => "", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Garden", 'icon_class' => "", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Terrace", 'icon_class' => "", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Hot tub/Jacuzzi", 'icon_class' => "", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Heating", 'icon_class' => "", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Free Wifi", 'icon_class' => "", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Air Condition", 'icon_class' => "", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Free on-site Parking", 'icon_class' => "", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['name' => "Swimming Pool", 'icon_class' => "", 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
        ];

        Facility::insert($facilityList);
    }
}
