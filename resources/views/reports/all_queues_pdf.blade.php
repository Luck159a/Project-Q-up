<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>รายงานคิวเข้ารับบริการ</title>
    <style>
        @font-face {
            font-family: 'Sarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        body {
            font-family: 'Sarabun';
            font-size: 16px;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
        <h2>รายงานข้อมูลคิวเข้ารับบริการ</h2>
        <p>วันที่ออกรายงาน: {{ now()->format('d/m/Y H:i') }} น.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ลำดับ</th>
                <th>เลขคิว</th>
                <th>ช่วงเวลา</th>
                <th>ชื่อคนไข้</th>
                <th>แพทย์</th>
                <th>สถานะ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($queues as $index => $queue)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $queue->labelNo }}</td>
                <td>{{ $queue->period }}</td>
                <td>{{ $queue->user->name ?? 'ไม่ระบุ' }}</td>
                <td>{{ $queue->doctorSchedule->user->name ?? 'ไม่ระบุ' }}</td>
                <td>{{ $queue->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>