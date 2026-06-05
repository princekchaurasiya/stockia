<?php

namespace Database\Seeders;

use App\Models\InformationLink;
use Illuminate\Database\Seeder;

class InformationLinkSeeder extends Seeder
{
    /**
     * Seed default global information tools links (visible to all users).
     */
    public function run(): void
    {
        $defaults = [
            ['title' => 'NSE India', 'url' => 'https://www.nseindia.com', 'sort_order' => 1],
            ['title' => 'BSE India', 'url' => 'https://www.bseindia.com', 'sort_order' => 2],
            ['title' => 'TradingView', 'url' => 'https://www.tradingview.com', 'sort_order' => 3],
            ['title' => 'Moneycontrol', 'url' => 'https://www.moneycontrol.com', 'sort_order' => 4],
            ['title' => 'Chartink', 'url' => 'https://chartink.com', 'sort_order' => 5],
            ['title' => 'Screener', 'url' => 'https://www.screener.in', 'sort_order' => 6],
            ['title' => 'Smartkarma', 'url' => 'https://www.smartkarma.com', 'sort_order' => 7],
            ['title' => 'Value Picker', 'url' => 'https://www.valuepicker.com', 'sort_order' => 8],
            ['title' => 'StockEdge', 'url' => 'https://web.stockedge.com', 'sort_order' => 9],
            ['title' => 'Opstra Options', 'url' => 'https://opstra.definedge.com', 'sort_order' => 10],
            ['title' => 'SgxNifty', 'url' => 'https://sgxnifty.org', 'sort_order' => 11],
            ['title' => 'Business Standard', 'url' => 'https://www.business-standard.com', 'sort_order' => 12],
            ['title' => 'iCharts', 'url' => 'https://icharts.in', 'sort_order' => 13],
            ['title' => 'TopStockResearch', 'url' => 'https://topstockresearch.com', 'sort_order' => 14],
            ['title' => 'IBEF', 'url' => 'https://www.ibef.org', 'sort_order' => 15],
            ['title' => 'Trading Economics', 'url' => 'https://tradingeconomics.com', 'sort_order' => 16],
            ['title' => 'Tijori Finance', 'url' => 'https://www.tijorifinance.com', 'sort_order' => 17],
            ['title' => 'Marketsmith India', 'url' => 'https://marketsmithindia.com', 'sort_order' => 18],
        ];

        foreach ($defaults as $item) {
            InformationLink::firstOrCreate(
                [
                    'title' => $item['title'],
                    'url' => $item['url'],
                    'account_id' => null,
                ],
                [
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                    'created_by' => null,
                ]
            );
        }
    }
}
