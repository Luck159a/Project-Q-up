<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>รายงานคิวเข้ารับบริการรายวัน</title>
    <style>
        @font-face {
            font-family: 'Sarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        body {
            font-family: 'Sarabun', sans-serif;
            font-size: 16px;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>รายงานคิวเข้ารับบริการรายวัน</h2>
        <p>วันที่ประจำรายงาน: {{ \Carbon\Carbon::today()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>เลขคิว</th>
                <th>ช่วงเวลา</th>
                <th>คนไข้</th>
                <th>แพทย์</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($queues as $item)
            <tr>
                <td>{{ $item->labelNo }}</td>
                <td>{{ $item->period }}</td>
                <td>{{ $item->user->name ?? 'ไม่ระบุ' }}</td>
                <td>{{ $item->doctorSchedule->user->name ?? 'ไม่ระบุ' }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>