<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLookup extends Model
{
    protected $fillable = [
        'email',
        'company_name',
        'kam_name',
    ];
}
