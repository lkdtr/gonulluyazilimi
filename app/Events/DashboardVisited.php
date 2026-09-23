<?php

namespace App\Events;

use App\Models\User;

class DashboardVisited
{
    public function __construct(public User $user)
    {
    }
}
