<?php

namespace App\Http\Controllers\Api\V1;
use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\transaction\TransactionService;
use App\Http\Utils\ResponseHelper;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    use ResponseHelper;
    public function exportSales() {
        $filename = TransactionService::generateFilename();
        $excelFile =  Excel::download(new SalesExport, $filename)->getFile();

        $fileContent = base64_encode(file_get_contents($excelFile));

        return response()->json(['file_name' => $filename, 'excel_file' => $fileContent]);
    }
}
