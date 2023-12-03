<?php

namespace App\Models;

use App\Helpers\Classes\FrontendArray;
use App\Helpers\Traits\Cast;
use App\Helpers\Traits\Q;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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

    static public function frontend_list($query): Collection
    {
        return $query->get()->map(function (Promise $promise) {
            return new FrontendArray($promise->id, [
                'uid' => $promise->uid,
                'status' => $promise->status,
                'user_friendly_status' => $promise->user_friendly_status,
                'created_at' => $promise->created_at->toAtomString(),
                'updated_at' => $promise->updated_at->toAtomString(),
            ]);
        });
    }

    static public function frontend_fetch($query): Collection
    {
        return $query->get()->map(function (Promise $promise) {
            return new FrontendArray($promise->id, [
                'uid' => $promise->uid,
                'status' => $promise->status,
                'user_friendly_status' => $promise->user_friendly_status,
                'user_friendly_response' => $promise->user_friendly_response,
                'created_at' => $promise->created_at->toAtomString(),
                'updated_at' => $promise->updated_at->toAtomString(),
            ]);
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->uid = uid_promise();
        $this->status = Promise::STATUS_PENDING;
    }

    public function replicate(array $except = null): self
    {
        $out = parent::replicate($except);
        $out->uid = uid_promise();
        return $out;
    }
}
