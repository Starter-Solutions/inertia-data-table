<?php

namespace App\Models;

use Database\Factories\SupportTicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['subject', 'requester_email', 'priority', 'is_resolved', 'last_reply_at'])]
class SupportTicket extends Model
{
    /** @use HasFactory<SupportTicketFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_resolved' => 'boolean',
            'last_reply_at' => 'datetime',
        ];
    }
}
