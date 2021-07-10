<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoriesController extends Controller
{
    // ReflectionClass //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
         * SELECT categories.*,parents.name as parent_name FROM
         * categories JOIN categories as parents
         * ON parents.id = categories.parent_id
         * WHERE categories.status = active
         * ORDER BY categories.id DESC , categories.name ASC
         */
        $categories = Category::join('categories as parents', 'parents.id', '=', 'categories.parent_id')
            ->select([
                'categories.*',
                'parents.name as parent_name'
            ])
            ->where('categories.status', '=', 'active')
            ->orderByDesc('categories.id')
            ->orderBy('categories.name', 'ASC')
            ->get();

        // Read from session
        // Session::get();
        $success = session()->get('success');
        //session('success');
        // Delete from session
        //Session::forget('success');
        session()->forget('success');

        return view('admin.categories.index', [
            'categories' => $categories,
            'title' => 'Categories List',
            'success' => $success
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $parents = Category::all();
        $category = new Category();
        $button = 'Save';
        return view('admin.categories.create', compact('parents', 'category', 'button'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryRequest $request)
    {
        /* if the validation was right then it return array of ['field name' => value of this field] and
        if the validation was error then it return exception and laravel already handled it using by
        redirect to back and send collection variable its name is errors.
        $rules = [
            'name' => 'required|string|min:3|max:255|unique:categories,name',
            'parent_id' => 'nullable|int|exists:categories,id',
            'description' => 'nullable|min:5',
            'status' => 'required|in:active,draft',
            'image' => 'image|max:512000|dimensions:min_width=300,min_height=300'
        ];*/

        # Method #1
        //$clean = $request->validate($rules);

        # Method #2
        //$clean = $this->validate($request,$rules);
        # Method #3 (More details because it show what happened in laravel exactly)
        /*$data = $request->all();
        $validator = Validator::make($data,$rules);
        try {
            $clean = $validator->validate();
        } catch (ValidationException $e) {
            //
        }
        if ($validator->fails()){
            return  redirect()->back()->withErrors($validator);
        }*/

        /* Request Merge */
        $request->merge([
            'slug' => Str::slug($request->post('name')),
            'status' => 'active',
        ]);
        /* return array of all form fields */
        // $request->all();

        /* return single field value */
        // $request->name;
        // $request->input('name');
        // $request->get('name');
        // $request->post('_token');   // Only if the request method is post
        // $request->query('_token'); // Only if the request method is get (from URL) ?name=value

        /* Method #1 */
        // $category = new Category();
        // $category->name = $request->post('name');
        // $category->slug = Str::slug($request->post('name'));
        // $category->parent_id = $request->post('parent_id');
        // $category->description = $request->post('description');
        // $category->status = $request->post('status', 'active');
        // $category->save();

        /* Method #2 (Mass Assignment) */
        //$category = Category::create([
        // 'name' => $request->post('name'),
        // 'slug' => Str::slug($request->post('name')),
        // 'parent_id' => $request->post('parent_id'),
        // 'description' => $request->post('description'),
        // 'status' => $request->post('status', 'active'),
        //]);

        $category = Category::create($request->all());


        /* Method #3 (Mass Assignment) */
        //$category = new Category([
        // 'name' => $request->post('name'),
        // 'slug' => Str::slug($request->post('name')),
        // 'parent_id' => $request->post('parent_id'),
        // 'description' => $request->post('description'),
        // 'status' => $request->post('status', 'active'),
        //]);
        // $category->save();

        //PRG (post redirect get)
        return redirect()->route('category.index')
            ->with('success', 'Category Created');

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::find($id);
        /* This mean => $category = Category::where('id','=',$id)->first();
        'id' based on primary key in categories table
        So if the primary key was 'name' then Category::find($id);
        will be Category::where('name','=',$id)->first(); */
        $parents = Category::where('id', '<>', $category->id)->get();

        return view('admin.categories.edit')->with([
            'category' => $category,
            'parents' => $parents,
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
        $request->validate([
            'name' => 'required|string|min:3|max:255|unique:categories,name,' . $id,
            'parent_id' => 'nullable|int|exists:categories,id',
            'description' => 'nullable|min:5',
            'status' => 'required|in:active,draft',
            'image' => 'image|max:512000|dimensions:min_width=300,min_height=300'
        ]);
        $category = Category::find($id);
        // Method #1
        /*$category->name = $request->post('name');
        $category->parent_id = $request->post('parent_id');
        $category->description = $request->post('description');
        $category->status = $request->post('status');
        $category->save();*/

        // Method #2: Mass Assignment
        $category->update($request->all());

        # Method #3: Mass Assignment
        //$category->fill( $request->all() )->save();

        # Method #4: Mass Assignment
        //Category::where('id', '=', $id)->update( $request->all() );

        //PRG (post redirect get)
        return redirect()->route('category.index')
            ->with('success', 'Category Updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Method #1
        $category = Category::find($id);
        $category->delete();

        //Method #2
        Category::destroy($id);

        //Method #3
        Category::where('id', '=', $id)->delete();

        // Write into session
        //Session::put('success','Category Deleted');
        session()->put('success', 'Category Deleted');
        //session([
        //     'success' => 'Category Deleted'
        // ]);

        //Actually this is method to write into session but
        // when we read from session
        // Laravel will drop this key from session ^_^
        /* session()->flash('success', 'Category Deleted'); */

        //PRG (post redirect get)
        return redirect()->route('category.index');
        // return redirect()->route('category.index')->with('success', 'Category Deleted'); // flash
    }
}
