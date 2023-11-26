<?php

namespace App\Exceptions;

// https://serverfault.com/a/57082/323502
// > Authentication is the process of verifying who you are.
// > When you log on to a PC with a username and password
// > you are authenticating.
// >
// > Authorization is the process of verifying that you have
// > access to something. Gaining access to a resource (e.g.
// > directory on a hard disk) because the permissions
// > configured on it allow you access is authorization.

class NotAuthorized extends UserFriendlyException
{
    public function __construct($title = 'You don\'t have permissions to perform this action')
    {
        parent::__construct($title);
    }
}
