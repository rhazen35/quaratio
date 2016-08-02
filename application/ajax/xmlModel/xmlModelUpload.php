<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 19-7-2016
 * Time: 10:28
 */

require("../../../application/init/init.php");

use \application\controller;
use application\classes\iOXMLEAModel;
use application\classes\iOXMLModelUpload;
use application\classes\project;

( new controller\Controller( "class", "iOXMLModelUpload", "iOXMLModelUpload" ) )->request();
( new controller\Controller( "class", "iOXMLEAModel", "iOXMLEAModel" ) )->request();
( new controller\Controller( "class", "project", "project" ) )->request();

if( $_SERVER['REQUEST_METHOD'] === "POST" ):

    $validationStartTime = microtime(true);

    if( !empty( $_FILES ) ):

        if ( isset( $_FILES['file'] ) && ( $_FILES['file']['error'] == UPLOAD_ERR_OK ) ):

            $file               = $_FILES['file']['tmp_name'];
            $fileName           = $_FILES['file']['name'];
            $uploadedAt         = date( "Y-m-d H:i:s" );
            $xmlFile            = $_FILES['file']['tmp_name'];
            $path_parts         = pathinfo($_FILES["file"]["name"]);
            $extension          = $path_parts['extension'];
            $newFile            = sha1_file($file);

            /**
             * Check if the model already exists
             */
            $matchHash = ( new iOXMLModelUpload\IOXMLModelUpload( "matchHash", $newFile, $uploadedAt ) )->request();
            if( !empty( $matchHash ) ):
                $matchHash = $matchHash;
            else:
                $matchHash = "";
            endif;



            /**
             * Pass the xml file with the new model command and the timestamp
             * XML will be validated and a report is returned
             */
            $report = ( new iOXMLModelUpload\IOXMLModelUpload( "newModel", $xmlFile, $uploadedAt ) )->request();

            /**
             * Add the original file name to the report array
             */
            $report['originalFileName'] = $fileName;

            if( !hash_equals( $newFile, $matchHash ) ):

                $report['file_exists'] = false;

                /**
                 * Save the model in the database and in the files/xml_models_tmp directory
                 */
                $lastInsertedID = ( new iOXMLModelUpload\IOXMLModelUpload( "saveModel", $newFile, $uploadedAt ) )->request();

                /**
                 * Store the project id, model id, and user id in the projects_models join table
                 */
                $params = array( "model_id" => $lastInsertedID );
                ( new project\Project( "saveModelJoinTable" ) )->request( $params );

                /**
                 * Hash and save the file
                 */
                move_uploaded_file(
                    $_FILES['file']['tmp_name'],
                    sprintf(LITENING_ROOT_DIRECTORY.'/web/files/xml_models_tmp/%s.%s',
                        sha1_file($_FILES['file']['tmp_name']),
                        $extension
                    )) ;

                $_SESSION['xmlModelId'] = ( isset( $lastInsertedID ) ? $lastInsertedID : "" );

            else:

                $report['file_exists'] = true;
                $returnData = ( new iOXMLEAModel\IOXMLEAModel( $matchHash ) )->getModelIdByHash();
                $_SESSION['xmlModelId'] = ( !empty( $returnData[0]['id'] ) ? $returnData[0]['id'] : "" );

            endif;

            function microtimeFormat( $data )
            {
                $duration = microtime(true) - $data;
                $hours = (int)($duration/60/60);
                $minutes = (int)($duration/60)-$hours*60;
                $seconds = $duration-$hours*60*60-$minutes*60;
                return( number_format((float)$seconds, 3, '.', '') );
            }

            $validationEndTime = microtimeFormat( $validationStartTime );

            $report['startTime'] = $validationEndTime;

            $_SESSION['xmlValidatorReport'] = serialize( $report );

        endif;

    endif;

endif;