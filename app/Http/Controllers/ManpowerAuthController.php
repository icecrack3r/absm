<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AbsensiManpower;
use App\Models\JadwalAbsensiManpower;
use Carbon\Carbon;

class ManpowerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('manpower.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';
        
        if (Auth::guard('manpower')->attempt([
            $loginField => $credentials['login'],
            'password' => $credentials['password']
        ])) {
            return redirect()->route('manpower.dashboard');
        }

        return back()->withErrors([
            'login' => 'Kredensial yang diberikan tidak cocok dengan catatan kami.',
        ]);
    }

    public function dashboard()
    {
        $manpower = Auth::guard('manpower')->user();
        $today = Carbon::today();
        
        $jadwal = JadwalAbsensiManpower::where('manpower_id', $manpower->id)
            ->where('tanggal', $today)
            ->first();
            
        $absensi = AbsensiManpower::where('manpower_id', $manpower->id)
            ->where('tanggal', $today)
            ->first();

        return view('manpower.dashboard', compact('manpower', 'jadwal', 'absensi'));
    }

    public function checkIn(Request $request)
    {
        $manpower = Auth::guard('manpower')->user();
        $today = Carbon::today();
        
        $jadwal = JadwalAbsensiManpower::where('manpower_id', $manpower->id)
            ->where('tanggal', $today)
            ->first();
            
        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada jadwal absensi untuk hari ini'
            ]);
        }

        // Validasi lokasi
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $jadwal->latitude,
            $jadwal->longitude
        );

        if ($distance > $jadwal->radius_meter) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius yang diizinkan'
            ]);
        }

        AbsensiManpower::updateOrCreate(
            [
                'manpower_id' => $manpower->id,
                'tanggal' => $today
            ],
            [
                'jam_check_in' => Carbon::now()->format('H:i:s'),
                'latitude_check_in' => $request->latitude,
                'longitude_check_in' => $request->longitude,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil'
        ]);
    }

    public function checkOut(Request $request)
    {
        $manpower = Auth::guard('manpower')->user();
        $today = Carbon::today();
        
        $absensi = AbsensiManpower::where('manpower_id', $manpower->id)
            ->where('tanggal', $today)
            ->first();
            
        if (!$absensi || !$absensi->jam_check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in'
            ]);
        }

        $jadwal = JadwalAbsensiManpower::where('manpower_id', $manpower->id)
            ->where('tanggal', $today)
            ->first();

        // Validasi lokasi
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $jadwal->latitude,
            $jadwal->longitude
        );

        if ($distance > $jadwal->radius_meter) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius yang diizinkan'
            ]);
        }

        $absensi->update([
            'jam_check_out' => Carbon::now()->format('H:i:s'),
            'latitude_check_out' => $request->latitude,
            'longitude_check_out' => $request->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil'
        ]);
    }

    public function logout()
    {
        Auth::guard('manpower')->logout();
        return redirect()->route('manpower.login');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLonRad = deg2rad($lon2 - $lon1);

        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLonRad / 2) * sin($deltaLonRad / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}