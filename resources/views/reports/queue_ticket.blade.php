<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* 1. นิยามฟอนต์ให้ระบบรู้จัก (ภายใต้ <head>) */
        @font-face {
            font-family: 'Sarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/Sarabun-Regular.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Sarabun';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/Sarabun-Bold.ttf') }}") format('truetype');
        }

        /* 2. บังคับใช้ฟอนต์กับทุกสิ่งในหน้านี้ (ภายใต้สัญลักษณ์ *) */
        * {
            font-family: 'Sarabun', sans-serif !important;
        }

        body {
            text-align: center;
            color: #333;
            line-height: 1.1;
        }

        /* 3. ส่วนของการจัดรูปแบบใบเสร็จ (Layout) */
        .ticket {
            border: 2px dashed #6366f1;
            padding: 15px;
            margin: 5px;
        }
        .label-no {
            font-size: 50px;
            font-weight: bold;
            color: #4f46e5;
            margin: 10px 0;
        }
        .order-info {
            background: #f3f4f6;
            padding: 10px;
            margin-bottom: 15px;
        }
        .details {
            text-align: left;
            display: inline-block;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <h2 style="margin-bottom: 0;">ใบยืนยันการจองคิว</h2>
        <p>โรงพยาบาล/คลินิกของเรา</p>
        
        <div class="label-no">{{ $queue->labelNo }}</div>
        
        <div class="order-info">
            <div style="font-size: 22px;">ลำดับคิวของคุณคือ <strong>ที่ {{ $myOrder }}</strong></div>
            <div style="color: #e67e22;">(มีคิวก่อนหน้า {{ $queueBeforeCount }} คิว)</div>
        </div>

        <div class="details">
            <p><strong>ชื่อผู้ป่วย:</strong> {{ $queue->user->name }}</p>
            <p><strong>แพทย์:</strong> {{ $queue->doctorSchedule->user->name }}</p>
            <p><strong>เวลานัด:</strong> {{ $queue->period }} น.</p>
            <p><strong>วันที่นัด:</strong> {{ \Carbon\Carbon::parse($queue->doctorSchedule->schedule_date)->format('d/m/Y') }}</p>
        </div>

        <hr>
        <p style="font-size: 14px;">* กรุณามาถึงก่อนเวลานัด 15 นาที และแสดงใบนี้ต่อเจ้าหน้าที่</p>
    </div>
</body>
</html>