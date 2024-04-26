<?php

namespace App\Http\Utils;

class GenericMessage
{
    static $TRANSACT = "Transaction successfully added";
    static $TRANSACT_NOT_FOUND = "Transaction not found";
    static $ALREADY_APPROVED = "Transaction has already been approved.";
    static $ALREADY_REJECTED = "Transaction has already been rejected.";
    static $APPROVE = "Transaction approved.";
    static $REJECT = "Transaction rejected.";
    static $INVALID = "There's something wrong in your request";
    static $UNDEFINED_USER = "Undefined user, please try again.";
    static $INVALID_INPUT = "Oops! some of your input has a problem.";
    static $INVALID_CREDENTIALS = "Invalid credentials. Please verify your credentials and try again.";
    static $NOT_UPDATED = "Not yet updated.";

}
