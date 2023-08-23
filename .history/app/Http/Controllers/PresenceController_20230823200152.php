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
            // Check if there's already a clock-in record for today
            $existingClockIn = Presence::where('type', 'in')
                ->whereDate('timestamp_in', $today->toDateString())
                ->first();

            if (!$existingClockIn) {
                // Check if there's already a clock-out record for today
                $existingClockOut = Presence::where('type', 'out')
                    ->whereDate('timestamp_in', $today->toDateString())
                    ->first();

                if (!$existingClockOut) {
                    Presence::create([
                        'type' => 'in',
                        'timestamp_in' => $today,
                    ]);

                    return response()->json(['message' => 'Clock in successful']);
                } else {
                    return response()->json(['message' => 'Clock out already performed for today']);
                }
            } else {
                // return response()->json(['message' => 'Clock in already performed for today']);
                Presence::create([
                    'type' => 'out',
                    'timestamp_in' => $today,
                ]);

                return response()->json(['message' => 'Clock out successful']);
            }
        } else {
            return response()->json(['message' => 'Clock in/out only allowed on weekdays']);
        }
    }

    public function generatePaySlip(Request $request)
    {
        $month = $request->input('month'); // Assuming you've retrieved the month from the request

        // Assuming the basic salary is fixed at 2,000,000
        $basicSalary = 2000000;
        // $onTime = Carbon::parse(Date('Y-m-d'). " " . "08:00:00");
        $onTime = date_format(date_create(Date('Y-m-d') . " " . "08:00:00"), 'Y-m-d H:i:s');

        // Example attendance data (replace with your actual data)
        $attendanceData = Presence::where('type', 'in')
            ->whereYear('timestamp_in', '=', Carbon::parse($month)->year)
            ->whereMonth('timestamp_in', '=', Carbon::parse($month)->month)
            ->get();

        if ()

        return response()->json($attendanceData);

        $performanceAllowance = 0;
        $latePenalty = 0;

        foreach ($attendanceData as $attendance) {
            $clockInTime = Carbon::parse($attendance['timestamp_in'] . ' ' . $attendance['clock_in']);
            $clockOutTime = Carbon::parse($attendance['timestamp_in'] . ' ' . $attendance['clock_out']);

            // Check if the employee was present for the full day (clock out performed)
            if ($clockOutTime->isAfter($clockInTime)) {
                // Calculate late arrival in minutes
                $lateArrivalMinutes = $clockInTime->diffInMinutes($clockOutTime->copy()->setTime(8, 0));

                if ($lateArrivalMinutes >= 60) {

                    $performanceAllowance = 0;
                    $latePenalty = 0;
                    break; // No need to continue checking other days
                } elseif ($lateArrivalMinutes >= 30) {

                    $latePenalty += 10000;
                } elseif ($lateArrivalMinutes >= 15) {
                    $latePenalty += 5000;
                }

                // Apply performance allowance for the day
                $performanceAllowance += 15000;
            }
        }

        // Calculate total take-home pay
        $totalTakeHomePay = $basicSalary + $performanceAllowance + $latePenalty;

        // Define components for the payslip
        $components = [
            ['name' => 'Gaji Pokok', 'amount' => $basicSalary],
            ['name' => 'Tunjangan Kinerja', 'amount' => $performanceAllowance],
            ['name' => 'Potongan Keterlambatan', 'amount' => -$latePenalty],
        ];

        // Construct the response
        $response = [
            'month' => $month,
            'components' => $components,
            'take_home_pay' => $totalTakeHomePay,
        ];

        return response()->json($response);
    }
}
