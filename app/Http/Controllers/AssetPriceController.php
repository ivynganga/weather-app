<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Models\Asset;
use App\Models\AssetPrice;

class AssetPriceController extends Controller
{
    public function update() {
        $exchange_url = null;
        $assetid = null;
        
        $response = Asset::all('id', 'code');
        foreach ($response as $assets) {
            $exchange_url = 'https://rest.coinapi.io/v1/exchangerate/';
            $assetid = Str::upper($assets->code);
            $exchange_url = $exchange_url.$assetid.'/usd';
            $response_er = Http::withHeaders(['X-CoinAPI-Key' => env('COINAPI_APIKEY')])->get($exchange_url)->json();
            $amount_usd = $response_er['rate'];
            $exchangerate_url = 'https://v6.exchangerate-api.com/v6/pair/usd/kes/'.$amount_usd;
            $response_ex = Http::withHeaders(['Authorization' => 'Bearer '.env('EXCHANGERATEAPI_TOKEN')])->get($exchangerate_url)->json();
            if (is_array($response_ex) && sizeof($response_ex) > 0 && strtolower(trim($response_ex['result'])) == 'success') {
                $amount_kes = (strlen(trim($response_ex['conversion_result'])) > 0 ? $response_ex['conversion_result'] : 0);
                $rate = (strlen(trim($response_ex['conversion_rate'])) > 0 ? $response_ex['conversion_rate'] : 0);
            }
            $result = AssetPrice::Create(['asset_id' => $assets->id, 'amount_usd' => $amount_usd, 'amount_kes' => $amount_kes, 'rate' => $rate, 'date' => date('Y-m-d H:i:s')]);
        }
    }


    public function get() {
        $response = cache::remember('cryptoprice', now()->addseconds(28800), function () {
            $result = AssetPrice::with('asset')->orderByDesc('id')->limit(6)->get();;
            return($result);
        });
        return view('index', ['cryptos' => $response]);
    }

}
