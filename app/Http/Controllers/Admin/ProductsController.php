<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$this->authorize('view-any', Product::class);
        $products = Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->select([
                'products.*',
                'categories.name as category_name'
            ])
            ->paginate();

        //->paginate(5, ['*'], 'page', 5);
        //->simplePaginate(); // If you don't care about number of pages
        $session = session()->get('success');
        session()->forget('success');
        return view('admin.products.index', [
            'products' => $products,
            'session' => $session
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::pluck('name', 'id');
        //dd($categories);
        return view('admin.products.create', [
            'categories' => $categories,
            'product' => new Product(),
            'button' => 'Save'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(Product::validateRules());
        /*$request->merge([
            'slug' => Str::slug($request->post('name'))
        ]);*/
        $product = Product::create($request->all());

        return redirect()->route('products.index')
            ->with('success', "Product ($product->name) created.");

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        //$this->authorize('view', $product);
        return view('admin.products.show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::withTrashed()->pluck('name', 'id'),
            'button' => 'Update'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate(Product::validateRules());
        if ($request->hasFile('image')) {
            $file = $request->file('image'); // UploadedFile Object
            // $file->getClientOriginalName(); // Return file name
            // $file->getClientOriginalExtension();
            // $file->getClientMimeType(); // audio/mp3
            // $file->getType();
            // $file->getSize();

            // Filesystem - Disks: config/filesystems
            // local: storage/app => by default
            // public: storage/app/public
            // s3: Amazon Drive
            // custom: defined by us!
            $image_path = $file->store('uploads/products', [
                'disk' => 'public'
            ]);

            $request->merge([
                'image_path' => $image_path,
            ]);
        }
        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', "Product ($product->name) updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        //Storage::disk('public')->delete($product->image_path);

        return redirect()->route('products.index')
            ->with('success', "Product ($product->name) deleted.");
    }

    public function trash()
    {
        $products = Product::onlyTrashed()->paginate();
        return view('admin.products.trash', [
            'products' => $products
        ]);
    }

    public function restore(Request $request, $id = null)
    {
        if ($id) {
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->restore();
            return redirect()->route('products.index')
                ->with('success', "Product ($product->name) restored.");
        }
        Product::onlyTrashed()->restore();
        return redirect()->route('products.index')
            ->with('success', "All trashed products restored.");
    }

    public function forceDelete(Request $request, $id = null)
    {
        if ($id) {
            $product = Product::onlyTrashed()->findOrFail($id);
            $product->forceDelete();
            return redirect()->route('products.index')
                ->with('success', "Product ($product->name) deleted forever.");
        }
        Product::onlyTrashed()->forceDelete();
        return redirect()->route('products.index')
            ->with('success', "All trashed products deleted forever.");
    }
}
