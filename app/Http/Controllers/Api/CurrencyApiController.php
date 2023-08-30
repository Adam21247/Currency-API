<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
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

        $selectedCurrencies = ['USD', 'EUR', 'GBP'];
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

        foreach ($currencies as $currency) {
            $lastUpdated = Cache::get("last_update:{$currency}");
            if ($lastUpdated && Carbon::now()->isSameDay($lastUpdated)) {
                return response()->json(['message' => 'Currency exchange rates already been fetched today.']);
            } else {
                $response = Http::get("http://api.nbp.pl/api/exchangerates/rates/a/{$currency}/last/2/");
                $data = $response->json();


                foreach ($data['rates'] as $rateData) {

                    CurrencyExchange::create([
                        'currency_code' => $currency,
                        'exchange_rate' => $rateData['mid'],
                        'date' => $rateData['effectiveDate'],
                    ]);

                }
                Cache::put("last_update:{$currency}", Carbon::now(), now()->addDay());
            }



        }
        return response()->json(['message' => 'Currency exchange rates fetched and stored.']);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
