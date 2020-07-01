<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tweets()
    {
        return $this->hasMany('App\Tweet');
    }

    public function timeline()
    {
        // include the user's and followeds' tweets

        $followed_ids = $this->follows()->pluck('id');
        return Tweet::whereIn('user_id', $followed_ids)->orWhere('user_id', $this->id)->latest()->get();
    }

    public function getAvatar()
    {
        return 'https://i.pravatar.cc/50?u=' . $this->email;
    }
    public function follow(User $user)
    {
        return $this->follows()->save($user);
    }
    public function follows()
    {
        return $this->belongsToMany('App\User', 'follows', 'user_id', 'followed_user_id')->withTimestamps();
    }
}
