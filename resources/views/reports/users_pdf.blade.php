<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* 1. กำหนด Font Face โดยใช้ base_path เพื่อให้ DomPDF หาไฟล์บน Windows เจอแน่นอน */
        @font-face {
            font-family: 'Sarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ base_path('public/fonts/Sarabun-Regular.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Sarabun';
            font-style: normal;
            font-weight: bold;
            src: url("{{ base_path('public/fonts/Sarabun-Bold.ttf') }}") format('truetype');
        }

        /* 2. บังคับใช้ฟอนต์ Sarabun กับทุกจุดในเอกสาร */
        * {
            font-family: 'Sarabun', sans-serif !important;
        }

        table, th, td {
            font-family: 'Sarabun', sans-serif !important;
        }

        body {
            font-size: 16px;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .report-header {
            margin-bottom: 20px;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
            padding: 8px;
        }

        td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: middle;
            word-wrap: break-word; 
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>รายงานบัญชีผู้ใช้</h2>
    
    <div class="report-header">
        <strong>วันที่ออกรายงาน:</strong> {{ date('d/m/Y') }} <br>
        <strong>จำนวนผู้ใช้ทั้งหมด:</strong> {{ count($users) }} ราย
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="10%">ID</th>
                <th width="35%">ชื่อ-นามสกุล</th>
                <th width="40%">อีเมล</th>
                <th width="15%">บทบาท</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="text-center">{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td class="text-center">
                    @php
                        $roles = [
                            'admin' => 'ผู้ดูแลระบบ',
                            'doctor' => 'หมอ',
                            'staff' => 'เจ้าหน้าที่',
                            'patient' => 'ผู้ป่วย'
                        ];
                    @endphp
                    {{ $roles[$user->role] ?? ucfirst($user->role) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">ไม่พบข้อมูลผู้ใช้</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>