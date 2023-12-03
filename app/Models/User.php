<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Exceptions\NotAuthorized;
use App\Exceptions\UserFriendlyException;
use App\Helpers\Traits\Cast;
use App\Helpers\Traits\Q;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property $id
 * @property $uid
 * @property $name
 * @property $email
 * @property $password
 * @property $remember_token
 * @property $is_debug_eval_enabled
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $email_verified_at
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Cast, Q;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_debug_eval_enabled' => 'boolean',
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    /**
     * @throws UserFriendlyException
     */
    static public function register($email, $password, callable $fn = null): User
    {
        if (User::query()->where('email', $email)->exists()) {
            throw new UserFriendlyException("User with the specified email already exists: [$email]");
        }
        $user = new User();
        $user->email = $email;
        $user->password_set($password);
        if (isset($fn)) {
            call_user_func($fn, $user);
        }
        $user->save();
        return $user;
    }

    /**
     * @throws Exception
     */
    static public function remove($query): int
    {
        safety_check_query_for_batch_remove($query);

        return $query->pluck('users.id')->chunk(100)->sum(function ($ids) {
            return User::query()->whereIn('id', $ids)->delete();
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->uid = uid_user();
    }

    public function replicate(array $except = null): self
    {
        $out = parent::replicate($except);
        $out->uid = uid_user();
        return $out;
    }

    public function should_allow_debug_eval(): void
    {
        if (!$this->is_debug_eval_enabled) {
            throw new NotAuthorized();
        }
    }

    public function password_assert_valid($password): void
    {
        if (!$this->password_check($password)) {
            throw new UserFriendlyException('Invalid password');
        }
    }

    public function password_assert_different($password): void
    {
        if ($this->password_check($password)) {
            throw new UserFriendlyException('New password matched the old one. Please use a different password.');
        }
    }

    public function password_check($password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function password_set($plain): void
    {
        $this->password = Hash::make($plain);
    }
}
