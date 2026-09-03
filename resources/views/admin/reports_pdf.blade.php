<!DOCTYPE html>
<html>
<head>
    <title>Laporan Presensi Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { margin-top: 40px; float: right; text-align: center; width: 200px; }
    </style>
</head>
<body>

    <div class="text-center">
        <h2 class="uppercase" style="margin: 0;">Laporan Presensi Siswa</h2>
        <p style="margin: 5px 0 0 0;">Sistem Informasi Presensi Digital HadirKu</p>
        <hr style="margin-top: 10px; border: 1px solid #000;">
    </div>

    <p><b>Tanggal Presensi:</b> {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</p>
    <p><b>Kelas:</b> {{ $className }}</p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">NISN</th>
                <th width="30%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="15%">Jam Masuk</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->student->nisn ?? '-' }}</td>
                    <td><b>{{ $item->student->user->name ?? 'N/A' }}</b></td>
                    <td>{{ $item->student->class->class_name ?? 'Tanpa Kelas' }}</td>
                    <td>{{ $item->time_in }} WIB</td>
                    <td><b>{{ strtoupper($item->status) }}</b></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data presensi pada tanggal dan kelas ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Penanggung Jawab / Admin,</p>
        <br><br><br>
        <p class="font-bold"><u>{{ auth()->user()->name }}</u></p>
    </div>

</body>
</html>