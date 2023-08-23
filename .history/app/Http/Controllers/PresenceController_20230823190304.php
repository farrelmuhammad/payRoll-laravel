<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Presence;

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
                return response()->json(['message' => 'Clock in already performed for today']);
            }
        } else {
            return response()->json(['message' => 'Clock in/out only allowed on weekdays']);
        }
    }


    public function clockOut(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|integer',
        ]);

        $lastPresence = Presence::where('employee_id', $validatedData['employee_id'])
            ->where('type', 'in')
            ->orderByDesc('timestamp_in')
            ->first();

        if (!$lastPresence) {
            return response()->json(['message' => 'No clock in record found'], 400);
        }

        $lastPresence->update([
            'type' => 'out',
            'timestamp_out' => now(),
        ]);

        return response()->json(['message' => 'Clock out successful']);
    }

    public function generatePaySlip(Request $request)
    {
        // $validatedData = $request->validate([
        //     // 'employee_id' => 'required|integer',
        //     'month' => 'required|date_format:Y-m',
        // ]);

        // // Calculate performance allowance and late penalty based on presences
        // $presences = Presence::where('employee_id', $validatedData['employee_id'])
        //     ->whereYear('timestamp_in', '=', substr($validatedData['month'], 0, 4))
        //     ->whereMonth('timestamp_in', '=', substr($validatedData['month'], 5, 2))
        //     ->get();

        $performanceAllowance = 0;
        $latePenalty = 0;

        // foreach ($presences as $presence) {
        //     // Calculate performance allowance and late penalty for each presence
        //     // Add to $performanceAllowance and $latePenalty accordingly
        // }

        // Calculate total take-home pay
        $totalTakeHomePay = 2000000 + $performanceAllowance - $latePenalty;

        // $paySlip = [
        //     'month' => $validatedData['month'],
        //     'components' => [
        //         ['name' => 'Gaji Pokok', 'amount' => 2000000],
        //         ['name' => 'Tunjangan Kinerja', 'amount' => $performanceAllowance],
        //         ['name' => 'Potongan Keterlambatan', 'amount' => -$latePenalty],
        //     ],
        //     'take_home_pay' => $totalTakeHomePay,
        // ];
        // dd($paySlip);
        // return response()->json(['message' => 'Clock out successful']);

        return response()->json(['data' => $totalTakeHomePay]);
        // return response()->json("tes");
    }
}
