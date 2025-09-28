<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentRating extends Model
{
    protected $table = 'department_rating';
    
    protected $primaryKey = 'id';
    
    public $timestamps = false; // since we use custom cdate/mdate
    
    protected $fillable = [
        'dept_id',
        'department_head_name',
        'average_rating',
        'rating_date',
        'cby',
        'cip',
        'mby',
        'mdate',
        'mip',
        'cdate',
        'is_delete',
    ];
    
    protected $casts = [
        'rating_date' => 'datetime',
        'average_rating' => 'decimal:2',
    ];
    
    // Relationship with Department
    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'id');
    }
}
