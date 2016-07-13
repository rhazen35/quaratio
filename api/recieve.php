<?php

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 30-Jun-16
 * Time: 16:22
 */

namespace api\recieve;

use api\classes\RequestHandler;

require($_SERVER['DOCUMENT_ROOT']."/quaratio/api/classes/requestHandler/requestHandler.php");

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if( $_SERVER['REQUEST_METHOD'] === "POST" ):

    $type = "post";

    (new RequestHandler\requestHandler( $type ))->handle( $_POST );

endif;