<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // Koordinat Titik Sekolah & Maximum Radius (Meter)
    private $schoolLat = -6.200000;  // Sesuaikan koordinat latitude sekolahmu
    private $schoolLng = 106.816666; // Sesuaikan koordinat longitude sekolahmu
    private $maxRadius = 100;         // Radius maksimum dalam meter

    public function scan()
    {
        return view('attendance.scan');
    }

    public function storeScan(Request $request)
    {
        $request->validate([
            'qr_code_token' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // 1. Cek Saklar Radius (ON / OFF) dari Database
        $locationSetting = Setting::where('key', 'enable_location_check')->first();
        $isLocationCheckActive = $locationSetting ? ($locationSetting->value == '1') : true;

        // 2. Jalankan Validasi Lokasi HANYA JIKA Saklar bernilai ON (1)
        if ($isLocationCheckActive) {
            $userLat = $request->latitude;
            $userLng = $request->longitude;

            if (!$userLat || !$userLng) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mendeteksi lokasi GPS Anda!'
                ], 400);
            }

            $distance = $this->calculateDistance($userLat, $userLng, $this->schoolLat, $this->schoolLng);

            if ($distance > $this->maxRadius) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda berada di luar radius sekolah! Jarak Anda: ' . round($distance) . ' meter.'
                ], 400);
            }
        }

        // 3. Cari Data Siswa berdasarkan Token QR
        $student = Student::where('qr_code_token', $request->qr_code_token)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak valid!'
            ], 404);
        }

        // 4. Simpan / Update Data Presensi
        $today = Carbon::today()->toDateString();
        $attendance = Attendance::firstOrCreate(
            ['student_id' => $student->id, 'date' => $today],
            ['time_in' => Carbon::now()->toTimeString(), 'status' => 'Hadir']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Presensi berhasil dicatat untuk siswa: ' . ($student->user->name ?? 'Siswa')
        ]);
    }

    // Rumus Haversine untuk Mengukur Jarak Koordinat (Meter)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}