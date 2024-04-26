<?php

namespace App\Exports;

use App\Http\Helpers\transaction\TransactionService;
use App\Http\Helpers\user\UserService;
use App\Http\Resources\V1\TransactionCollection;
use App\Http\Resources\V1\TransactionResource;
use App\Http\Helpers\customer\CustomerService;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $transactions;

    public function __construct($transaction){
        $this->transactions = $transaction;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }



    public function map($transaction): array
    {
        $employee = UserService::getFullnameById($transaction->user_id);
        $customer = CustomerService::getFullnameById($transaction->customer_id);
        $payment_method = TransactionService::toMethod($transaction->payment_method);
        $amount = floatval($transaction->amount_due);
        return [
            $transaction->reference_number,
            $transaction->amount_due,
            $transaction->number_of_items,
            $transaction->payment_method = $payment_method,
            $transaction->status,
            // $transaction->checkouts,
            $transaction->commission,
            $transaction->customer_id = $customer,
            $transaction->user_id = $employee,
            $transaction->created_at->format('Y-m-d'),
        ];
    }


    public function headings(): array
    {
        return [
            'Reference Number',
            'Amount',
            'No. of items',
            'Payment Method',
            'Status',
            // 'Checkouts',
            'Commission',
            'Customer',
            'Employee',
            'Date Created',
        ];
    }
}
