<?php

namespace Database\Seeders;

use App\Models\Nifty50Extended;
use Illuminate\Database\Seeder;

class Nifty50ExtendedSeeder extends Seeder
{
    public function run(): void
    {
        $industryToSector = [
            'Financial Services' => 'Financial Services',
            'Information Technology' => 'IT',
            'Oil Gas & Consumable Fuels' => 'Oil, Gas & Energy',
            'Automobile and Auto Components' => 'Auto',
            'Fast Moving Consumer Goods' => 'FMCG',
            'Telecommunication' => 'Telecom',
            'Construction' => 'Construction',
            'Metals & Mining' => 'Metals & Mining',
            'Healthcare' => 'Pharma',
            'Power' => 'Power',
            'Consumer Durables' => 'Consumer Durables',
            'Services' => 'Services',
            'Capital Goods' => 'Capital Goods',
            'Construction Materials' => 'Construction Materials',
            'Consumer Services' => 'Consumer Services',
        ];

        $sectorWeightage = [
            'Financial Services' => 37.06, 'IT' => 10.83, 'Oil, Gas & Energy' => 9.93,
            'Auto' => 6.71, 'FMCG' => 6.01, 'Telecom' => 4.75, 'Construction' => 4.00,
            'Metals & Mining' => 3.50, 'Pharma' => 3.50, 'Power' => 2.50,
            'Consumer Durables' => 2.00, 'Services' => 2.00, 'Capital Goods' => 1.50,
            'Construction Materials' => 2.00, 'Consumer Services' => 1.50,
        ];

        $stockWeightage = [
            'HDFCBANK' => 12.30, 'ICICIBANK' => 8.38, 'RELIANCE' => 8.16,
            'INFY' => 4.98, 'BHARTIARTL' => 4.75, 'LT' => 4.00, 'SBIN' => 3.87,
            'AXISBANK' => 3.40, 'TCS' => 2.76, 'ITC' => 2.69, 'KOTAKBANK' => 2.50,
            'HINDUNILVR' => 2.20, 'BAJFINANCE' => 2.00, 'ASIANPAINT' => 1.80,
            'MARUTI' => 1.70, 'ULTRACEMCO' => 1.60, 'SUNPHARMA' => 1.50,
            'TITAN' => 1.40, 'NESTLEIND' => 1.30, 'WIPRO' => 1.20, 'ONGC' => 1.15,
            'TATASTEEL' => 1.10, 'POWERGRID' => 1.05, 'NTPC' => 1.00, 'COALINDIA' => 0.95,
            'TECHM' => 0.90, 'HCLTECH' => 0.85, 'BAJAJFINSV' => 0.80, 'DRREDDY' => 0.75,
            'ADANIENT' => 0.70, 'M&M' => 0.65, 'ADANIPORTS' => 0.60, 'INDIGO' => 0.55,
            'CIPLA' => 0.50, 'GRASIM' => 0.48, 'APOLLOHOSP' => 0.45, 'JSWSTEEL' => 0.42,
            'HINDALCO' => 0.40, 'SBILIFE' => 0.38, 'HDFCLIFE' => 0.35, 'BEL' => 0.32,
            'EICHERMOT' => 0.30, 'BAJAJ-AUTO' => 0.28, 'TATACONSUM' => 0.26,
            'TRENT' => 0.24, 'MAXHEALTH' => 0.22, 'SHRIRAMFIN' => 0.20, 'JIOFIN' => 0.18,
            'TMPV' => 0.16, 'KWIL' => 0.14, 'ETERNAL' => 0.12,
        ];

        $baseData = [
            ['ADANIENT', 'Adani Enterprises Ltd.', 'Metals & Mining'],
            ['ADANIPORTS', 'Adani Ports and Special Economic Zone Ltd.', 'Services'],
            ['APOLLOHOSP', 'Apollo Hospitals Enterprise Ltd.', 'Healthcare'],
            ['ASIANPAINT', 'Asian Paints Ltd.', 'Consumer Durables'],
            ['AXISBANK', 'Axis Bank Ltd.', 'Financial Services'],
            ['BAJAJ-AUTO', 'Bajaj Auto Ltd.', 'Automobile and Auto Components'],
            ['BAJFINANCE', 'Bajaj Finance Ltd.', 'Financial Services'],
            ['BAJAJFINSV', 'Bajaj Finserv Ltd.', 'Financial Services'],
            ['BEL', 'Bharat Electronics Ltd.', 'Capital Goods'],
            ['BHARTIARTL', 'Bharti Airtel Ltd.', 'Telecommunication'],
            ['CIPLA', 'Cipla Ltd.', 'Healthcare'],
            ['COALINDIA', 'Coal India Ltd.', 'Oil Gas & Consumable Fuels'],
            ['DRREDDY', "Dr. Reddy's Laboratories Ltd.", 'Healthcare'],
            ['EICHERMOT', 'Eicher Motors Ltd.', 'Automobile and Auto Components'],
            ['ETERNAL', 'Eternal Ltd.', 'Consumer Services'],
            ['GRASIM', 'Grasim Industries Ltd.', 'Construction Materials'],
            ['HCLTECH', 'HCL Technologies Ltd.', 'Information Technology'],
            ['HDFCBANK', 'HDFC Bank Ltd.', 'Financial Services'],
            ['HDFCLIFE', 'HDFC Life Insurance Company Ltd.', 'Financial Services'],
            ['HINDALCO', 'Hindalco Industries Ltd.', 'Metals & Mining'],
            ['HINDUNILVR', 'Hindustan Unilever Ltd.', 'Fast Moving Consumer Goods'],
            ['ICICIBANK', 'ICICI Bank Ltd.', 'Financial Services'],
            ['ITC', 'ITC Ltd.', 'Fast Moving Consumer Goods'],
            ['INFY', 'Infosys Ltd.', 'Information Technology'],
            ['INDIGO', 'InterGlobe Aviation Ltd.', 'Services'],
            ['JSWSTEEL', 'JSW Steel Ltd.', 'Metals & Mining'],
            ['JIOFIN', 'Jio Financial Services Ltd.', 'Financial Services'],
            ['KOTAKBANK', 'Kotak Mahindra Bank Ltd.', 'Financial Services'],
            ['KWIL', "Kwality Wall's (India) Ltd.", 'Fast Moving Consumer Goods'],
            ['LT', 'Larsen & Toubro Ltd.', 'Construction'],
            ['M&M', 'Mahindra & Mahindra Ltd.', 'Automobile and Auto Components'],
            ['MARUTI', 'Maruti Suzuki India Ltd.', 'Automobile and Auto Components'],
            ['MAXHEALTH', 'Max Healthcare Institute Ltd.', 'Healthcare'],
            ['NTPC', 'NTPC Ltd.', 'Power'],
            ['NESTLEIND', 'Nestle India Ltd.', 'Fast Moving Consumer Goods'],
            ['ONGC', 'Oil & Natural Gas Corporation Ltd.', 'Oil Gas & Consumable Fuels'],
            ['POWERGRID', 'Power Grid Corporation of India Ltd.', 'Power'],
            ['RELIANCE', 'Reliance Industries Ltd.', 'Oil Gas & Consumable Fuels'],
            ['SBILIFE', 'SBI Life Insurance Company Ltd.', 'Financial Services'],
            ['SHRIRAMFIN', 'Shriram Finance Ltd.', 'Financial Services'],
            ['SBIN', 'State Bank of India', 'Financial Services'],
            ['SUNPHARMA', 'Sun Pharmaceutical Industries Ltd.', 'Healthcare'],
            ['TCS', 'Tata Consultancy Services Ltd.', 'Information Technology'],
            ['TATACONSUM', 'Tata Consumer Products Ltd.', 'Fast Moving Consumer Goods'],
            ['TMPV', 'Tata Motors Passenger Vehicles Ltd.', 'Automobile and Auto Components'],
            ['TATASTEEL', 'Tata Steel Ltd.', 'Metals & Mining'],
            ['TECHM', 'Tech Mahindra Ltd.', 'Information Technology'],
            ['TITAN', 'Titan Company Ltd.', 'Consumer Durables'],
            ['TRENT', 'Trent Ltd.', 'Consumer Services'],
            ['ULTRACEMCO', 'UltraTech Cement Ltd.', 'Construction Materials'],
            ['WIPRO', 'Wipro Ltd.', 'Information Technology'],
        ];

        Nifty50Extended::query()->delete();

        foreach ($baseData as $i => $row) {
            [$symbol, $companyName, $industry] = $row;
            $sector = $industryToSector[$industry] ?? $industry;
            $niftyWt = $stockWeightage[$symbol] ?? (2.0 / count($baseData));
            $sectorWt = $sectorWeightage[$sector] ?? 2.0;

            Nifty50Extended::create([
                'security_symbol' => $symbol,
                'company_name' => $companyName,
                'industry' => $industry,
                'nifty_weightage_pct' => round($niftyWt, 2),
                'sector_thematic_index' => $sector,
                'sector_thematic_weightage_pct' => round($sectorWt, 2),
                'relationship_of_index' => 'Sector',
                'sort_order' => $i + 1,
            ]);
        }
    }
}
