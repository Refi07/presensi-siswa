<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class StudentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Cari data detail siswa berdasarkan user login
        $student = Student::with(['user', 'class'])->where('user_id', $user->id)->first();

        $attendances = [];
        $totalHadir = 0;
        $totalTerlambat = 0;

        if ($student) {
            $attendances = Attendance::where('student_id', $student->id)
                ->orderBy('date', 'desc')
                ->get();

            $totalHadir = $attendances->where('status', 'hadir')->count();
            $totalTerlambat = $attendances->where('status', 'terlambat')->count();
        }

        return view('student.dashboard', compact('student', 'attendances', 'totalHadir', 'totalTerlambat'));
    }

    public function scanScreen()
    {
        return view('student.scan');
    }

    public function processScan(Request $request)
    {
        $user = Auth::user();
        $student = Student::with(['class', 'user'])->where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data siswa tidak terhubung dengan akun ini.'
            ], 400);
        }

        // --- 1. VALIDASI BATAS WAKTU PRESENSI (15:00 WIB) ---
        $timeSetting = Setting::where('key', 'enable_time_limit')->first();
        $isTimeLimitActive = $timeSetting ? ($timeSetting->value == '1') : true;

        if ($isTimeLimitActive && Carbon::now()->format('H:i') > '15:00') {
            return response()->json([
                'status' => 'error',
                'message' => 'Batas waktu presensi hari ini telah berakhir (Maksimal jam 15:00 WIB)!'
            ], 400);
        }

        // --- 2. VALIDASI LOKASI GPS (GEOFENCING) ---
        $locationSetting = Setting::where('key', 'enable_location_check')->first();
        $isLocationActive = $locationSetting ? ($locationSetting->value == '1') : true;

        if ($isLocationActive) {
            $studentLat = $request->input('latitude');
            $studentLng = $request->input('longitude');

            if (!$studentLat || !$studentLng) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mendeteksi lokasi GPS Anda. Izinkan akses lokasi di browser.'
                ], 400);
            }

            // Koordinat Lokasi SMK Antartika 2 Sidoarjo
            $schoolLat = -7.4339;   
            $schoolLng = 112.7231;  
            
            // Radius lokasi aktif: 100 meter (Gunakan jarak sebenarnya)
            $maxRadiusMeters = 100; 

            // Hitung jarak menggunakan rumus Haversine
            $distance = $this->calculateDistance($studentLat, $studentLng, $schoolLat, $schoolLng);

            if ($distance > $maxRadiusMeters) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda berada di luar lokasi sekolah! Jarak Anda: ' . round($distance) . ' meter dari sekolah.'
                ], 400);
            }
        }

        // --- 3. VALIDASI KODE PRESENSI ---
        // Menangkap parameter qr_code_token atau qr_code
        $inputData = strtoupper(trim($request->input('qr_code_token', $request->input('qr_code'))));
        $today = Carbon::today()->toDateString();

        $validClassCode = strtoupper(substr(md5($student->class_id . $today . 'HADIRKU_SECRET_KEY'), 0, 6));

        $validSchoolCodes = [
            'HADIRKU-SEKOLAH-SECRET', 
            $validClassCode           
        ];

        if (!in_array($inputData, $validSchoolCodes)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode atau QR Code tidak valid untuk kelas Anda hari ini!'
            ], 400);
        }

        $currentTime = Carbon::now()->format('H:i:s');

        // Cek apakah siswa sudah presensi hari ini
        $attendance = Attendance::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Anda sudah melakukan presensi hari ini!'
            ]);
        }

        // Tentukan Status berdasarkan Config (Batas Jam 07:30 WIB)
        $timeLimit = config('app.presence_in_time', '07:30');
        $status = (Carbon::now()->format('H:i') > $timeLimit) ? 'terlambat' : 'hadir';

        Attendance::create([
            'student_id' => $student->id,
            'date' => $today,
            'time_in' => $currentTime,
            'status' => $status,
        ]);

        // --- KIRIM WA OTOMATIS VIA FONNTE API JIKA TERLAMBAT ---
        if ($status === 'terlambat') {
            $parentPhone = $student->parent_phone ?? '089687048663';
            $studentName = $student->user->name ?? 'Siswa';
            $formattedDate = Carbon::now()->translatedFormat('d F Y');

            $message = "INFO PRESENSI HADIRKU\n" .
                       "SMK Antartika 2 Sidoarjo\n\n" .
                       "Yth. Orang Tua/Wali dari *{$studentName}*,\n\n" .
                       "Diberitahukan bahwa putra/putri Anda pada hari ini ({$formattedDate}) tercatat TERLAMBAT masuk sekolah pada pukul *{$currentTime} WIB*.\n\n" .
                       "_Pesan ini dikirim otomatis oleh Sistem HadirKu._";

            // Kirim request background ke Fonnte API
            $this->sendFonnteNotification($parentPhone, $message);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Presensi Berhasil! Status: ' . strtoupper($status)
        ]);
    }

    private function sendFonnteNotification($targetPhone, $message)
    {
        $token = env('FONNTE_TOKEN');

        if (!$token) {
            return; // Abaikan jika token belum diisi di .env
        }

        try {
            Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $targetPhone,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim WA via Fonnte: ' . $e->getMessage());
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}