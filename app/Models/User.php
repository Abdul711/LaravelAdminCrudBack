<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable
{
    use CrudTrait,HasRoles;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
        public function getRowNumber()
    {
        $page = request()->get('page', 1);
        $perPage = request()->get('limit', config('backpack.crud.default_page_length', 25));
        static $i = 1;
        return (($page - 1) * $perPage) + $i++;
    }
    //    public function setPicAttribute($value)
    // {
    //     if (request()->hasFile('pic')) {
    //         $file = request()->file('pic');

    //         // Format filename: UserName.extension
    //         $name = $this->attributes['name'] ?? 'user'; // fallback if name is missing
    //         $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $name); // clean the name
    //         $extension = $file->getClientOriginalExtension();
    //         $filename = $safeName . '.' . $extension;

    //         $destination = public_path('profilepic');

    //         if (!file_exists($destination)) {
    //             mkdir($destination, 0775, true);
    //         }

    //         // Optionally delete old file
    //         if (!empty($this->attributes['pic'])) {
    //             $old = public_path($this->attributes['pic']);
    //             if (file_exists($old)) {
    //                 @unlink($old);
    //             }
    //         }

    //         // Move file
    //         $file->move($destination, $filename);

    //         // Save relative path in DB
    //         $this->attributes['pic'] =  $filename;
    //     }
    // }
    protected static function booted()
{
    static::creating(function ($user) {
        if (backpack_user()) {
            // If user was created by an admin
            if (backpack_user()->hasRole('admin')) {
                $user->assignRole('user'); // Created by admin → role = user
            }
        } else {
            // Registered directly (no logged-in user)
            $user->assignRole('admin'); // Self-registration → role = admin
        }
    });
}


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
