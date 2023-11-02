<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchange;
use Illuminate\Support\Facades\Http;
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
        $response = Http::get(env('NBP_API_CURRENCIES_URL_TODAY'));
        $currencies = $response->json();

        return response()->json($this->currencyApiService->getExchangeRates($currencies));
    }

    public function store()
    {
        return response()->json(['message'=>$this->currencyApiService->fetchRates()]);
    }


    public function getCurrencyRatesByDate($date)
    {
        $currencyRates = CurrencyExchange::where('date', $date)->get(['currency_code', 'exchange_rate', 'date']);
        return response()->json(['data' => $currencyRates]);
    }
}



