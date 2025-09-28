<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';
    
    protected $primaryKey = 'id';
    
    public $timestamps = false; // since we use custom cdate/mdate
    
    protected $fillable = [
        'employee_name',
        'rating',
        'dept_id',
        'cby',
        'cip',
        'mby',
        'mdate',
        'mip',
        'cdate',
        'is_delete',
    ];
    
    // Relationship with Department
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'id');
    }
}
