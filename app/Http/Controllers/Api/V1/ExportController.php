<?php

namespace App\Http\Controllers\Api\V1;
use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\transaction\TransactionService;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportSales() {
        $filename = TransactionService::generateFilename();
        return Excel::download(new SalesExport, $filename);
    }
}
