<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case WaitingOnCustomer = 'waiting_on_customer';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In progress',
            self::WaitingOnCustomer => 'Waiting on customer',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::Open, self::InProgress, self::WaitingOnCustomer], true);
    }
}
