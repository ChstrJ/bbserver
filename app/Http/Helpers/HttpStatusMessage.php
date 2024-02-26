<?php

class HttpStatusMessage {
     // Informational 1xx
     static $CONTINUE = "Your request is received, and we're processing it.";
     static $SWITCHING_PROTOCOLS = "Switching to a different communication protocol.";
 
     // Successful 2xx
     static $OK = "Success! Your request was processed.";
     static $CREATED = "Congratulations! Your request led to a new resource creation.";
 
     // Redirection 3xx
     
     // Client Error 4xx
     static $BAD_REQUEST = "Oops! Something's wrong with your request. Please check and try again.";
     static $UNAUTHORIZED = "Authentication required. Please log in to access this resource.";
     static $FORBIDDEN = "Access denied. You don't have permission to view this content.";
     static $NOT_FOUND = "Sorry, the requested resource couldn't be found.";
     static $UNPROCESSABLE_ENTITY = "Sorry, we couldn't process your request. Please check the data and try again.";
}