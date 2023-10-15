<?php


namespace App\Http\Services;


use App\Models\CurrencyExchange;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CurrencyApiService
{
    private $selectedCurrencies = ['EUR', 'GBP', 'USD'];

    public function getExchangeRates($currencies)
    {

        $selectedExchangeRates = [];

        foreach ($currencies[0]['rates'] as $rate) {
            if (in_array($rate['code'], $this->selectedCurrencies)) {
                $selectedExchangeRates[] = [
                    'currency' => $rate['code'],
                    'exchange_rate' => $rate['mid']
                ];
            }
        }

        return $selectedExchangeRates;
    }

    public function fetchRates()
    {

        foreach ($this->selectedCurrencies as $currency) {
            $currentDate = Carbon::now();
            if (DB::table('currency_exchanges')->where('currency_code', $currency)
                ->whereNotNull('exchange_rate')
                ->whereDate('created_at', $currentDate->toDateString())
                ->exists()) {
                return "Currencies has already been fetched. You can fetch currenices once a day";
            } else {
                $response = Http::get("http://api.nbp.pl/api/exchangerates/rates/a/{$currency}/last/3");
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
