<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

class AmenitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
       AmenitiesSeeder::CreateAmenities();
    }

   public static function CreateAmenities()
   {
      $amenityList = [
         ['name' => 'Air Condition', 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
         ['name' => 'Kitchenette', 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
         ['name' => 'Kitchen', 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
         ['name' => 'Balcony', 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
         ['name' => 'View', 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
         ['name' => 'Flat-Scrren Tv', 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
         ['name' => 'Private Pool', 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
         ['name' => 'Terrace', 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
         ['name' => 'Washing-Machine', 'created_by' => 0, 'created_at' => date('Y-m-d H:i:s')],
      ];

      Amenity::insert($amenityList);
    }
}
