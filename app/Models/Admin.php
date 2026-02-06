<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Admin extends Model implements CanResetPassword, AuthenticatableContract
{
    use CanResetPasswordTrait, Notifiable, Authenticatable;

    protected $table = 'admin';
    public $timestamps = false;

    protected $fillable = [
        'prefix',
        'username',
        'password',
        'lastname',
        'firstname',
        'middlename',
        'role',
        'email',
    ];

    /**
     * Get the e-mail address where password reset links are sent.
     * Must return a valid email so the reset URL contains ?email=...
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Send the password reset notification using the custom notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Find admin by email for password reset
     */
    public static function findForPasswordReset($email)
    {
        return static::where('email', $email)->first();
    }

    /**
     * Get the password attribute name for authentication
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->username;
    }

    public function getFullNameAttribute()
    {
        $fullName = $this->prefix . ' ' . $this->lastname . ', ' . $this->firstname;

        if (!empty($this->middlename)) {
            // Take only the first letter of middlename and add a dot
            $fullName .= ' ' . strtoupper(substr($this->middlename, 0, 1)) . '.';
        }

        return $fullName;
    }


    public function getIsCurrentUserAttribute()
    {
        return $this->id === session('admin_id');
    }

    public function getCanBeDeletedAttribute()
    {
        return !$this->is_current_user && Admin::count() > 1;
    }

    // admin logo initials
    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->firstname, 0, 1) . substr($this->lastname, 0, 1));
    }
}
