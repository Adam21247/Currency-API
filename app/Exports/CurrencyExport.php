<?php

namespace App\Exports;

use App\Models\CurrencyExchange;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CurrencyExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect(CurrencyExchange::getAllCurrency());
    }

    public function headings(): array
    {
        return [
            'Id',
            'Currency_code',
            'Exchange_rate',
            'Date',
        ];
    }
}
