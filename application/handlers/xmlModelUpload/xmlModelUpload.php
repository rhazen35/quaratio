<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 19-7-2016
 * Time: 10:28
 */

namespace application\handlers\xmlModelUpload;

use \application\controller;
use application\classes\iOXMLModelUpload;

( new controller\Controller( "class", "iOXMLModelUpload", "iOXMLModelUpload" ) )->request();

if( $_SERVER['REQUEST_METHOD'] === "POST" ):

    if( !empty( $_FILES ) ):

        if ( isset( $_FILES['xmlFile'] ) && ( $_FILES['xmlFile']['error'] == UPLOAD_ERR_OK ) ):

            $uploadedAt         = date( "Y-m-d H:i:s" );
            $xmlFile            = $_FILES['xmlFile']['tmp_name'];
            $path_parts         = pathinfo($_FILES["xmlFile"]["name"]);
            $extension          = $path_parts['extension'];
            $newFile            = sha1_file($_FILES['xmlFile']['tmp_name']);

            /**
             * Check if the model already exists
             */
            $returnData = ( new iOXMLModelUpload\IOXMLModelUpload( "matchHash", $newFile, $uploadedAt ) )->request();
            if( !empty( $returnData ) && $returnData !== false ):
                $matchHash = $returnData[0];
            else:
                $matchHash = "";
            endif;

            /**
             * Pass the xml file with the new model command and the timestamp
             * XML will be validated and a report is returned
             */
            $report = ( new iOXMLModelUpload\IOXMLModelUpload( "newModel", $xmlFile, $uploadedAt ) )->request();

            /**
             * Hash and save the file if validation was succesfull
             */
            if( $report['validation_success'] === true ):


                if( !hash_equals( $newFile, $matchHash ) ):

                    if (!move_uploaded_file(
                        $_FILES['xmlFile']['tmp_name'],
                        sprintf('./files/%s.%s',
                        sha1_file($_FILES['xmlFile']['tmp_name']),
                        $extension
                        )
                    )):

                        throw new \RuntimeException('Failed to move uploaded file.');

                    endif;

                    $report['file_exists'] = false;
                    /**
                     * Save the model in the database
                     */
                    ( new iOXMLModelUpload\IOXMLModelUpload( "saveModel", $newFile, $uploadedAt ) )->request();

                else:

                    $report['file_exists'] = true;

                endif;

            endif;

            $requestType      = "template";
            $requestAttribute = "xmlModelReport";
            $requestDirectory = "xmlModel";
            $report           = serialize( $report );
            $urlData          = array( "requestType" => $requestType, "requestAttribute" => $requestAttribute, "requestDirectory" => $requestDirectory, "report" => $report );
            $urlData          = base64_encode( json_encode( $urlData ) );

            header("Location: ".LITENING_SELF."?data=".$urlData."");

        else:

            echo 'The file you uploaded can not be processed, please use another file.';

        endif;

    endif;

endif;