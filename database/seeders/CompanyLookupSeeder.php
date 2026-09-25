<?php

namespace Database\Seeders;

use App\Models\CompanyLookup;
use Illuminate\Database\Seeder;

class CompanyLookupSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            'abc.com'                  => ['ABC', 'Sokha Chea'],
            'xyztrading.com'           => ['XYZ TRADING', 'Dara Sok'],
            'globalcorp.com'           => ['GLOBAL CORP', 'Rithy Vong'],
            'smartretail.com'          => ['SMART RETAIL', 'Chan Meas'],
            'sunriselogistics.com'     => ['SUNRISE LOGISTICS', 'Vanna Heng'],
            'pacificbank.com'          => ['PACIFIC BANK', 'Sophal Ly'],
            'mekongfoods.com'          => ['MEKONG FOODS', 'Mony Kong'],
            'angkortech.com'           => ['ANGKOR TECH', 'Bopha Ouk'],
            'khmerbuild.com'           => ['KHMER BUILD', 'Vuthy Sar'],
            'goldengate.com'           => ['GOLDEN GATE', 'Kunthea Sok'],
            'phnompenhretail.com'      => ['PHNOM PENH RETAIL', 'Sreyleak Pich'],
            'royalgarment.com'         => ['ROYAL GARMENT', 'Pisach Ratana'],
            'mekongconstruction.com'   => ['MEKONG CONSTRUCTION', 'Thida Vong'],
            'siemreaphotel.com'        => ['SIEM REAP HOTEL', 'Piseth Chan'],
            'battambangrice.com'       => ['BATTAMBANG RICE', 'Sreymom Heng'],
        ];

        $users = ['billing', 'finance', 'accounts', 'info', 'admin', 'contact'];

        foreach ($companies as $domain => [$companyName, $kamName]) {
            foreach ($users as $user) {
                CompanyLookup::updateOrCreate(
                    ['email' => "{$user}@{$domain}"],
                    ['company_name' => $companyName, 'kam_name' => $kamName]
                );
            }
        }
    }
}
