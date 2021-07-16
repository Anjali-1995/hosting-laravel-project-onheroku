<?php

namespace App\Jobs;

use App\Inventory;
use App\Model\OrderDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DecrementFromInventory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $branch,$order;
    public function __construct($branch,$order)
    {
       
        $this->branch = $branch;
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
       $details = OrderDetail::where('order_id',$this->order->id)->get();
       
        foreach($details as $detail){
            $productId = $detail['product_id'];
           
            $quantity = $detail['quantity'];

            $inventory = Inventory::where('productId',$productId)
                                    ->where('branchId',$this->branch['id'])
                                    ->get()
                                    ->first();
            
            $inventory->decrementCount($quantity);
        }

    }
}
