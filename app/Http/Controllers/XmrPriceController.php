<?php
namespace App\Http\Controllers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class XmrPriceController extends Controller
{
    public function getXmrPrice()
    {
        return Cache::remember('xmr_price', 240, function () {
            $client = new Client();
            
            // First, try CoinGecko API
            try {
                $response = $client->request('GET', 'https://api.coingecko.com/api/v3/simple/price', [
                    'query' => [
                        'ids' => 'monero',
                        'vs_currencies' => 'usd',
                    ],
                    'timeout' => 5,
                ]);
                $data = json_decode($response->getBody(), true);
                if (isset($data['monero']['usd'])) {
                    return intval($data['monero']['usd']);
                }
            } catch (\Exception $e) {
                Log::warning('CoinGecko API error: ' . $e->getMessage());
            }
            
            // If CoinGecko fails, try CryptoCompare API
            try {
                $response = $client->request('GET', 'https://min-api.cryptocompare.com/data/price', [
                    'query' => [
                        'fsym' => 'XMR',
                        'tsyms' => 'USD',
                    ],
                    'timeout' => 5,
                ]);
                $data = json_decode($response->getBody(), true);
                if (isset($data['USD'])) {
                    return intval($data['USD']);
                }
            } catch (ConnectException $e) {
                Log::error('CryptoCompare API Connection error: ' . $e->getMessage());
            } catch (RequestException $e) {
                Log::error('CryptoCompare API Request error: ' . $e->getMessage());
            } catch (\Exception $e) {
                Log::error('Unexpected error when fetching XMR price: ' . $e->getMessage());
            }
            
            // If both APIs fail, return 'UNAVAILABLE'
            return 'UNAVAILABLE';
        });
    }
}