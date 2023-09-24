<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchange;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Http\Services\CurrencyApiService;


class CurrencyApiController extends Controller
{

    private $currencyApiService;

    public function __construct(CurrencyApiService $currencyApiService)
    {
        $this->currencyApiService = $currencyApiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = Http::get(env('NBP_API_CURRENCIES_URL'));
        $currencies = $response->json();

        return response()->json($this->currencyApiService->getExchangeRates($currencies));
    }

    public function fetchAndStoreCurrencyExchange()
    {


$currencies = ['EUR', 'GBP', 'USD'];
$messages = [];
foreach ($currencies as $currency){
$lastUpdated = Cache::get("last_update:{$currency}");

            if ($lastUpdated && Carbon::now()->isSameDay($lastUpdated)) {
                $messages[$currency] = "Data for {$currency} has already been updated today.";
            } else {
                $response = Http::get("http://api.nbp.pl/api/exchangerates/rates/a/{$currency}/last/3/");
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

    public function getCurrencyRatesByDate($date)
    {
        $currencyRates = CurrencyExchange::where('date', $date)->get(['currency_code', 'exchange_rate', 'date']);
        return response()->json(['data' => $currencyRates]);
    }
}

