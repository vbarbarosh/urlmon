<?php

namespace App\Models;

use App\Helpers\Traits\Cast;
use App\Helpers\Traits\Q;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $uid
 * @property $status
 * @property $subject
 * @property $request
 * @property $response
 * @property $user_friendly_status
 * @property $user_friendly_response
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Promise extends Model
{
    use HasFactory, Cast, Q;

    const STATUS_PENDING = 'pending';
    const STATUS_REJECTED = 'rejected';
    const STATUS_FULFILLED = 'fulfilled';

    protected $hidden = [
        'id',
        'request',
        'response',
    ];

    protected $casts = [
        'request' => 'array',
        'response' => 'array',
        'user_friendly_response' => 'array',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->uid = uid_promise();
    }

    public function replicate(array $except = null): self
    {
        $out = parent::replicate($except);
        $out->uid = uid_promise();
        return $out;
    }
}
