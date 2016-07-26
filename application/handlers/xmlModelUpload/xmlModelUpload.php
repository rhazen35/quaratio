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

    $validationStartTime = microtime(true);

    if( !empty( $_FILES ) ):

        if ( isset( $_FILES['xmlFile'] ) && ( $_FILES['xmlFile']['error'] == UPLOAD_ERR_OK ) ):

            $file               = $_FILES['xmlFile']['tmp_name'];
            $fileName           = $_FILES['xmlFile']['name'];
            $uploadedAt         = date( "Y-m-d H:i:s" );
            $xmlFile            = $_FILES['xmlFile']['tmp_name'];
            $path_parts         = pathinfo($_FILES["xmlFile"]["name"]);
            $extension          = $path_parts['extension'];
            $newFile            = sha1_file($file);

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
             * Add the original file name to the report array
             */
            $report['originalFileName'] = $fileName;

            /**
             * Hash and save the file if validation was succesfull
             */
            if( $report['validation']['valid'] === true ):

                if( !hash_equals( $newFile, $matchHash ) ):

                    if ( !move_uploaded_file(
                        $_FILES['xmlFile']['tmp_name'],
                        sprintf('./files/%s.%s',
                        sha1_file($_FILES['xmlFile']['tmp_name']),
                        $extension
                        )
                    ) ):

                        throw new \RuntimeException('Failed to move uploaded file.');

                    endif;

                    $report['file_exists'] = false;
                    /**
                     * Save the model in the database
                     */
                    $lastInsertedID = ( new iOXMLModelUpload\IOXMLModelUpload( "saveModel", $newFile, $uploadedAt ) )->request();
                else:

                    $report['file_exists'] = true;

                endif;

            endif;

            function microtimeFormat( $data )
            {
                $duration = microtime(true) - $data;
                $hours = (int)($duration/60/60);
                $minutes = (int)($duration/60)-$hours*60;
                $seconds = $duration-$hours*60*60-$minutes*60;
                return number_format((float)$seconds, 3, '.', '');
            }

            $validationEndTime = microtimeFormat( $validationStartTime );

            $report['startTime'] = $validationEndTime;

            $_SESSION['xmlValidatorReport'] = serialize( $report );

            $requestType      = "template";
            $requestAttribute = "xmlModelReport";
            $requestDirectory = "xmlModel";
            $lastInsertedID   = ( isset( $lastInsertedID ) ? $lastInsertedID : "" );
            $urlData          = array( "requestType" => $requestType, "requestAttribute" => $requestAttribute, "requestDirectory" => $requestDirectory, "xmlModelId" => $lastInsertedID );
            $urlData          = base64_encode( json_encode( $urlData ) );

            header("Location: ".LITENING_SELF."?data=".$urlData."");
            exit();

        else:

            echo 'Er kon geen file gedetecteerd worden, kies het juiste bestand.';

        endif;

    endif;

endif;