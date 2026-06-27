<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'job_title',
        'status',
        'applied_date',
        'job_url',
        'notes',
        'salary_expectation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
