<?php

namespace App\Http\Controllers;

use App\Events\BidPlaced;
use App\Http\Requests\PlaceBidRequest;
use App\Models\Bid;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BidController extends Controller
{
    public function placeBid(PlaceBidRequest $request)
    {
        try {
            $product = Product::findOrFail($request->product_id);

            $this->validateBid($request, $product);

            return $this->handleBidProcessing($request, $product);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Validate bid conditions before placing it.
     */
    private function validateBid($request, $product)
    {
        // Check if the auction has ended
        if (Carbon::now()->greaterThan($product->end_time)) {
            throw new \Exception('The auction has ended.');
        }

        // Check if the bid amount is higher than the current price
        if ($request->amount <= $product->current_price) {
            throw new \Exception('Your bid must be higher than the current price.');
        }

        // Check if the user has already placed a bid
        if (Bid::where('user_id', Auth::user()->id)
            ->where('product_id', $request->product_id)
            ->where('amount', $request->amount)
            ->exists()
        ) {
            throw new \Exception('You have already placed this bid.');
        }

        // Check if the user is bidding too fast
        $lastBid = Bid::where('user_id', Auth::user()->id)
            ->where('product_id', $request->product_id)
            ->latest()
            ->first();

        if ($lastBid && $lastBid->created_at->diffInSeconds(Carbon::now()) < 5) {
            throw new \Exception('You are bidding too fast. Please wait a few seconds before placing another bid.');
        }
    }

    /**
     * Process bid after validation.
     */
    private function handleBidProcessing($request, $product)
    {
        $previousHighestBid = $product->highestBid();
        $previousHighestBidderId = $previousHighestBid ? $previousHighestBid->user_id : null;

        $product->current_price = $request->amount;
        if (Carbon::now()->diffInMinutes($product->end_time) <= 2) {
            $product->end_time = Carbon::parse($product->end_time)->addMinutes(2);
        }
        $product->save();

        $bid = new Bid();
        $bid->user_id = Auth::id();
        $bid->product_id = $request->product_id;
        $bid->amount = $request->amount;
        $bid->save();

        event(new BidPlaced($bid, $previousHighestBidderId));

        return response()->json([
            'status' => 'success',
            'message' => 'Bid placed successfully.',
        ]);
    }
}
