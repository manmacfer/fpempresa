<?php
namespace App\Enums;

enum MatchingStatus:string {
    case Pending   = 'pending';
    case Accepted  = 'accepted';
    case Rejected  = 'rejected';
    case Cancelled = 'cancelled';
}
