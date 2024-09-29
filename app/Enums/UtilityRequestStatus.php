<?php

namespace App\Enums;

enum UtilityRequestStatus: string {
    case PENDING = 'Pending';
    case APPROVED = 'Approved';
    case CANCELLED = 'Cancelled';
}