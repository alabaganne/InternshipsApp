<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'website',
        'about',
        'city_id'
    ];

    public function company_supervisors() {
        return $this->hasMany(CompanySupervisor::class);
    }

    public function user() {
        return $this->morphOne(User::class, 'userable');
    }

    public function internships() {
        return $this->hasMany(Internship::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }
}
