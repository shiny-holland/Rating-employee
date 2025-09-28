<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department'; // table name (not pluralized)

    protected $primaryKey = 'id';

    public $timestamps = false; // since we use custom cdate/mdate

    protected $fillable = [
        'dept_name',
        'cby',
        'cip',
        'mby',
        'mdate',
        'mip',
        'cdate',
        'is_delete',
    ];
}
