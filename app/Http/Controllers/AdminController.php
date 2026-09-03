<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function index()
    {
        $totalSiswa = Student::count();
        $totalKelas = ClassModel::count();
        $totalGuru = User::where('role', 'teacher')->count();

        // Ambil status validasi lokasi (default 1 / ON jika belum ada)
        $locationSetting = Setting::firstOrCreate(
            ['key' => 'enable_location_check'],
            ['value' => '1']
        );

        // Ambil status batas waktu presensi (default 1 / ON jika belum ada)
        $timeSetting = Setting::firstOrCreate(
            ['key' => 'enable_time_limit'],
            ['value' => '1']
        );

        return view('admin.dashboard', compact('totalSiswa', 'totalKelas', 'totalGuru', 'locationSetting', 'timeSetting'));
    }

    // Mengubah Status Radius Presensi (ON / OFF)
    public function toggleLocationRadius(Request $request)
    {
        $setting = Setting::firstOrCreate(
            ['key' => 'enable_location_check'],
            ['value' => '1']
        );

        $setting->value = $setting->value == '1' ? '0' : '1';
        $setting->save();

        $statusText = $setting->value == '1' ? 'AKTIF (Harus di sekolah)' : 'NONAKTIF (Bebas lokasi)';
        return redirect()->back()->with('success', 'Validasi lokasi presensi berhasil diubah menjadi: ' . $statusText);
    }

    // Mengubah Status Batas Waktu Presensi 15:00 WIB (ON / OFF)
    public function toggleTimeLimit(Request $request)
    {
        $setting = Setting::firstOrCreate(
            ['key' => 'enable_time_limit'],
            ['value' => '1']
        );

        $setting->value = $setting->value == '1' ? '0' : '1';
        $setting->save();

        $statusText = $setting->value == '1' ? 'AKTIF (Presensi dikunci setelah 15:00 WIB)' : 'NONAKTIF (Bebas jam / Uji coba)';
        return redirect()->back()->with('success', 'Batas waktu presensi berhasil diubah menjadi: ' . $statusText);
    }

    // Kelola Data Kelas
    public function classes()
    {
        $classes = ClassModel::with('teacher')->get();
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.classes', compact('classes', 'teachers'));
    }

    public function storeClass(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        ClassModel::create([
            'class_name' => $request->class_name,
            'teacher_id' => $request->teacher_id,
        ]);

        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function destroyClass($id)
    {
        ClassModel::destroy($id);
        return redirect()->back()->with('success', 'Kelas berhasil dihapus!');
    }

    // Kelola Data Siswa
    public function students()
    {
        $students = Student::with(['user', 'class', 'parent'])->get();
        $classes = ClassModel::all();
        
        $parents = User::where('role', 'parent')
            ->with('students.user')
            ->get();

        return view('admin.students', compact('students', 'classes', 'parents'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'nisn' => 'required|string|unique:students,nisn',
            'class_id' => 'required|exists:classes,id',
            'parent_phone' => 'required|string|max:20',
            'parent_id' => 'nullable|exists:users,id',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'student';
        $user->save();

        $student = new Student();
        $student->nisn = $request->nisn;
        $student->user_id = $user->id;
        $student->class_id = $request->class_id;
        $student->parent_id = $request->parent_id;
        $student->parent_phone = $request->parent_phone;
        $student->qr_code_token = Str::random(32);
        $student->save();

        return redirect()->back()->with('success', 'Data Siswa & Nomor WA Ortu berhasil ditambahkan!');
    }

    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data Siswa & Orang Tua berhasil di-import sekaligus!');
    }

    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);
        User::destroy($student->user_id);
        $student->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus!');
    }

    public function parents()
    {
        $parents = User::where('role', 'parent')
            ->with(['students.user', 'student.user'])
            ->latest()
            ->get();

        return view('admin.parents', compact('parents'));
    }

    public function storeParent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'parent',
        ]);

        return redirect()->back()->with('success', 'Akun Orang Tua berhasil ditambahkan!');
    }

    public function teachers()
    {
        $teachers = User::where('role', 'teacher')->get();
        return view('admin.teachers', compact('teachers'));
    }

    public function storeTeacher(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'teacher',
        ]);

        return redirect()->back()->with('success', 'Data Guru & Akun Login berhasil ditambahkan!');
    }

    public function destroyTeacher($id)
    {
        User::destroy($id);
        return redirect()->back()->with('success', 'Data Guru berhasil dihapus!');
    }

    public function reports(Request $request)
    {
        $classes = ClassModel::all();
        $selectedClass = $request->input('class_id');
        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        $attendancesQuery = Attendance::with(['student.user', 'student.class'])
            ->whereDate('date', $selectedDate);

        if ($selectedClass) {
            $attendancesQuery->whereHas('student', function ($q) use ($selectedClass) {
                $q->where('class_id', $selectedClass);
            });
        }

        $attendances = $attendancesQuery->get();

        return view('admin.reports', compact('attendances', 'classes', 'selectedClass', 'selectedDate'));
    }
}