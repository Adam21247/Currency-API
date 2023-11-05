<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CurrencyExchange extends Model
{
    use HasFactory;

    protected $fillable = ['currency_code', 'exchange_rate', 'date'];

    public static function getAllCurrency()
    {
        $result = DB::table('currency_exchanges')
            ->select('id', 'currency_code', 'exchange_rate', 'date')
            ->get()->toArray();
        return $result;
    }
}
