<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
class User2 extends Model implements AuthenticatableContract
{
    use HasApiTokens, Notifiable, Authenticatable, CanResetPassword;
    protected $table= 'user';
    public $timestamps = false;
    protected $guard= 'user';
    protected $primaryKey = 'user_id';
    public function setPasswordAttribute($value)
{
    if( \Hash::needsRehash($value) ) {
        $value = \Hash::make($value);
    }
    $this->attributes['password'] = $value;
} 
}
