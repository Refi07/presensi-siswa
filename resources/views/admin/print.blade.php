<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi Siswa - {{ $className }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #000; }
        th { background-color: #f2f2f2; padding: 8px; text-align: center; font-weight: bold; }
        td { padding: 6px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        @media print {
            @page { size: A4; margin: 15mm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>LAPORAN REKAP PRESENSI SISWA</h2>
        <p>Kelas: {{ $className }} | Periode: {{ \Carbon\Carbon::parse($selectedMonth)->translatedFormat('F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Tanggal</th>
                <th>Nama Siswa</th>
                <th width="20%">Jam Masuk</th>
                <th width="20%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $row->date }}</td>
                    <td class="font-bold">{{ $row->student->user->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $row->time_in ?? '-' }}</td>
                    <td class="text-center font-bold" style="text-transform: uppercase;">{{ $row->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data presensi pada bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>