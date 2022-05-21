<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Asset;

class update_assets extends Command
{
    private $asset_ids = array('btc', 'eth', 'ltc', 'doge', 'usdt', 'bch');

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto:update_asset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
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
