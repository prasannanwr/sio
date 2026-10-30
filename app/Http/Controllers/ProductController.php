<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function showcase()
    {
            $products = Product::paginate(15);
            return view('products_page', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_per_tonne' => 'required|numeric',
            'price_per_kg' => 'required|numeric',
            'origin_port' => 'required|string|max:255',
            'supported_ports' => 'required|string',
            'shipping_terms' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = new Product($request->except('_token'));

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        return redirect()->route('products.index')->with(['message', 'Product created successfully.', 'message_type' => 'success']);
    }

    public function edit(Product $product)
    {
        // dd($product);
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price_per_tonne' => 'required|numeric',
            'price_per_kg' => 'required|numeric',
            'origin_port' => 'required|string|max:255',
            'supported_ports' => 'required|string',
            'shipping_terms' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product->fill($request->except('_token'));

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $product->image = $imageName;
        }

        $product->save();

        return redirect()->route('products.index')->with(['message', 'Product updated successfully.', 'message_type' => 'success']);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with(['message', 'Product deleted successfully.', 'message_type' => 'success']);
    }
}
