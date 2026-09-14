<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// 1. ROUTE HALAMAN UTAMA & REDIRECT DASHBOARD
Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'teacher') return redirect()->route('teacher.dashboard');
        if ($role === 'student') return redirect()->route('student.dashboard');
        if ($role === 'parent') return redirect()->route('parent.dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $role = Auth::user()->role;
    if ($role === 'admin') return redirect()->route('admin.dashboard');
    if ($role === 'teacher') return redirect()->route('teacher.dashboard');
    if ($role === 'student') return redirect()->route('student.dashboard');
    if ($role === 'parent') return redirect()->route('parent.dashboard');
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 2. GROUP ROUTE KHUSUS ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::post('/settings/toggle-location', [AdminController::class, 'toggleLocationRadius'])->name('admin.settings.toggle-location');
    Route::post('/settings/toggle-time', [AdminController::class, 'toggleTimeLimit'])->name('admin.settings.toggle-time');

    Route::get('/classes', [AdminController::class, 'classes'])->name('admin.classes');
    Route::post('/classes', [AdminController::class, 'storeClass'])->name('admin.classes.store');
    Route::delete('/classes/{id}', [AdminController::class, 'destroyClass'])->name('admin.classes.destroy');

    Route::get('/students', [AdminController::class, 'students'])->name('admin.students');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('admin.students.store');
    Route::post('/students/import', [AdminController::class, 'importStudents'])->name('admin.students.import');
    Route::delete('/students/{id}', [AdminController::class, 'destroyStudent'])->name('admin.students.destroy');

    Route::get('/parents', [AdminController::class, 'parents'])->name('admin.parents');
    Route::post('/parents', [AdminController::class, 'storeParent'])->name('admin.parents.store');

    Route::get('/teachers', [AdminController::class, 'teachers'])->name('admin.teachers');
    Route::post('/teachers', [AdminController::class, 'storeTeacher'])->name('admin.teachers.store');
    Route::delete('/teachers/{id}', [AdminController::class, 'destroyTeacher'])->name('admin.teachers.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/print', [ReportController::class, 'printPdf'])->name('admin.reports.print');
    Route::get('/reports/pdf', [ReportController::class, 'downloadPdf'])->name('admin.reports.pdf');
});

// 3. GROUP ROUTE GURU & ADMIN (PERSETUJUAN IZIN)
Route::middleware(['auth'])->prefix('teacher')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'index'])->name('teacher.dashboard');
    Route::post('/attendance/manual', [TeacherController::class, 'storeManual'])->name('teacher.attendance.manual');

    Route::get('/permissions', [PermissionController::class, 'indexTeacher'])->name('teacher.permissions.index');
    Route::post('/permissions/{id}/update', [PermissionController::class, 'updateStatus'])->name('teacher.permissions.update');
});

// 4. GROUP ROUTE KHUSUS SISWA
Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'index'])->name('student.dashboard');
    Route::get('/scan', [StudentController::class, 'scanScreen'])->name('student.scan');
    Route::post('/scan/process', [StudentController::class, 'processScan'])->name('student.scan.process');
});

// 5. GROUP ROUTE KHUSUS ORANG TUA
Route::middleware(['auth', 'role:parent'])->prefix('parent')->group(function () {
    Route::get('/dashboard', [ParentController::class, 'index'])->name('parent.dashboard');
    Route::get('/permissions', [PermissionController::class, 'indexParent'])->name('parent.permissions.index');
    Route::post('/permissions', [PermissionController::class, 'storeParent'])->name('parent.permissions.store');
});

// 6. ROUTE AKSES BERSAMA (FITUR PENGUMUMAN & PROFILE)
Route::middleware('auth')->group(function () {
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::get('/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::post('/scan/store', [AttendanceController::class, 'storeScan'])->name('attendance.scan.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';