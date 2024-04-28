<?php



namespace App\Http\Utils;

class Message
{
    use ResponseHelper;
    public static function approve()
    {
        return ["message" => GenericMessage::$APPROVE];
    }

    public static function reject()
    {
        return ["message" => GenericMessage::$REJECT];
    }

    public static function transactNotFound()
    {
        return ["message" => GenericMessage::$TRANSACT_NOT_FOUND];
    }

    public static function alreadyApproved()
    {
        return ["message" => GenericMessage::$ALREADY_APPROVED];
    }


    public static function alreadyRejected()
    {
        return ["message" => GenericMessage::$ALREADY_APPROVED];
    }

    public static function notFound()
    {
        return ["message" => HttpStatusMessage::$NOT_FOUND];
    }
    public static function invalid()
    {
        return ["message" => GenericMessage::$INVALID];
    }

    public static function invalidCredentials()
    {
        return ["message" => GenericMessage::$INVALID_CREDENTIALS];
    }

    public static function orderSuccess() {
        return ["message" => GenericMessage::$SUCCESS_ORDER];
    }
}