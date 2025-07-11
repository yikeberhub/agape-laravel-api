@php use Illuminate\Support\Str; @endphp

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 100px;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 20px;
            margin: 0;
        }

        h3 {
            font-size: 14px;
            margin: 5px 0;
            color: #555;
        }

        .meta-info {
            margin-top: 5px;
            font-size: 11px;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        tbody tr:nth-child(even) {
            background-color: #c2c2fa;
        }

        tbody tr:nth-child(odd) {
            background-color: #e7eceed2;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 30px;
            right: 30px;
            font-size: 10px;
            text-align: center;
            color: #888;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>

<div class="header">
    <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Agape Mobility Ethiopia Logo">
    <h1>Agape Mobility Ethiopia</h1>
    <h3>Disabilities Report</h3>
    <div class="meta-info">
        <p>This document is used for reporting disability records to third-party organizations.</p>
        <p><strong>Exported at:</strong> {{ now()->format('F j, Y, h:i A') }}</p>
    </div>
</div>

<table>
    <thead>
        <tr>
            @foreach ($columns as $col)
                <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($disabilities as $disability)
            <tr>
                @foreach ($columns as $col)
                    @php
                        if (Str::startsWith($col, ['recorder_', 'warrant_', 'equipment_'])) {
                            $path = preg_replace('/_/', '.', $col, 1);
                            $value = data_get($disability, $path, '');
                        } else {
                            $value = $disability[$col] ?? '';
                            if (in_array($col, ['is_provided', 'is_active', 'is_deleted'])) {
                            $value = $value ? 'Yes' : 'No';
                        }
                         }
                    @endphp
                    <td>{{ $value }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    &copy; {{ date('Y') }} Agape Mobility Ethiopia. All rights reserved.
</div>

</body>
</html>
