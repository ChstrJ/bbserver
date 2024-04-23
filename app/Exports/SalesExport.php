<?php

namespace App\Exports;

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
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Transaction::all();
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
        $amount = floatval($transaction->amount_due); 
        return [
            $transaction->reference_number,
            $transaction->amount_due,
            $transaction->number_of_items,
            $transaction->payment_method,
            $transaction->status,
            // $transaction->checkouts,
            $transaction->image,
            $transaction->commission,
            $transaction->customer_id = $customer,
            $transaction->user_id = $employee,
            $transaction->created_at->format('Y-m-d'),
            $transaction->updated_at->format('Y-m-d'),
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
            'Image',
            'Commission',
            'Customer',
            'Employee',
            'Created at',
            'Updated at',
        ];
    }
}
