<?php

namespace Database\Seeders;

use App\Models\DataSourceLink;
use Illuminate\Database\Seeder;

class DataSourceLinkSeeder extends Seeder
{
    public function run(): void
    {
        $nifty50Url = 'https://nsearchives.nseindia.com/content/indices/ind_nifty50list.csv';

        DataSourceLink::firstOrCreate(
            ['slug' => 'nifty50'],
            [
                'name' => 'List of Nifty 50 stocks',
                'url' => $nifty50Url,
                'display_columns' => config('stockia.nifty50.display_columns'),
                'is_active' => true,
            ]
        );

        DataSourceLink::firstOrCreate(
            ['slug' => 'market_activity'],
            [
                'name' => 'Market Activity Report (NSE)',
                'url' => config('stockia.market_activity.nse_all_reports_url', 'https://www.nseindia.com/all-reports'),
                'display_columns' => ['index', 'previous_close', 'open', 'high', 'low', 'close', 'gain_loss', 'return'],
                'is_active' => true,
            ]
        );
    }
}
