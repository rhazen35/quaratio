<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 28-7-2016
 * Time: 15:40
 */

namespace application\ajax\project\newProject;

require("../../../application/init/init.php");

use \application\controller;
use application\classes\project;

( new controller\Controller( "class", "project", "project" ) )->request();

$urlData = array( "requestType" => "template", "requestAttribute" => "home", "requestDirectory" => "common" );
$urlData = base64_encode( json_encode( $urlData ) );

if( $_SERVER['REQUEST_METHOD'] === "POST" ):

    $name        = ( $_POST['name'] ? $_POST['name'] : "" );
    $description = ( $_POST['description'] ? $_POST['description'] : "" );

    if( empty( $name ) ): echo 'Please enter a name.'; exit(); endif;
    if( empty( $description ) ): echo 'Please enter a Description.'; exit(); endif;

    if( !empty( $name ) && !empty( $description ) ):

        $params = array( "name" => $name, "description" => $description );

        $lastInsertId = ( new project\Project( "newProject" ) )->request( $params );
        $_SESSION['projectId'] = $lastInsertId;
        echo "Project created!";
        exit();

    endif;

endif;