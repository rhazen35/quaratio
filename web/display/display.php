<?php

/**
 *
 * Handles the display of templates
 *
 * - Send to login page when not logged in
 * - Send to register page when a sign-up is requested
 * - Requests the header and the menu
 * - Requests templates
 *
 *  Development
 *
 * - Provides links for system info and system logs.
 * - Show or hide will be stored in a session.
 * - System info and logs will be shown at the top of the page.
 *
 */

namespace web\display;

use \application\core;
use \application\controller;
use application\classes\login;
use application\classes\systemLogger;
use init;

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 10-6-2016
 * Time: 02:33
 */

// Request the get data($_GET)

$data = ( new controller\Controller( "", "", "" ) )->getData();
$data = serialize( $data );
define( "CMS_GET_DATA", $data );

// Create variables from data

$data    = unserialize( CMS_GET_DATA );
$allowed = ['requestType', 'requestAttribute', 'requestDirectory', 'systemInfo', 'systemLogs'];

if( !empty( $data ) ):
    foreach( $data as $key => $value ):
        if( in_array( $key, $allowed ) ) ${$key} = $value;
    endforeach;
endif;

// Request the login class.

( new controller\Controller( "class", "login", "login" ) )->request();

// Check if a user is logged in.

$isLoggedIn = ( new login\Login(" ", " ") )->isLoggedIn();

// Request the system logger

( new controller\Controller( "class", "systemLogger", "systemLogger" ) )->request();

// Request the pagination

( new controller\Controller( "class", "pagination", "pagination" ) )->request();

// Header

( new controller\Controller( "template", "header", "common" ) )->request();

if( $isLoggedIn === true ):

    /** -------------------------------------------------------------- */
    /** -------------------- DEVELOPMENT ONLY! ----------------------- */
    /** -------------------------------------------------------------- */

    // Show the system logs

    $systemLogs         = ( !empty( $systemLogs ) ? $systemLogs : "" );
    $sessionSystemLogs  = ( !empty( $_SESSION['LTN_systemLogs'] ) ? $_SESSION['LTN_systemLogs'] : "" );

    // Quick access menu

    echo '<div class="quickAccess">';

    // Logout

    echo '<div class="quickAccess-Clock">'.date("d-m-Y").'<span class="clock" id="clock"></span></div>';

    echo '<div class="quickAccess-Link quickLogout"><a href="../application/handlers/login/logout.php">Logout</a> | </div>';

    if( $systemLogs === "display" || $sessionSystemLogs === "display" && $systemLogs !== "hide" ):

        $_SESSION['LTN_systemLogs'] = "display";
        $urlDataSysLogs = array();
        $urlDataSysLogs['systemLogs'] = "hide";
        $urlDataSysLogs = base64_encode( json_encode( $urlDataSysLogs ) );

        echo '<div class="quickAccess-Link quickSystemLogs"><a href="'.LITENING_SELF.'?data='.$urlDataSysLogs.'">Hide system logs</a> | </div>';

    elseif( empty( $sessionSystemLogs ) || $systemLogs === "hide" || $sessionSystemLogs === "hide" ):

        unset( $_SESSION['LTN_systemLogs'] );
        $urlDataSysLogs = array();
        $urlDataSysLogs['systemLogs'] = "display";
        $urlDataSysLogs = base64_encode( json_encode( $urlDataSysLogs ) );

        echo '<div class="quickAccess-Link quickSystemLogs"><a href="'.LITENING_SELF.'?data='.$urlDataSysLogs.'">Show system logs</a> | </div>';

    endif;

    // Show / Hide system information url

    $systemInfo         = ( !empty( $systemInfo ) ? $systemInfo : "" );
    $sessionSystemInfo  = ( !empty( $_SESSION['LTN_systemInfo'] ) ? $_SESSION['LTN_systemInfo'] : "" );

    if( $systemInfo === "display" || $sessionSystemInfo === "display" && $systemInfo !== "hide" ):

        $_SESSION['LTN_systemInfo'] = "display";
        $urlDataSysInf = array();
        $urlDataSysInf['systemInfo'] = "hide";
        $urlDataSysInf = base64_encode( json_encode( $urlDataSysInf ) );

        echo '<div class="quickAccess-Link quickSysteminfo"><a href="'.LITENING_SELF.'?data='.$urlDataSysInf.'">Hide system info</a> | </div>';

    elseif( empty( $sessionSystemInfo ) || $systemInfo === "hide" || $sessionSystemInfo === "hide" ):

        unset( $_SESSION['LTN_systemInfo'] );
        $urlDataSysInf = array();
        $urlDataSysInf['systemInfo'] = "display";
        $urlDataSysInf = base64_encode( json_encode( $urlDataSysInf ) );

        echo '<div class="quickAccess-Link quickSysteminfo"><a href="'.LITENING_SELF.'?data='.$urlDataSysInf.'">Show system info</a> | </div>';

    endif;

    echo '</div>'; // Close quick access

    // Display the system information when set to display

    if( $systemInfo === "display" || $sessionSystemInfo === "display" && $systemInfo !== "hide" ):
        ( new controller\Controller( "template", "systemInfo", "common" ) )->request();
    endif;

    /** -------------------------------------------------------------- */
    /** ----------------- END OF DEVELOPMENT ONLY -------------------- */
    /** -------------------------------------------------------------- */

    // Request the system logs if display is on

    if( $systemLogs === "display" || $sessionSystemLogs === "display" && $systemLogs !== "hide" ):
        ( new controller\Controller( "template", "systemLogs", "common" ) )->request();
    endif;

    // Request an app

    if( !empty( $requestType ) && !empty( $requestAttribute ) && !empty( $requestDirectory ) ):

        if( $requestType === "template" ):

            ( new controller\Controller( $requestType, $requestAttribute, $requestDirectory ) )->request();

        else:
            echo 'A wrong type was passed to the display: instantiation aborted!';
        endif;

    else:

        ( new controller\Controller( "template", "home", "common" ) )->request();

    endif;

elseif( !empty( $requestAttribute ) && $requestAttribute === "register" ):

    // Request the sign-up template

    ( new controller\Controller( "template", "register", "register" ) )->request();

else:

    // Request the login

    ( new controller\Controller( "template", "login", "login" ) )->request();

endif;

// Footer

( new controller\Controller( "template", "footer", "common" ) )->request();