<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    protected $fillable = [
        'cover_letter',
        'message',
        'attachments',
        'status',
        'company_id'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function internship() {
        return $this->belongsTo(Internship::class);
    }

    public function company() {
        return $this->belongsTo(Company::class);
    }
}
