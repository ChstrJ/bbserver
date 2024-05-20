<?php

namespace App\Exports;

use App\Http\Helpers\transaction\TransactionService;
use App\Http\Helpers\user\UserService;
use App\Http\Helpers\customer\CustomerService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $transactions;

    public function __construct($transaction)
    {
        $this->transactions = $transaction;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Reference Number',
            'Amount',
            'No. of items',
            'Payment Method',
            'Status',
            'Checkouts',
            'Commission',
            'Customer',
            'Salesperson',
            'Date Ordered',
            'Time Ordered',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $firstCol = 1;
        $lastCol = count($this->transactions) + 1;
        $amountFormat = '[>=1000]#,##0.00;[<1000]#0.00';
        $sheet->getStyle(1)->getFont()->setBold(true);
        $sheet->getStyle("B{$firstCol}:B{$lastCol}")->getNumberFormat()->setFormatCode($amountFormat);
        $sheet->getStyle("G{$firstCol}:G{$lastCol}")->getNumberFormat()->setFormatCode($amountFormat);
        $sheet->setAutoFilter("A{$firstCol}:I{$lastCol}");
    }

    public function map($transaction): array
    {
        $employee = UserService::getFullnameById($transaction->user_id);
        $customer = CustomerService::getFullnameById($transaction->customer_id);
        $payment_method = TransactionService::toMethod($transaction->payment_method);
        $checkouts = TransactionService::processCheckouts($transaction->checkouts);
        $formattedTime = $transaction->created_at->format('h:i:s A');
        return [
            $transaction->reference_number,
            $transaction->amount_due,
            $transaction->number_of_items,
            $payment_method,
            $transaction->status,
            $checkouts,
            $transaction->commission,
            $customer,
            $employee,
            $transaction->created_at->format('m-d-Y'),
            $formattedTime,
        ];
    }

}
