<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    // use HasFactory;
    protected $fillable = ['employee_id', 'type', 'timestamp_in', 'timestamp_out'];

    public function calculateLatePenalty()
    {
        // Implementasi perhitungan potongan keterlambatan
    }

    public function calculatePerformanceAllowance()
    {
        // Implementasi perhitungan tunjangan kinerja
    }
}
