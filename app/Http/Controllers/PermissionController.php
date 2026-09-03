<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    // Form & Riwayat Pengajuan Izin oleh Orang Tua
    public function indexParent()
    {
        $user = Auth::user();
        $students = Student::where('parent_id', $user->id)->get();
        $studentIds = $students->pluck('id');

        $permissions = Permission::with('student.user')
            ->whereIn('student_id', $studentIds)
            ->latest()
            ->get();

        return view('parent.permissions', compact('students', 'permissions'));
    }

    // Simpan Pengajuan Izin dari Orang Tua
    public function storeParent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'type' => 'required|in:izin,sakit',
            'reason' => 'required|string',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')->store('attachments', 'public');
        }

        Permission::create([
            'student_id' => $request->student_id,
            'parent_id' => Auth::id(),
            'date' => $request->date,
            'type' => $request->type,
            'reason' => $request->reason,
            'attachment' => $filePath,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan izin berhasil dikirim! Menunggu persetujuan Guru.');
    }

    // Daftar Pengajuan Izin untuk Guru / Admin
    public function indexTeacher()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin bisa melihat semua pengajuan izin
            $permissions = Permission::with(['student.user', 'student.class', 'parent'])->latest()->get();
        } else {
            // Guru hanya melihat pengajuan dari siswa yang memiliki kelas bertaut dengan teacher_id miliknya
            $permissions = Permission::whereHas('student.class', function ($query) use ($user) {
                $query->where('teacher_id', $user->id);
            })->with(['student.user', 'student.class', 'parent'])->latest()->get();
        }

        return view('teacher.permissions', compact('permissions'));
    }

    // Update Status Izin (Approve / Reject) oleh Guru
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $permission = Permission::findOrFail($id);
        $permission->status = $request->status;
        $permission->save();

        // Jika disetujui (Approved), otomatis masukkan/update data ke tabel Attendances
        if ($request->status === 'approved') {
            Attendance::updateOrCreate(
                [
                    'student_id' => $permission->student_id,
                    'date' => $permission->date,
                ],
                [
                    'time_in' => '00:00:00',
                    'status' => $permission->type,
                ]
            );
        }

        return redirect()->back()->with('success', 'Status pengajuan izin berhasil diperbarui!');
    }
}