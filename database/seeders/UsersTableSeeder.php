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
        $admin = User::updateOrCreate(['email' => 'admin@gmail.com'],[
            'name'          => 'Admin', 
            'role_id'       => 1,
            'parent_id'     => null,
            'password'      => Hash::make('pass@admin'), 
            'status'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        UserDetail::updateOrCreate(['user_id' => $admin->id],[
            'phone_number'  =>  '1234567891'
        ]);
    }
}
