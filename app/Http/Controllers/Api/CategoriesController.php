<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Category::when($request->query('parent_id'), function ($query, $value) {
            $query->where('parent_id', '=', $value);
        })->paginate();
    }


    public function store(Request $request)
    {
        //$user = Auth::guard('sanctum')->user();
//        if (!$request->user()->tokenCan('categories.create')){
//            abort(403,'Not allowed');
//        }
        try {
            $request->validate([
                'name' => 'required',
                'parent_id' => 'nullable|int|exists:categories,id'
            ]);
            $category = Category::create($request->all());
            $category->refresh(); // To get all attributes from DB

            // return $category;
            //return \response()->json($category,201);
//        return new JsonResponse($category, 201, [
//            'x-application-name' => config('app.name') // [Custom header]
//        ]);
            return Response::json($category, 201, [
                'x-application-name' => config('app.name') // [Custom header]
            ]);
        } catch (\Exception $exception) {
            return Response::json("The name is already exists", 409);
        }

    }

    public function show($id)
    {
        return Category::with('children')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|required',
            'parent_id' => 'nullable|int|exists:categories,id'
        ]);
        $category = Category::findOrFail($id);
        $category->update($request->all());

        return Response::json([
            'message' => "$category->name Category Updated",
            'categories' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return Response::json([
            'message' => "$category->name Category Deleted"
        ]);
    }
}
