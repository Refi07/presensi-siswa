<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\ClassModel;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassModel::all();
        $selectedClass = $request->input('class_id');
        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        $query = Attendance::with(['student.user', 'student.class'])
            ->whereDate('date', $selectedDate);

        if ($selectedClass && $selectedClass !== '') {
            $query->whereHas('student', function ($q) use ($selectedClass) {
                $q->where('class_id', $selectedClass);
            });
        }

        $attendances = $query->orderBy('time_in', 'asc')->get();

        return view('admin.reports', compact('attendances', 'classes', 'selectedClass', 'selectedDate'));
    }

    public function downloadPdf(Request $request)
    {
        $selectedClass = $request->input('class_id');
        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        $query = Attendance::with(['student.user', 'student.class'])
            ->whereDate('date', $selectedDate);

        if ($selectedClass && $selectedClass !== '') {
            $query->whereHas('student', function ($q) use ($selectedClass) {
                $q->where('class_id', $selectedClass);
            });
        }

        $attendances = $query->orderBy('time_in', 'asc')->get();
        
        $className = 'Semua Kelas';
        if ($selectedClass) {
            $classModel = ClassModel::find($selectedClass);
            if ($classModel) {
                $className = $classModel->class_name;
            }
        }

        $pdf = Pdf::loadView('admin.reports_pdf', compact('attendances', 'selectedDate', 'className'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Presensi-' . $selectedDate . '.pdf');
    }
}