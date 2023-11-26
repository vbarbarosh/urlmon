<?php

namespace Database\Seeders;

use App\Models\User;

class UserSeeder
{
    public function run()
    {
        User::register('hello', 'hello', function (User $user) {
            $user->is_debug_eval_enabled = true;
        });
    }
}
