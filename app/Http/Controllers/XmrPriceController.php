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
        // MANUAL PRICE OVERRIDE (No caching - immediate updates)
        // 
        // PRIVACY WARNING: Using API calls may expose your server's IP address and compromise
        // anonymity. If you are not concerned about this, keep the API section enabled below.
        // If you want enhanced privacy, uncomment the line below and manually update the 
        // XMR price value whenever you need to change your server's Monero pricing.
        //
        // Uncomment the line below and set your desired XMR price to disable API calls:
        // return number_format(416.00, 2, '.', ''); // Set your manual XMR price here
        
        return Cache::remember('xmr_price', 240, function () {
            
            // API PRICE FETCHING (Comment out this entire section if using manual price above)
            // ====================================================================
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
                    return number_format($data['monero']['usd'], 2, '.', '');
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
                    return number_format($data['USD'], 2, '.', '');
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
            // ====================================================================
            // END API PRICE FETCHING
            
        });
    }
}
