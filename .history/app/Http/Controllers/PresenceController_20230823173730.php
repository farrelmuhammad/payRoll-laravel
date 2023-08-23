<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;

class PresenceController extends Controller
{
    public function clockIn(Request $request)
    {
        // Implementasi presensi masuk
    }

    public function clockOut(Request $request)
    {
        // Implementasi presensi keluar
    }

    public function generatePaySlip(Request $request)
    {
        // Implementasi pembuatan slip gaji
    }
}
