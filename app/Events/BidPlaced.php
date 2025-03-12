<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BidPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bid;
    public $previousHighestBidderId; 
    public $productName;   
    public $new_end_time; 

    /**
     * Create a new event instance.
     */
    public function __construct(Bid $bid, $previousHighestBidderId)
    {
        $this->bid = $bid;
        $this->previousHighestBidderId = $previousHighestBidderId;
        $this->new_end_time = $bid->product->end_time;
    }

    public function broadcastOn()
    {
        
        return new Channel('bids');
    }

    public function broadcastAs()
    {
        
        return 'new-bid';
    }
    public function broadcastWith()
    {
        return [
            'bid' => $this->bid,
            'previous_highest_bidder' => $this->previousHighestBidderId,
            'product_name' => $this->bid->product->name,
            'new_end_time' => $this->new_end_time
        ];
    }

}
