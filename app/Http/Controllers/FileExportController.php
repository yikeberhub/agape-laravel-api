<?php

namespace App\Http\Controllers;

use App\Exports\DisabilitiesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Barryvdh\DomPDF\Facade\Pdf;

class FileExportController extends Controller
{
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel,csv',
            'columns' => 'required|array|min:1',
            'columns.*' => 'string',
            'filters' => 'nullable|array',
        ]);
    
        $format = $request->input('format');
        $columns = $request->input('columns');
        $filters = $request->input('filters', []);
    
        $filename = 'disabilities_export_' . now()->format('Ymd_His');
    
        if (in_array($format, ['excel', 'csv'])) {
            $export = new DisabilitiesExport($filters, $columns);
            $writerType = $format === 'excel' ? ExcelFormat::XLSX : ExcelFormat::CSV;
    
            return Excel::download($export, $filename . ($format === 'excel' ? '.xlsx' : '.csv'), $writerType);
        }

        if ($format === 'pdf') {
            $disabilities = (new DisabilitiesExport($filters, $columns))->collection();
    
            $data = [
                'disabilities' => $disabilities,
                'columns' => $columns,
            ];
    
            $pdf = Pdf::loadView('exports.disabilities_pdf', $data)->setPaper('a4', 'landscape');
    
            return $pdf->download($filename . '.pdf');
        }
    
        return response()->json(['error' => 'Unsupported format'], 400);
    }
}
