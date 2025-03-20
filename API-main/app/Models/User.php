<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function getAllUsers()
    {
        return self::all();
    }

    public static function addUser($name, $email, $role, $password)
    {
        return self::create([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password' => bcrypt('defaultpassword')
        ]);
    }

    public static function updateUser($id, $name, $email, $role)
    {
        $user = self::find($id);
        if (!$user) {
            throw new \Exception('User not found');
        }

        $user->name = $name;
        $user->email = $email;
        $user->role = $role;
        $user->save();

        return $user;
    }

    public static function deleteUser($id)
    {
        $user = self::find($id);
        if (!$user) {
            return false; // Trả về false thay vì ném lỗi
        }
        return $user->delete();
    }
}