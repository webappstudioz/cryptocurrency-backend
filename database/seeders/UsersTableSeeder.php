<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::updateOrCreate(['email' => 'adminc2c@yopmail.com'],[
            'user_name'     => 'C2C1111',
            'first_name'    => 'Admin', 
            'last_name'     => 'Administrator', 
            'role_id'       => 1,
            'parent_id'     => null,
            'password'      => Hash::make('Admin@c2c'), 
            'security_key'  => 'Admin@c2c',
            'status'        => 1,
            'phone_number'  =>  '1234567891',
            'verified'      =>  1,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);  
    }
}
