<?php

namespace App\Http\Utils;

class GenericMessage
{
    public static $TRANSACT = "Transaction successfully added";
    public static $TRANSACT_NOT_FOUND = "Transaction not found";
    static $APPROVE = "Transaction approved.";
    static $REJECT = "Transaction rejected.";
    static $INVALID = "There's something wrong in your request";
    static $UNDEFINED_USER = "Undefined user, please try again.";
    static $INVALID_INPUT = "Oops! some of your input has a problem.";
    static $INVALID_CREDENTIALS = "Please check your username or password.";
}