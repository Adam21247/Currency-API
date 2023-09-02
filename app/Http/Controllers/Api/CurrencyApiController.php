<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CurrencyApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = Http::get('http://api.nbp.pl/api/exchangerates/tables/A/');
        $currencies = $response->json();
        $selectedCurrencies = ['EUR', 'GBP', 'USD'];
        $selectedExchangeRates = [];
        foreach ($currencies[0]['rates'] as $rate) {
            if (in_array($rate['code'], $selectedCurrencies)) {
                $selectedExchangeRates[] = [
                    'currency' => $rate['code'],
                    'exchange_rate' => $rate['mid']
                ];
            }
        }
        return response()->json($selectedExchangeRates);
    }

    public function fetchAndStoreCurrencyExchange()
    {
        $currencies = ['EUR', 'GBP', 'USD'];
        $messages = [];
        foreach ($currencies as $currency) {
            $lastUpdated = Cache::get("last_update:{$currency}");

            if ($lastUpdated && Carbon::now()->isSameDay($lastUpdated)) {
                $messages[$currency] = "Data for {$currency} has already been updated today.";
            } else {
                $response = Http::get("http://api.nbp.pl/api/exchangerates/rates/a/{$currency}/last/7/");
                $data = $response->json();
                foreach ($data['rates'] as $rateData) {
                    CurrencyExchange::create([
                        'currency_code' => $currency,
                        'exchange_rate' => $rateData['mid'],
                        'date' => $rateData['effectiveDate'],
                    ]);
                }
                Cache::put("last_update:{$currency}", Carbon::now(), now()->addDay());
                $messages[$currency] = "Currency exchange rates for {$currency} fetched and stored.";
            }
        }
        return response()->json(['messages' => $messages]);
    }
}
