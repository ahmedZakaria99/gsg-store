<?php

namespace App\Models;

use App\Scopes\ActiveStatusScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_ACTIVE = 'active';
    const STATUS_DRAFT = 'draft';

    protected $fillable = [
        'name', 'slug', 'description', 'image_path', 'price', 'sale_price',
        'quantity', 'weight', 'width', 'height', 'length', 'status',
        'category_id',
    ];
    protected $perPage = 5; //by default 15

    public static function validateRules()
    {
        return [
            'name' => 'required|max:255',
            'category_id' => 'required|int|exists:categories,id',
            'description' => 'nullable',
            'image' => 'nullable|image|dimensions:min_width=300,min_height=300',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'quantity' => 'nullable|int|min:0',
            'sku' => 'nullable|unique:products,sku',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'status' => 'in:' . self::STATUS_ACTIVE . ',' . self::STATUS_DRAFT,
        ];
    }

    protected $appends = [
        'image_url',
        'permalink'
    ];

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return asset('images/placeholder.png'); // any path you want
        }
        if (stripos($this->image_path, 'http') === 0) {
            return $this->image_path;
        }
        return asset('storage/' . $this->image_path);
    }

    public function getPermalinkAttribute()
    {
        return route('product.details', $this->slug);
    }

    // Mutators: set{AttributeName}Attribute
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::title($value);
        $this->attributes['slug'] = Str::slug($value);
    }

    // Scope
    protected static function booted()
    {
        // if you want except this scope for any statement
        // you can use withoutGlobalScope('ScopeName'); or
        // use withoutGlobalScopes(); for all of scopes
        // use withoutGlobalScopes(['ScopeName1','ScopeName2',...]); for some of scopes
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('products.status', '=', 'active');
        });
        // And you can use custom global scope by make scope class like laravel when made softDeletes.
        // if you want except this scope for any statement
        // you can use withoutGlobalScope(NameClass) or
        // use withoutGlobalScopes(); for all of scopes
        // use withoutGlobalScopes([NameClass1,NameClass2,...]); for some of scopes
        static::addGlobalScope(new ActiveStatusScope());
    }

    // local Scope: scope{Name}
    // ->name()
    protected function scopeActive(Builder $builder)
    {
        $builder->where('status', '=', 'active');
    }

    protected function scopePrice(Builder $builder, $from, $to)
    {
        $builder->whereBetween('price', [$from, $to]);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'product_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable', 'rateable_type', 'rateable_id', 'id');
    }
}
