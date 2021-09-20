<?php

namespace Database\Factories;


use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->words(2,true);
        $slug = Str::slug($name);
        $category = DB::table('categories')
            ->inRandomOrder()
            ->limit(1)
            ->first(['id']);
        $description = $this->faker->words(200,true);
        $image_path = $this->faker->imageUrl();
        $status = ['active','draft'];
        return [
            'name' => $name,
            'slug' => $slug,
            'parent_id' => $category ? $category->id : null,
            'description' => $description,
            'image_path' => $image_path,
            'status' => $status[rand(0,1)]
        ];
    }
}
