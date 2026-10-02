<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->isStaff() || $ticket->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return ! $user->isStaff();
    }

    public function reply(User $user, Ticket $ticket): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $ticket->user_id === $user->id && $ticket->status->isActive();
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->isStaff();
    }
}
