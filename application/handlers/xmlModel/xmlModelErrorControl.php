<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 24-7-2016
 * Time: 16:36
 */

namespace application\handlers\xmlModel\xmlModelErrorControl;

if( $_SERVER['REQUEST_METHOD'] === "POST" ):

    $type    = ( isset( $_POST['type'] ) ? $_POST['type'] : "" );
    $display = ( isset( $_POST['display'] ) ? $_POST['display'] : "" );

    switch( $type ):

        case"severe":
            $_SESSION['xmlModelErrorSevere']  = $display;
            break;
        case"error":
            $_SESSION['xmlModelErrorError']   = $display;
            break;
        case"warning":
            $_SESSION['xmlModelErrorWarning'] = $display;
            break;
        case"info":
            $_SESSION['xmlModelErrorInfo']    = $display;
            break;
        case"valid":
            $_SESSION['xmlModelErrorValid']   = $display;
            break;

    endswitch;

    $urlDataPRG = array( "requestType" => "template", "requestAttribute" => "xmlModelReport", "requestDirectory" => "xmlModel");
    $urlDataPRG = base64_encode( json_encode( $urlDataPRG ) );

    header("Location: ".LITENING_SELF."?data=".$urlDataPRG."");
    exit();

endif;