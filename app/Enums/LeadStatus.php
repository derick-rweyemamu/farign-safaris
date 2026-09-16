<?php

namespace App\Enums;

enum LeadStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case PROPOSAL_SENT = 'proposal_sent';
    case QUALIFIED = 'qualified';
    case WON = 'won';
    case LOST = 'lost';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'New',
            self::CONTACTED => 'Contacted',
            self::PROPOSAL_SENT => 'Proposal Sent',
            self::QUALIFIED => 'Qualified',
            self::WON => 'Won',
            self::LOST => 'Lost',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NEW => 'blue',
            self::CONTACTED => 'amber',
            self::PROPOSAL_SENT => 'purple',
            self::QUALIFIED => 'indigo',
            self::WON => 'green',
            self::LOST => 'red',
        };
    }
}
