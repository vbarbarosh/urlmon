<?php

namespace App\Helpers\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait Q
{
    public function q(): EloquentBuilder
    {
        return $this->query()->where($this->qualifyColumn('uid'), $this->uid)->limit(1);
    }
}
