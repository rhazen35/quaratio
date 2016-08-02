<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 29-Jul-16
 * Time: 11:50
 */

namespace application\ajax\project\deleteProject;

require("../../../application/init/init.php");

use \application\controller;
use application\classes\project;
use application\classes\iOXMLEAModel;


( new controller\Controller( "class", "project", "project" ) )->request();
( new controller\Controller( "class", "iOXMLEAModel", "iOXMLEAModel" ) )->request();

header('Content-Type: application/json; charset=UTF-8');
if( $_SERVER['REQUEST_METHOD'] === "POST" ):

    $projectId   = ( !empty( $_POST['projectId'] ) ? $_POST['projectId'] : "" );

    if( empty( $projectId ) ):

        echo json_encode( ['delete' => false] );
        exit();

    else:

        $params    = array( "project_id" => $projectId );
        $modelId   = ( new project\Project( "getModelIdByProjectId" ) )->request( $params );

        if( !empty( $modelId ) ):
            $model     = ( new iOXMLEAModel\IOXMLEAModel( $modelId['model_id'] ))->getModel();
            $modelHash = $model['hash'];

            $params['model_id'] = $modelId['model_id'];

            unlink( LITENING_ROOT_DIRECTORY.'/web/files/xml_models_tmp/'.$modelHash.'.xml');

        endif;

        ( new project\Project( "deleteProject" ) )->request( $params );

        echo json_encode( ['delete' => true] );
        exit();

    endif;

endif;
