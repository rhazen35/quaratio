<?php

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 4-7-2016
 * Time: 16:11
 */

namespace apllication\handlers\excel;

use \application\core;
use application\classes\iOExcel;

$writeArray = array();
$readArray = array();

$appels = ( !empty( $_POST['appel'] ) ?  $_POST['appel'] : "" );
$knipwit = ( !empty( $_POST['knipwit'] ) ?  $_POST['knipwit'] : "" );
$roomboter = ( !empty( $_POST['roomboter'] ) ?  $_POST['roomboter'] : "" );
$ei = ( !empty( $_POST['ei'] ) ?  $_POST['ei'] : "" );
$pin = ( !empty( $_POST['pin'] ) ?  $_POST['pin'] : "" );

$writeArray['A3'] = $appels;
$writeArray['A4'] = $knipwit;
$writeArray['A5'] = $roomboter;
$writeArray['A6'] = $ei;
$writeArray['F7'] = $pin;

$readArray["totaalProducten"] = "A7";
$readArray["totaalPrijs"] = "F8";

// Create random user_id for file usage

if( empty( $_SESSION['user_excel_id'] ) ):

    $alphabet    = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $userId      = array();
    $alphaLength = strlen( $alphabet ) - 1;

    for ($i = 0; $i < 16; $i++) {
        $n = rand( 0, $alphaLength );
        $userId[] = $alphabet[$n];
    }

    $_SESSION['user_excel_id'] = implode( $userId );

endif;

$userId = ( !empty( $_SESSION['user_excel_id'] ) ? $_SESSION['user_excel_id'] : "" );

// Set the dir, base file and new file

$directory = $_SERVER['DOCUMENT_ROOT'].'/quaratio/web/files';

$baseFile = $_SERVER['DOCUMENT_ROOT'].'/quaratio/web/files/calculator.xlsx';
$newFile = $_SERVER['DOCUMENT_ROOT'].'/quaratio/web/files/'.( string ) $userId.'.xlsx';

if ( !file_exists($directory) ) {
    mkdir ($directory, 0744);
}

if( !file_exists( $newFile ) ):

    if( copy( $baseFile, $newFile ) ):

        include '../PHPExcel/Classes/PHPExcel/IOFactory.php';

        ( new core\Core( "class", "iOExcel", "iOExcel" ) )->request();
        ( new iOExcel\IOExcel() )->write( $writeArray );

        $returnData = ( new iOExcel\IOExcel() )->read( $readArray );
        $returnData = serialize($returnData);

        $urlData['requestType'] = "template";
        $urlData['requestAttribute'] = "home";
        $urlData['requestDirectory'] = "common";

        $urlData['excelResponseData'] = $returnData;
        $urlData = base64_encode(json_encode($urlData));

        unlink($newFile);

        header("Location: ".LITENING_SELF."?data=".$urlData."");
        exit();

    else:

        echo 'Copy has failed.';

    endif;

else:

    echo 'File already exists.';

endif;