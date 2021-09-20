<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Query Builder
        DB::connection('mysql')->table('categories')->insert([
            'name' => 'First Category',
            'slug' => 'first-category',
            'status' => 'active'
        ]);
        // SQL Statement
        /*DB::statement("INSERT INTO  categories (name , slug , status)
             VALUES('First Category', 'first-category', 'active')");*/

        // Query Builder
        for ($i = 1; $i <= 10; $i++) {
            DB::table('categories')->insert([
                'name' => 'Category ' . $i,
                'slug' => 'category-' . $i,
                'status' => 'active'
            ]);
        }

        // ORM Eloquent Model
        Category::create([
            'name' => 'Category Model',
            'slug' => 'category-model',
            'status' => 'draft',
        ]);



    }
}
