<?php

namespace App\Http\Controllers\Api\V1;
use App\Exports\SalesExport;
use App\Http\Controllers\Controller;
use App\Http\Helpers\transaction\TransactionService;
use App\Http\Utils\ResponseHelper;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    use ResponseHelper;
    public function exportSales(Request $request) {

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Transaction::query();

        if($startDate && $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        }

        $transactions = $query->get();

        $filename = TransactionService::generateFilename();
        $excelFile = Excel::download(new SalesExport($transactions), $filename)->getFile();

        $fileContent = base64_encode(file_get_contents($excelFile));

        return response()->json(['file_name' => $filename, 'excel_file' => $fileContent]);
    }
}
