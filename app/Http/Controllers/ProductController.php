<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $products = Product::latest();
            return DataTables::eloquent($products)
                ->addIndexColumn()
                ->addColumn('name', function ($product) {
                    return $product->name;
                })
                ->addColumn('description', function ($product) {
                    return $product->description;
                })
                ->addColumn('starting_price', function ($product) {
                    return $product->starting_price;
                })
                ->addColumn('end_time', function ($product) {
                    return $product->end_time;
                })
                ->addColumn('image', function ($product) {
                    if ($product->product_image) {
                        $media = $product->getFirstMediaUrl('product_image'); 
                        if ($media) {
                            return '<img src="' . $media . '" border="0" width="60" class="img-rounded" align="center" />';
                        }
                        return 'No Image';
                    }
                    return 'No Image';
                })
                ->addColumn('action', function ($product) {
                    return '
                    <a href="javascript:void(0)" class="btn btn-primary btn-sm edit-btn" data-id="' . $product->id . '">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger btn-sm delete-btn" data-id="' . $product->id . '">Delete</a>
                ';
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        }
        return view('products.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->starting_price = $request->starting_price;
            $product->current_price = $request->starting_price;
            $product->end_time = $request->end_time;
            $product->save();
            if ($request->image) {
                $product->addMediaFromBase64(json_decode($request->image)->data)->usingFileName(Str::random() . '.jpeg')->toMediaCollection('product_image');
            }
            return redirect()->back()->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::find($id);
        $image = $product->getFirstMediaUrl('product_image');
        return response()->json([
            'status' => 'success',
            'data' => $product,
            'product_image' => $image
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, string $id)
    {
        try {
            $product = Product::find($request->product_id);
            $product->name = $request->name;
            $product->description = $request->description;
            $product->starting_price = $request->starting_price;
            $product->end_time = $request->end_time;
            $product->save();
            $product->clearMediaCollection('product_image');
            if ($request->image) {
                $product->addMediaFromBase64(json_decode($request->image)->data)->usingFileName(Str::random() . '.jpeg')->toMediaCollection('product_image');
            }
            return redirect()->back()->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        $product->clearMediaCollection('product_image');
        $product->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }
}
