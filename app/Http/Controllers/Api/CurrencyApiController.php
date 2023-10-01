<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchange;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Http\Services\CurrencyApiService;
use Illuminate\Support\Facades\Request;


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
        $response = Http::get(env('NBP_API_CURRENCIES_URL_TODAY'));
        $currencies = $response->json();

        return response()->json($this->currencyApiService->getExchangeRates($currencies));
    }

    public function fetchAndStoreCurrencyExchange()
    {

        $currencies = ['EUR', 'GBP', 'USD'];
        foreach ($currencies as $currency) {
            $currentDate = Carbon::now();
            if (DB::table('currency_exchanges')->where('currency_code', $currency)
                ->whereNotNull('exchange_rate')
                ->whereDate('created_at', $currentDate->toDateString())
                ->exists()) {
                    return response()->json(['message' => 'Currencies already fetched']);
            } else {
                $response = Http::get("http://api.nbp.pl/api/exchangerates/rates/a/{$currency}/last/3");
                $data = $response->json();
                dump($data);
                foreach ($data['rates'] as $rateData) {
                    CurrencyExchange::create([
                        'currency_code' => $currency,
                        'exchange_rate' => $rateData['mid'],
                        'date' => $rateData['effectiveDate'],
                    ]);
                }
            }
        }
            return response()->json(['message' => 'Congratulations! You fetched currencies']);
    }


        public function getCurrencyRatesByDate($date)
        {
            $currencyRates = CurrencyExchange::where('date', $date)->get(['currency_code', 'exchange_rate', 'date']);
            return response()->json(['data' => $currencyRates]);
        }
    }



