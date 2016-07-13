<?php
/*
 * Register handler:
 *
 * - Validates user input.
 * - Checks if the email adress already exists.
 * - Registers the user.
 *
 */
namespace application\handlers\register;

use \application\controller;
use \application\classes\login;
use application\classes\registeruser;
use application\classes\systemLogger;

( new controller\Controller( "class", "register", "register" ) )->request();

// Data url array's

$urlDataLogin                     = array();;
$urlDataLogin["message"]          = "signedUp";
$urlDataLogin["requestType"]      = "template";
$urlDataLogin["requestAttribute"] = "login";
$urlDataLogin["requestDirectory"] = "login";
$urlDataLogin                     = base64_encode(json_encode($urlDataLogin));

$urlData                     = array();
$urlData["message"]          = "signedUp";
$urlData["requestType"]      = "template";
$urlData["requestAttribute"] = "register";
$urlData["requestDirectory"] = "register";

// Handle post only

if($_SERVER['REQUEST_METHOD'] === "POST"):

    $url                = LITENING_SELF;
    $email              = (isset($_POST['email']) && !empty($_POST['email']) ? $_POST['email'] : "");
    $password           = (isset($_POST['password']) && !empty($_POST['password']) ? $_POST['password'] : "");
    $passwordRepeat     = (isset($_POST['passwordRepeat']) && !empty($_POST['passwordRepeat']) ? $_POST['passwordRepeat'] : "");
    $urlData['email']   = $email;

    // Email

    if(empty($email)):
        $urlData["emailError"] = "empty";
        $urlData["error"] = "noEmail";
        header("Location: ".$url."?data=".base64_encode(json_encode($urlData)));
        exit();
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)):
        $urlData["emailError"] = "notValid";
        $urlData["error"] = "invalidEmail";
        header("Location: ".$url."?data=".base64_encode(json_encode($urlData)));
        exit();
    endif;

    // Password

    if(empty($password)):
        $urlData["passwordError"] = "empty";
        $urlData["error"] = "noPassword";
        header("Location: ".$url."?data=".base64_encode(json_encode($urlData)));
        exit();
    elseif(strlen($password) < 8):
        $urlData["passwordError"] = "tooShort";
        $urlData["error"] = "passwordTooShort";
        header("Location: ".$url."?data=".base64_encode(json_encode($urlData)));
        exit();
    endif;

    // Password repeat

    if(empty($passwordRepeat)):
        $urlData["passwordRepeatError"] = "empty";
        $urlData["error"] = "noPasswordRepeat";
        header("Location: ".$url."?data=".base64_encode(json_encode($urlData)));
        exit();
    elseif(strlen($passwordRepeat) < 8):
        $urlData["passwordRepeatError"] = "tooShort";
        header("Location: ".$url."?data=".base64_encode(json_encode($urlData)));
        exit();
    elseif($passwordRepeat !== $password):
        $urlData["error"] = "noMatch";
        header("Location: ".$url."?data=".base64_encode(json_encode($urlData)));
        exit();
    endif;

    // Check if email, password and password repeat have value.

    if(empty($email) && empty($password) && empty($passwordRepeat)):

        $urlData["emailError"]           = "empty";
        $urlData["passwordError"]        = "empty";
        $urlData["passwordRepeatError"]  = "empty";

        header("Location: ".$url."?data=".base64_encode(json_encode($urlData)));
        exit();

    else:

        $results = (new registeruser\RegisterUser( $email, $password ))->checkEmailExists();

        $id = "";

        foreach($results as $result):

            $id = $result[0];

        endforeach;

        if( $id === NULL ):

            // Register the user

            ( new registeruser\RegisterUser( $email, $password ) )->register();

            // Log the action - see convertSystemLogs() for the declarations of the numbers.

            $results = ( new login\Login( $email, " " ) )->loginId();

            foreach( $results as $result ):

                $userId = $result[0];

            endforeach;

            $userId                 = !empty( $userId ) ? $userId : "";
            $_SESSION['registerId'] = $userId;

            ( new systemLogger\SystemLogger( "1", "1", "1" ) )->createLog();

            unset($_SESSION['registerId']);
            header("Location: ".$url."?data=".$urlDataLogin."");
            exit();

        else:
            
            $urlData["error"]   = "emailExists";

            header("Location: ".$url."?data=".base64_encode(json_encode($urlData)));
            exit();

        endif;

    endif;

endif;

?>