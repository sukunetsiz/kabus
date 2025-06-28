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
        // PRIVACY WARNING: Even with Tor routing, API calls create network traffic patterns.
        // For maximum privacy, consider using manual price updates instead.
        // If you want enhanced privacy, uncomment the line below and manually update the 
        // XMR price value whenever you need to change your server's Monero pricing.
        //
        // Uncomment the line below and set your desired XMR price to disable API calls:
        // return number_format(416.00, 2, '.', ''); // Set your manual XMR price here
        
        return Cache::remember('xmr_price', 240, function () {
            
            // API PRICE FETCHING VIA TOR (Comment out this entire section if using manual price above)
            // ====================================================================
            
            // Create client with Tor SOCKS5 proxy configuration
            $client = new Client([
                'proxy' => [
                    'http' => 'socks5h://127.0.0.1:9050',
                    'https' => 'socks5h://127.0.0.1:9050',
                ],
                'timeout' => 30, // Timeout for Tor
                'connect_timeout' => 15,
                'verify' => true, // SSL verification
                'curl' => [
                    CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5_HOSTNAME,
                ]
            ]);
            
            // First, try CoinGecko API
            try {
                $response = $client->request('GET', 'https://api.coingecko.com/api/v3/simple/price', [
                    'query' => [
                        'ids' => 'monero',
                        'vs_currencies' => 'usd',
                    ],
                    'timeout' => 25,
                ]);
                $data = json_decode($response->getBody(), true);
                if (isset($data['monero']['usd'])) {
                    return number_format($data['monero']['usd'], 2, '.', '');
                }
            } catch (\Exception $e) {
            }
            
            // If CoinGecko fails, try CryptoCompare API
            try {
                $response = $client->request('GET', 'https://min-api.cryptocompare.com/data/price', [
                    'query' => [
                        'fsym' => 'XMR',
                        'tsyms' => 'USD',
                    ],
                    'timeout' => 25,
                ]);
                $data = json_decode($response->getBody(), true);
                if (isset($data['USD'])) {
                    return number_format($data['USD'], 2, '.', '');
                }
            } catch (\Exception $e) {
            }
            
            // Only log when all APIs fail - this is the critical error
            Log::error('All XMR price APIs failed via Tor');
            return 'UNAVAILABLE';
            // ====================================================================
            // END API PRICE FETCHING
            
        });
    }
}
