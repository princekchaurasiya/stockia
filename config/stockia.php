<?php

return [
    /*
    | Columns to exclude from display, table, charts, and export.
    | Matches case-insensitively (e.g. "Series", "ISIN Code", "isin_code").
    */
    'excluded_columns' => ['series', 'isin_code', 'isin code'],

    /*
    | Display names for columns (used in table header and Excel export).
    | Keys are lowercase/snake_case; values are proper labels.
    */
    'column_display_names' => [
        'symbol' => 'Security Symbol',
        'security_symbol' => 'Security Symbol',
        'company_name' => 'Company Name',
        'industry' => 'Industry',
        'nifty_weightage' => 'Nifty Weightage (%)',
        'sector_thematic_index' => 'Sector & Thematic Index',
        'sector_thematic_weightage' => 'Sector & Thematic Weightage (%)',
        'relationship_of_index' => 'Relationship of Index (Sector/Thematic)',
        'index' => 'INDEX',
        'previous_close' => 'Previous Close',
        'open' => 'OPEN',
        'high' => 'HIGH',
        'low' => 'LOW',
        'close' => 'CLOSE',
        'gain_loss' => 'Gain/Loss',
        'return' => 'Return (%)',
    ],

    'nifty50_extended' => [
        'data_as_of' => '2025-01-30',
        'source' => 'NSE India (ind_nifty50list.csv) + approximate weightages from NSE indices',
    ],

    'nifty50' => [
        'slug' => 'nifty50',
        'display_columns' => ['company_name', 'industry', 'symbol'],
        'export_headers' => [
            'company_name' => 'Company Name',
            'industry' => 'Industry',
            'symbol' => 'Security Symbol',
        ],
    ],

    'market_activity' => [
        'slug' => 'market_activity',
        'nse_all_reports_url' => 'https://www.nseindia.com/all-reports',
        'excluded_indices' => [
            'India VIX',
            'Nifty50 PR 2x Lev',
            'Nifty50 PR 1x Inv',
            'Nifty50 TR 2x Lev',
            'Nifty50 TR 1x Inv',
        ],
        'section_boundaries' => [
            'advances',
            'declines',
            'unchanged',
            'total securities that have hit their price bands',
            'top 25 securities today',
            'top five nifty 50 gainers',
            'top five nifty 50 losers',
            'securities price volume data in normal market',
        ],
        'index_column' => 'index',
        'previous_close_column' => 'previous_close',
        'close_column' => 'close',
    ],
];
