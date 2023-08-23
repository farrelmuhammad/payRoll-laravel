<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Presence;
use Carbon\Carbon;

class PresenceController extends Controller
{
    public function clockIn()
    {
        $today = now();
        $currentDayOfWeek = $today->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

        if ($currentDayOfWeek >= 1 && $currentDayOfWeek <= 5) { // Monday to Friday
            Presence::create([
                'type' => 'in',
                'timestamp_in' => $today,
            ]);

            return response()->json(['message' => 'Clock in successful']);
        } else {
            Presence::create([
                'type' => 'out',
                'timestamp_in' => $today,
            ]);

            return response()->json(['message' => 'Clock out successful']);
        }
    }

    public function generatePaySlip(Request $request)
    {
        $month = $request->input('month');

        $basicSalary = 2000000;
        $performanceAllowance = 0;
        $latePenalty = 0;
        $onTimeRaw = date_format(date_create(Date('Y-m-d') . " " . "08:00:00"), 'Y-m-d H:i:s');
        $onTime = Carbon::parse($onTimeRaw);

        $attendanceData = Presence::where('type', 'in')
            ->whereYear('timestamp_in', '=', Carbon::parse($month)->year)
            ->whereMonth('timestamp_in', '=', Carbon::parse($month)->month)
            ->get();

        foreach ($attendanceData as $att) {
            $attendance = Carbon::parse($attendanceData[0]["timestamp_in"]);

            if ($attendance->diffInMinutes($onTime) >= 60) {
                $latePenalty += 0;
                $performanceAllowance += 0;
                break;
            } elseif ($attendance->diffInMinutes($onTime) >= 30) {
                $latePenalty += 10000;
                $performanceAllowance += 15000;
            } elseif ($attendance->diffInMinutes($onTime) >= 15) {
                $latePenalty += 5000;
                $performanceAllowance += 15000;
            } else {
                $latePenalty += 0;
                $performanceAllowance += 15000;
            }
        }

        $totalTakeHomePay = $basicSalary + $performanceAllowance - $latePenalty;

        $components = [
            ['name' => 'Gaji Pokok', 'amount' => $basicSalary],
            ['name' => 'Tunjangan Kinerja', 'amount' => $performanceAllowance],
            ['name' => 'Potongan Keterlambatan', 'amount' => -$latePenalty],
        ];

        $response = [
            'month' => $month,
            'components' => $components,
            'take_home_pay' => $totalTakeHomePay,
        ];

        return response()->json($response);
    }
}
