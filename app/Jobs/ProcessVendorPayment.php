<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Orders;

class ProcessVendorPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Orders $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        if (!$this->order->vendor_payment_amount) {
            $this->order->processVendorPayment();
        }
    }
}
