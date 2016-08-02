<?php

/**
 * Login hanlder:
 *
 * - Handles the user login to the LITENING framework.
 * - Validates the user input.
 * - Gets the user password with a corresponding email.
 * - Gets the user id for the login session.
 *
 */

namespace application\handlers\login;

use \application\controller;
use \application\classes\login;
use \application\classes\systemLogger;

// Handle post only

if( $_SERVER['REQUEST_METHOD'] === 'POST' ):

    $url        = LITENING_SELF;
    $email      = (isset($_POST['email']) ? $_POST['email'] : "");
    $email      = trim($email);
    $urlData    = array("email" => $email);
    $password   = (isset($_POST['password']) ? $_POST['password'] : "");

    // Url data home

    $urlDataHome                     = array();
    $urlDataHome['requestType']      = "template";
    $urlDataHome['requestAttribute'] = "home";
    $urlDataHome['requestDirectory'] = "common";
    $urlDataHome                     = base64_encode(json_encode($urlDataHome));

    // Validate email address

    if( empty( $email ) ):

        $urlData["emailError"]  = "empty";
        $urlData["error"]       = "noEmail";

        header("Location: ".$url."?data=".base64_encode(json_encode($urlData))."");
        exit();

    elseif( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ):

        $urlData["emailError"]  = "notValid";
        $urlData["error"]       = "invalidEmail";

        header("Location: ".$url."?data=".base64_encode(json_encode($urlData))."");
        exit();

    endif;

    // Validate password

    if( empty( $password ) ):

        $urlData["passwordError"]   = "empty";
        $urlData["error"]           = "noPassword";

        header("Location: ".$url."?data=".base64_encode(json_encode($urlData))."");
        exit();

    elseif( strlen( $password ) < 8 ):

        $urlData["passwordError"] = "notValid";
        $urlData["error"]         = "invalidPassword";

        header("Location: ".$url."?data=".base64_encode(json_encode($urlData))."");
        exit();

    endif;

    // Check if email and password have value

    if( empty( $email ) && empty( $password ) ):

        $urlData["emailError"]          = "empty";
        $urlData["passwordError"]       = "empty";
        $urlData["passwordRepeatError"] = "empty";

        header("Location: ".$url."?data=".base64_encode(json_encode($urlData))."");
        exit();

    else:

        $results = ( new login\Login( $email, " " ) )->getUserPass();

        foreach( $results as $result ):

            $returnPass = $result[0];

        endforeach;

        $verify = !empty( $returnPass ) ? password_verify( $password, $returnPass ) : "";

        if( $verify ):

            $results = ( new login\Login( $email, " " ) )->loginId();

            foreach( $results as $result ):

                $userId = $result[0];
            
            endforeach;

            $_SESSION['login']                 = true;
            $_SESSION['userId']                = isset($userId) ? $userId : "";

            ( new login\Login( $email, " " ) )->login();

            // Log the action - see convertSystemLogs() for the declarations of the numbers.

            ( new systemLogger\SystemLogger( "2", "1", "1" ) )->createLog();

            header("Location: ".LITENING_SELF."?data=".$urlDataHome."");
            exit();

        else:

            $urlData["error"] = "loginFailed";

            header("Location: ".$url."?data=".base64_encode(json_encode($urlData))."");
            exit();

        endif;

    endif;

endif;
?>