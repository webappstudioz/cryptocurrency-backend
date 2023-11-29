<?php

namespace Database\Seeders;

use App\Models\TimeZone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Ramsey\Uuid\Type\Time;

class TimeZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TimeZone::updateOrCreate([
            'time_zone'      => '11:00 am morning',
        ],[
            'user_id'       => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),  
        ]);
        TimeZone::updateOrCreate([
            'time_zone'     => '3:00 pm afternoon',
        ],[
            'user_id'       => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),  
        ]);
        TimeZone::updateOrCreate([
            'time_zone'     => '6:00 pm evening',
        ],[
            'user_id'       => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),  
        ]);
        TimeZone::updateOrCreate([
            'time_zone'     => '9:00 pm night',
        ],[
            'user_id'       => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),  
        ]);
    }
}
