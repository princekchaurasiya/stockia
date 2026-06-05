<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nifty50Extended extends Model
{
    protected $table = 'nifty50_extended';

    protected $fillable = [
        'security_symbol',
        'company_name',
        'industry',
        'nifty_weightage_pct',
        'sector_thematic_index',
        'sector_thematic_weightage_pct',
        'relationship_of_index',
        'sort_order',
    ];

    protected $casts = [
        'nifty_weightage_pct' => 'decimal:2',
        'sector_thematic_weightage_pct' => 'decimal:2',
    ];
}
