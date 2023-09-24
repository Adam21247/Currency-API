<?php


namespace App\Http\Services;


class CurrencyApiService
{
    private $selectedCurrencies = ['EUR', 'GBP', 'USD'];

    public function getExchangeRates($currencies) {

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

}
