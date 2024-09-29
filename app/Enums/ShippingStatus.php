<?php

namespace App\Enums;

enum ShippingStatus: string {
    case PENDING = 'Pending';
    case ORDERRECEIVED = 'OrderReceived';
    case PROCESSING = 'Processing';
    case ONTHEWAY = 'OnTheWay';
    case DELIVERED = 'Delivered';
    case RETURNED = 'Returned';
}