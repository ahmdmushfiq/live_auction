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
            $product = Product::find($request->product_id);
            if (Carbon::now()->greaterThan($product->end_time)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'The auction has ended.',
                ]);
            }

            if ($request->amount <= $product->current_price) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your bid must be higher than the current price.',
                ]);
            }

            $previousHighestBid = $product->highestBid();
            $previousHighestBidderId = $previousHighestBid ? $previousHighestBid->user_id : null;

            $product->current_price = $request->amount;
            if (Carbon::now()->diffInMinutes($product->end_time) <= 2) {
                $product->end_time = Carbon::parse($product->end_time)->addMinutes(2);
            }
            $product->save();


            $bid = new Bid();
            $bid->user_id = Auth::user()->id;
            $bid->product_id = $request->product_id;
            $bid->amount = $request->amount;
            $bid->save();

            // Trigger the event
            event(new BidPlaced($bid, $previousHighestBidderId, ));

            return response()->json([
                'status' => 'success',
                'message' => 'Bid placed successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
