<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $companies = [
            [
                'company_name' => 'Social Security Jordan',
                'group_id' => 'govt-sector',
            ],
            [
                'company_name' => 'Arab Bank',
                'group_id' => 'private-sector',
            ],
            [
                'company_name' => 'Orange Jordan',
                'group_id' => 'private-sector',
            ],
            [
                'company_name' => 'Manaseer Group',
                'group_id' => 'private-sector',
            ],
            [
                'company_name' => 'Royal Jordanian',
                'group_id' => 'private-sector',
            ],
        ];

        foreach ($companies as $company) {
            Company::updateOrCreate(['company_name' => $company['company_name']], $company);
        }
    }
}
