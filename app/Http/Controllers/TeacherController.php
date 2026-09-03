<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TeacherController extends Controller
{
    // Dashboard Guru & Rekap Kelas
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // Cari kelas yang diampu oleh guru ini
        $myClass = ClassModel::where('teacher_id', $user->id)->first();

        $students = [];
        $todayAttendances = [];
        $todayClassCode = '-';

        if ($myClass) {
            $students = Student::with('user')->where('class_id', $myClass->id)->get();
            $todayAttendances = Attendance::whereIn('student_id', $students->pluck('id'))
                ->whereDate('date', $today)
                ->get()
                ->keyBy('student_id');

            // Generate Kode Unik Harian Khusus Kelas Guru Ini
            $todayClassCode = strtoupper(substr(md5($myClass->id . $today . 'HADIRKU_SECRET_KEY'), 0, 6));
        }

        // Cek apakah sistem presensi hari ini sudah dikunci (Lewat jam PRESENCE_LOCK_TIME)
        $lockTime = env('PRESENCE_LOCK_TIME', '15:00');
        $isLocked = Carbon::now()->format('H:i') >= $lockTime;

        return view('teacher.dashboard', compact('myClass', 'students', 'todayAttendances', 'today', 'todayClassCode', 'isLocked', 'lockTime'));
    }

    // Input Manual Presensi (Izin, Sakit, Alfa, Hadir)
    public function storeManual(Request $request)
    {
        // Cek Kunci Edit
        $lockTime = env('PRESENCE_LOCK_TIME', '15:00');
        if (Carbon::now()->format('H:i') >= $lockTime) {
            return redirect()->back()->with('error', 'Waktu pengubahan presensi harian telah ditutup (Batas: ' . $lockTime . ' WIB). Hubungi Admin untuk perubahan data.');
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:hadir,terlambat,izin,sakit,alfa',
        ]);

        $today = Carbon::today()->toDateString();
        $currentTime = Carbon::now()->toTimeString();

        // Update jika sudah ada, atau buat baru jika belum absen
        Attendance::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'date' => $today,
            ],
            [
                'time_in' => $currentTime,
                'status' => $request->status,
            ]
        );

        return redirect()->back()->with('success', 'Status presensi siswa berhasil diperbarui!');
    }
}