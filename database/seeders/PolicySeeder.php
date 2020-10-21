<?php

namespace Database\Seeders;

use App\Models\Policy;
use App\Models\SubPolicy;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        PolicySeeder::CreatePolicy();
    }

    public static function CreatePolicy()
    {
       $policy_id = 0;
       $policyList = [
          'House Rules' => [
             ['name' => "Smoking Allowed", 'options' => "Yes / No"],
             ['name' => "Pets Allowed", 'options' => "Yes / No"],
             ['name' => "Children Allowed", 'options' => "Yes / No"],
             ['name' => "Parties/Event Allowed", 'options' => "Yes / No"],
             ['name' => "Check-in", 'options' => ""],
             ['name' => "Check-out", 'options' => ""],
          ],
          "Cancellation Policy" => [
             ['name' => "Days Before Cancel", 'options' => "1 Day/ 2 Days/ 5 Days"]
          ],
       ];

       foreach ($policyList as $key=>$value) {
          if(is_string($key)) {
             $newPolicy = new Policy();
             $newPolicy->name = $key;
             $newPolicy->save();
             // looping through details
             $subPolicyDetails = array_map(function($param) use ($newPolicy){
                return $temp = [
                   'name' => $param['name'],
                   'options' => $param['options'],
                   'policy_id' => $newPolicy->id
                ];
             }, $value);
             SubPolicy::insert($subPolicyDetails);
          }
       }
    }
}
