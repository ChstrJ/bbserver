<?php

namespace App\Http\Helpers\transaction;

class TransactionStatus {
    static $APPROVE = "approved";
    static $REJECT = "rejected";
    static $PENDING = "pending";
    static $REMOVED = 1;
    static $RESTORE = 0;
}