@php use Illuminate\Support\Str; @endphp

<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { margin-bottom: 20px; }
        h1, h3, p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #444; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>

<div class="header">
    <h1>Agape Mobility Ethiopia</h1>
    <h3>Disabilities Report</h3>
    <p>This document is used for reporting disability records to third-party organizations.</p>
    <p><strong>Exported at:</strong> {{ now()->format('F j, Y H:i A') }}</p>
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
                        // Replace ONLY the first underscore with a dot (for nested access)
                        $path = preg_replace('/_/', '.', $col, 1);
                        $value = data_get($disability, $path, '');
                    } else {
                        // Direct field access (non-nested)
                        $value = $disability[$col] ?? '';
                    }
                @endphp
                <td>{{ $value }}</td>
            @endforeach
            
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
