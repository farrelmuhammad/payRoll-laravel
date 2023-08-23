<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;

class PembayaranController extends Controller
{
    public function clockIn(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|integer',
            // You can add more validation rules if needed
        ]);

        $presence = Presence::create([
            'employee_id' => $validatedData['employee_id'],
            'type' => 'in',
            'timestamp_in' => now(),
        ]);

        return response()->json(['message' => 'Clock in successful']);
    }

    public function clockOut(Request $request)
    {
        $validatedData = $request->validate([
            'employee_id' => 'required|integer',
            // You can add more validation rules if needed
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
        $validatedData = $request->validate([
            'employee_id' => 'required|integer',
            'month' => 'required|date_format:Y-m',
        ]);

        // Calculate performance allowance and late penalty based on presences
        $presences = Presence::where('employee_id', $validatedData['employee_id'])
            ->whereYear('timestamp_in', '=', substr($validatedData['month'], 0, 4))
            ->whereMonth('timestamp_in', '=', substr($validatedData['month'], 5, 2))
            ->get();

        $performanceAllowance = 0;
        $latePenalty = 0;

        foreach ($presences as $presence) {
            // Calculate performance allowance and late penalty for each presence
            // Add to $performanceAllowance and $latePenalty accordingly
        }

        // Calculate total take-home pay
        $totalTakeHomePay = 2000000 + $performanceAllowance - $latePenalty;

        $paySlip = [
            'month' => $validatedData['month'],
            'components' => [
                ['name' => 'Gaji Pokok', 'amount' => 2000000],
                ['name' => 'Tunjangan Kinerja', 'amount' => $performanceAllowance],
                ['name' => 'Potongan Keterlambatan', 'amount' => -$latePenalty],
            ],
            'take_home_pay' => $totalTakeHomePay,
        ];
        // dd($paySlip);
        // return "tes";

        return response()->json(['data' => $paySlip]);
        // return response()->json("tes");
    }
}
