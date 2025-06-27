<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\XmrPriceController;

class HomeController extends Controller
{
    /**
     * Show the home page.
     *
     * @return \Illuminate\View\View
     */
    public function index(XmrPriceController $xmrPriceController)
    {
        // Get active popup (always show if available)
        $popup = \App\Models\Popup::getActive();
        
        // Get active advertisements
        $advertisements = \App\Models\Advertisement::getActiveAdvertisements();
        
        // Get current XMR price for conversion
        $xmrPrice = $xmrPriceController->getXmrPrice();
        
        // Organize advertisements by slot, skipping ads with deleted products
        $adSlots = [];
        foreach ($advertisements as $ad) {
            // Skip advertisements where product is soft-deleted
            if (!$ad->product || $ad->product->trashed()) {
                continue;
            }
            
            // Get the formatted measurement unit
            $measurementUnits = \App\Models\Product::getMeasurementUnits();
            $formattedMeasurementUnit = $measurementUnits[$ad->product->measurement_unit] ?? $ad->product->measurement_unit;
            
            // Format product price in XMR
            $productXmrPrice = (is_numeric($xmrPrice) && $xmrPrice > 0) 
                ? $ad->product->price / $xmrPrice 
                : null;
            
            // Get formatted options with XMR price
            $formattedBulkOptions = $ad->product->getFormattedBulkOptions($xmrPrice);
            $formattedDeliveryOptions = $ad->product->getFormattedDeliveryOptions($xmrPrice);
            
            $adSlots[$ad->slot_number] = [
                'product' => $ad->product,
                'vendor' => $ad->product->user,
                'ends_at' => $ad->ends_at,
                'measurement_unit' => $formattedMeasurementUnit,
                'xmr_price' => $productXmrPrice,
                'bulk_options' => $formattedBulkOptions,
                'delivery_options' => $formattedDeliveryOptions
            ];
        }
        
        return view('home', [
            'username' => Auth::user()->username,
            'popup' => $popup,
            'adSlots' => $adSlots
        ]);
    }
}
