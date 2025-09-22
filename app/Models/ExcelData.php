<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcelData extends Model
{
    protected $fillable = [
        'file_name',
        'sheet_name',
        'cell_reference',
        'cell_value',
        'file_path'
    ];
}
