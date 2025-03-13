<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashBoardController extends Controller
{
    public function index()
    {
        $products = Product::orderByRaw("(end_time > ?) DESC, end_time ASC", [Carbon::now()])->get();
        return view('dashboard', compact('products'));
    }

    public function bidsShow($id){
        $product = Product::with('bids.user')->findOrFail($id);
        $bidders = Bid::where('product_id', $id)
              ->join('users', 'users.id', '=', 'bids.user_id')
              ->select('bids.user_id', 'users.name', DB::raw('MAX(bids.amount) as top_bid'))
              ->groupBy('bids.user_id', 'users.name')
              ->get();
        return view('bidder.index', compact('bidders', 'product'));
        
    }

    public function bidderBids(Request $request){
        $bids = Bid::where('user_id', $request->bidderId)->where('product_id', $request->productId)
        ->select('amount')
        ->get();
        return response()->json([
            'status' => 'success',
            'data' => $bids
        ]);
    }
}
