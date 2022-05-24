<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Asset;
use View;


class AssetController extends Controller {
    private $asset_ids;

    public function __construct() 
    {
        $this->asset_ids = array('btc', 'eth', 'ltc', 'doge', 'usdt', 'bch');
        View::share('asset_ids', $this->asset_ids);
    }



    public function fetch() {
        $asset_id = $this->asset_ids;
        $asset_url = 'https://rest.coinapi.io/v1/assets'.(is_array($asset_id) && sizeof($asset_id) > 0 ? '?'.'filter_asset_id='.(implode(',', $asset_id)) : null);
        $asseticon_url = 'https://rest.coinapi.io/v1/assets/icons/100';

        $response = Http::withHeaders(['X-CoinAPI-Key' => env('COINAPI_APIKEY')])->get($asset_url)->json();
        foreach ($response as $assets => $asset) {
            $icon = null;
            $response_ic = Http::withHeaders(['X-CoinAPI-Key' => env('COINAPI_APIKEY')])->get($asseticon_url)->json();
            foreach ($response_ic as $asseticons => $asseticon) {
                if (strtolower(trim($asset['asset_id'])) == strtolower(trim($asseticon['asset_id']))) {
                    $icon = trim($asseticon['url']);
                }
            }
            $result = Asset::updateOrCreate(['code' => Str::lower($asset['asset_id'])], ['code' => Str::lower($asset['asset_id']), 'name' => $asset['name'], 'position' => array_search(Str::lower($asset['asset_id']), $asset_id) + 1, 'icon' => $icon, 'date' => date('Y-m-d H:i:s')]);
        }
    }
    
   
}
