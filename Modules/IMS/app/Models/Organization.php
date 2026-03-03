<?php

namespace Modules\IMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\IMS\Database\Factories\OrganizationFactory;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tin_number',
        'VAT_reg_number',
        'VAT_reg_date',
        'email',
        'phone',
        'house_number',
        'trade_name',
        'legal_name',
        'woreda_id',
        'kebele_id',
        'locality_id',
        'tax_center_id',
        'sector_type',
    ];

    // protected static function newFactory(): OrganizationFactory
    // {
    //     // return OrganizationFactory::new();
    // }
}
