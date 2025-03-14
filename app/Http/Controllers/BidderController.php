<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BidderController extends Controller
{
    public function index(Request $request)
    {
        if(request()->ajax()) {
            $bidders = User::where('role', 'bidder')->latest();
            return DataTables::eloquent($bidders)
                ->addIndexColumn()
                ->addColumn('name', function ($bidder) {
                    return $bidder->name;
                })
                ->addColumn('email', function ($bidder) {
                    return $bidder->email;
                })
                ->addColumn('no_of_products_bidded', function ($bidder) {
                    $product = Bid::where('user_id', $bidder->id)
                        ->distinct()
                        ->count('product_id');
                    return $product;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('search')) {
                        $search = $request->search['value'];
                        $query->where(function ($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                    }   
                })
                ->toJson();
        }
        return view('bidder.all-bidders');
    }
}
