<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class ParentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Cari data siswa yang terhubung dengan akun orang tua ini
        $student = Student::with(['user', 'class'])->where('parent_id', $user->id)->first();

        $attendances = [];
        $totalHadir = 0;
        $totalTerlambat = 0;
        $totalIzin = 0;
        $totalAlfa = 0;

        if ($student) {
            $attendances = Attendance::where('student_id', $student->id)
                ->orderBy('date', 'desc')
                ->get();

            $totalHadir = $attendances->where('status', 'hadir')->count();
            $totalTerlambat = $attendances->where('status', 'terlambat')->count();
            $totalIzin = $attendances->whereIn('status', ['izin', 'sakit'])->count();
            $totalAlfa = $attendances->where('status', 'alfa')->count();
        }

        return view('parent.dashboard', compact('student', 'attendances', 'totalHadir', 'totalTerlambat', 'totalIzin', 'totalAlfa'));
    }
}