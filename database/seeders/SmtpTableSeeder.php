<?php

namespace Database\Seeders;

use App\Models\SmtpInformation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmtpTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SmtpInformation::updateOrCreate([
			'from_email' => 'bhartirani7902@gmail.com'
		],[
			'host' => 'smtp.gmail.com',
			'port' => 587,
			'username' => 'bhartirani7902@gmail.com',
			'from_name' => 'C2C',
			'password' => 'igkw ugce oyji yywf',
			'encryption' => 'tls',
			'status' => 1,
			'created_at' => date('Y-m-d H:i:s'),
		]);
    }
}
