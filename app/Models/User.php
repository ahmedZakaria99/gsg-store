<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hasAbility($ability)
    {
        /* $roles = Role::whereHas('users', function ($query) {
                $query->where('id', '=', Auth::id());
            })->get();
        return $roles;
        */
        $roles = Role::whereRaw('roles.id IN (SELECT role_id FROM role_user WHERE user_id = ?)', [
            $this->id,
        ])->get();
        foreach ($roles as $role) {

            if (in_array($ability, $role->abilities)) {
                return true;
            }
        }
        return false;
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id')->withDefault([
            'address' => 'Not found'
        ]);
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_user',
            'user_id',
            'role_id',
            'id',
            'id'
        );
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'user_id', 'id')
            ->withDefault();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable', 'rateable_type', 'rateable_id', 'id');
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function routeNotificationForMail($notification = null)
    {
        return $this->email;
    }

    public function routeNotificationForFcm($notification = null)
    {
        return $this->deviceTokens()->pluck('token')->toArray();
    }
}
