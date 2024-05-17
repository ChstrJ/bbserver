<?php



namespace App\Http\Utils;

class Response
{
    public static function createResource()
    {
        $msg = ["message" => GenericMessage::$CREATE];
        $code = HttpStatusCode::$CREATED;
        return response()->json($msg, $code);
    }

    public static function updateResource()
    {
        $msg = ["message" => GenericMessage::$UPDATE];
        $code = HttpStatusCode::$ACCEPTED;
        return response()->json($msg, $code);
    }

    public static function restoreResource() {
        $msg = ["message" => GenericMessage::$RESTORE];
        $code = HttpStatusCode::$ACCEPTED;
        return response()->json($msg, $code);
    }

    public static function deleteResource() {
        $msg = ["message" => GenericMessage::$DELETE];
        $code = HttpStatusCode::$ACCEPTED;
        return response()->json($msg, $code);
    }
    public static function approve()
    {
        $msg = ["message" => GenericMessage::$APPROVE];
        $code = HttpStatusCode::$ACCEPTED;
        return response()->json($msg, $code);
    }

    public static function reject()
    {
        $msg = ["message" => GenericMessage::$REJECT];
        $code = HttpStatusCode::$ACCEPTED;
        return response()->json($msg, $code);
    }

    public static function transactNotFound()
    {
        $msg = ["message" => GenericMessage::$TRANSACT_NOT_FOUND];
        $code = HttpStatusCode::$NOT_FOUND;
        return response()->json($msg, $code);
    }

    public static function alreadyChanged()
    {
        $msg = ["message" => GenericMessage::$ALREADY_CHANGED];
        $code = HttpStatusCode::$CONFLICT;
        return response()->json($msg, $code);
    }

    public static function alreadyApproved()
    {
        $msg = ["message" => GenericMessage::$ALREADY_APPROVED];
        $code = HttpStatusCode::$CONFLICT;
        return response()->json($msg, $code);
    }


    public static function alreadyRejected()
    {
        $msg = ["message" => GenericMessage::$ALREADY_APPROVED];
        $code = HttpStatusCode::$CONFLICT;
        return response()->json($msg, $code);
    }

    public static function notFound()
    {
        $msg = ["message" => HttpStatusMessage::$NOT_FOUND];
        $code = HttpStatusCode::$NOT_FOUND;
        return response()->json($msg, $code);
    }
    public static function invalid()
    {
        $msg = ["message" => GenericMessage::$INVALID];
        $code = HttpStatusCode::$UNPROCESSABLE_ENTITY;
        return response()->json($msg, $code);
    }

    public static function invalidCredentials()
    {
        $msg = ["message" => GenericMessage::$INVALID_CREDENTIALS];
        $code = HttpStatusCode::$UNAUTHORIZED;
        return response()->json($msg, $code);
    }

    public static function orderSuccess() {
        $msg = ["message" => GenericMessage::$ORDER_ADD];
        $code = HttpStatusCode::$OK;
        return response()->json($msg, $code);
    }

    public static function orderRemoved() {
        $msg = ["message" => GenericMessage::$ORDER_REMOVE];
        $code = HttpStatusCode::$ACCEPTED;
        return response()->json($msg, $code);
    }
   
}