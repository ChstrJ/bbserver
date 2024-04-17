<?php



namespace App\Http\Utils;

class Message {
    use ResponseHelper;
    public static function Approve() {
        return ["message" => GenericMessage::$APPROVE];
    }

    public static function Reject() {
        return ["message" => GenericMessage::$REJECT];
    }

    public static function TransactNotFound() {
        return ["message" => GenericMessage::$TRANSACT_NOT_FOUND];
    }

    public static function AlreadyApproved() {
        return ["message" => GenericMessage::$ALREADY_APPROVED];
    }

    public static function AlreadyRejected() {
        return ["message" => GenericMessage::$ALREADY_APPROVED];
    }

    public static function NotFound() {
        return ["message" => HttpStatusMessage::$NOT_FOUND];
    }
}