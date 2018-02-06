<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPassword as ResetPasswordNotification;

class User extends Authenticatable
{
    use SoftDeletes;
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

    public function getDates()
	{
		return array(
			'created_at',
			'updated_at',
			'deleted_at',
		);
    }
    
    public static function boot()
	{
		parent::boot();

        static::created(function($element) {
            cache()->tags('users')->flush();
        });

        static::saved(function($element) {
            cache()->tags('users')->flush();
        });

        static::deleted(function($element) {
            cache()->tags('users')->flush();
        });
    }

    /* Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
