<?php


namespace App\Http\Services;


use App\Models\CurrencyExchange;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CurrencyApiService
{
    const SELECTCURRENCIES = ['EUR', 'GBP', 'USD'];

    public function getExchangeRates($currencies)
    {

        $date = ($currencies[0]['effectiveDate']);

        $selectedExchangeRates = [];

        foreach ($currencies[0]['rates'] as $rate) {
            if (in_array($rate['code'], self::SELECTCURRENCIES)) {
                $selectedExchangeRates[] = [
                    'currency' => $rate['code'],
                    'exchange_rate' => $rate['mid'],
                    'date' => $date
                ];
            }
        }

        return $selectedExchangeRates;
    }

    public function fetchRates()
    {

        foreach (self::SELECTCURRENCIES as $currency) {
            $currentDate = Carbon::now();

            if (DB::table('currency_exchanges')->where('currency_code', $currency)
                ->whereNotNull('exchange_rate')
                ->whereDate('created_at', $currentDate->toDateString())
                ->exists()) {
                return "Currencies has already been fetched. You can fetch currencies once a day";
            } else {
                $response = Http::get(env('NBP_API_CURRENCIES_URL_LAST_DAYS') . "/$currency/last/3");
                $data = $response->json();
                foreach ($data['rates'] as $rateData) {
                    CurrencyExchange::create([
                        'currency_code' => $currency,
                        'exchange_rate' => $rateData['mid'],
                        'date' => $rateData['effectiveDate'],
                    ]);
                }
            }
        }
        return "Congratulations! You fetched currencies";
    }
}

