<?php

namespace Database\Seeders;

use App\Models\InvoiceRejectionReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceRejectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        InvoiceRejectionReason::create(['reason' => 'Incorrect Account details.']);
        InvoiceRejectionReason::create(['reason' => 'Insufficent balance.']);
        InvoiceRejectionReason::create(['reason' => 'incorrect payment details.']);
        InvoiceRejectionReason::create(['reason' => 'wrong amount']);

    }
}
