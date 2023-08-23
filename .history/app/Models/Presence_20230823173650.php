<?php

// app/Models/Presence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    protected $fillable = ['employee_id', 'type', 'timestamp_in', 'timestamp_out'];

    public function calculateLatePenalty()
    {
        // Hitung potongan keterlambatan
        $latePenalty = 0;

        if ($this->type === 'in') {
            $schedule = $this->timestamp_in->format('H:i:s');
            $expectedSchedule = '08:00:00';

            $lateMinutes = (strtotime($schedule) - strtotime($expectedSchedule)) / 60;

            if ($lateMinutes >= 60 || $this->timestamp_out === null) {
                $latePenalty = 0; // Tidak ada potongan jika terlambat >= 1 jam atau tidak ada clock-out
            } elseif ($lateMinutes >= 30) {
                $latePenalty = 10000; // Potongan 10.000 jika terlambat >= 30 menit
            } elseif ($lateMinutes >= 15) {
                $latePenalty = 5000; // Potongan 5.000 jika terlambat >= 15 menit
            }
        }

        return $latePenalty;
    }

    public function calculatePerformanceAllowance()
    {
        // Hitung tunjangan kinerja
        $performanceAllowance = 0;

        if ($this->type === 'out' && $this->timestamp_out !== null) {
            $schedule = $this->timestamp_in->format('H:i:s');
            $actualClockOut = $this->timestamp_out->format('H:i:s');
            $expectedClockOut = '16:00:00';

            if ($actualClockOut <= $expectedClockOut) {
                $performanceAllowance = 15000; // Tunjangan 15.000 jika clock-out sesuai jadwal
            }
        }

        return $performanceAllowance;
    }
}
