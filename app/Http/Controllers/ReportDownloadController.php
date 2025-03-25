<?php

namespace App\Http\Controllers;

use App\Models\DownloadedReport;
use Illuminate\Support\Facades\Storage;

class ReportDownloadController extends Controller
{
    public function download(DownloadedReport $report)
    {
        $fullPath = $report->getFullFilePath();
        
        if (!Storage::exists($fullPath)) {
            abort(404, 'Report file not found');
        }

        return response()->download(
            Storage::path($fullPath),
            $report->file_name,
            ['Content-Type' => 'text/csv']
        );
    }
}
