<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $products = Product::with('category')->latest()->get();

            $products->each(function ($product) {
                $product->image_url = $product->getFirstMediaUrl('product_image');
            });

            return Datatables::of($products)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $btn = '<a class="btn btn-sm btn-primary text-white mx-2 editCategory" href="javascript:void(0)"
                        data-id="' . $row->id . '">Edit</a>';

                    $btn = $btn . '<a class="btn btn-sm btn-danger text-white mx-2 deleteCategory" href="javascript:void(0)"
                        data-id="' . $row->id . '">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.product');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['slug'] = Str::slug($validatedData['name']);
        $validatedData['created_by'] = Auth::id();

        $imageName = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $imageName = 'product-image' . md5(uniqid()) . time() . '.' . $extension;
        }

        $product = Product::create($validatedData);

        if ($imageName) {
            $product->addMediaFromRequest('image')->usingFileName($imageName)->toMediaCollection('product_image');
        }

        return response()->json($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
