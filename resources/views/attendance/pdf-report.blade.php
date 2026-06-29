<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>تقرير الحضور والغياب</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;700&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Noto Naskh Arabic', serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            direction: rtl;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            color: #4F46E5;
            font-size: 20px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .summary {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .summary-box {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .summary-box.green {
            background: #D1FAE5;
        }

        .summary-box.red {
            background: #FEE2E2;
        }

        .summary-box.yellow {
            background: #FEF3C7;
        }

        .summary-box.blue {
            background: #DBEAFE;
        }

        .summary-box .number {
            font-size: 24px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }

        th {
            background: #4F46E5;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: #999;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>تقرير الحضور والغياب</h1>
        <p>من {{ $startDate }} إلى {{ $endDate }}</p>
        <p>تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-box green">
            <div class="number">{{ $summary['present'] }}</div>
            <div>حاضر</div>
        </div>
        <div class="summary-box red">
            <div class="number">{{ $summary['absent'] }}</div>
            <div>غائب</div>
        </div>
        <div class="summary-box yellow">
            <div class="number">{{ $summary['late'] }}</div>
            <div>متأخر</div>
        </div>
        <div class="summary-box blue">
            <div class="number">{{ $summary['excused'] }}</div>
            <div>بعذر</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>التاريخ</th>
                <th>الطالب</th>
                <th>الحلقة</th>
                <th>الحالة</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->date->format('Y-m-d') }}</td>
                <td>{{ $record->student->name ?? '-' }}</td>
                <td>{{ $record->student->circle->name ?? '-' }}</td>
                <td>
                    @if($record->status === 'present') حاضر
                    @elseif($record->status === 'absent') غائب
                    @elseif($record->status === 'late') متأخر
                    @else بعذر @endif
                </td>
                <td>{{ $record->notes ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        تم إنشاء هذا التقرير بواسطة نظام مركز حملة القرآن
    </div>
</body>

</html>