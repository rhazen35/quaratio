<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 17-Jul-16
 * Time: 19:07
 */

namespace application\classes\iOXMLModelUpload;

use \application\controller;
use \application\model\service;
use application\classes\iOXMLEAValidator;

( new controller\Controller( "class", "iOXMLEAValidator", "iOXMLEAValidator" ) )->request();

if( !class_exists( "IOXMLModelUpload" ) ):

    class IOXMLModelUpload
    {

        protected $type;
        protected $xmlFile;
        protected $uploadedAt;

        public function __construct( $type, $xmlFile, $uploadedAt )
        {

            $this->type         = $type;
            $this->xmlFile      = $xmlFile;
            $this->uploadedAt   = $uploadedAt;

        }

        public function request()
        {

            switch( $this->type ):

                case"newModel":
                    return( $this->newModel() );
                    break;
                case"saveModel":
                    $this->saveModel();
                    break;
                case"matchHash":
                    return( $this->matchHash() );
                    break;
            endswitch;

        }

        private function newModel()
        {

            $validateXMLFile    = ( new iOXMLEAValidator\IOXMLEAValidator( $this->xmlFile ) )->validate();

            return( $validateXMLFile );

        }

        private function matchHash()
        {

            $sql        = "CALL proc_getMatchingModelHash(?)";
            $data       = array("hash" => $this->xmlFile);
            $format     = array('s');

            $type       = "read";
            $database   = "quaratio";

            $returnData = ( new service\Service( $type, $database ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):

                foreach($returnData as $returnDat):
                    $hash = $returnDat;
                endforeach;

                return( $hash );

            else:

                return( false );

            endif;

        }

        private function saveModel()
        {

            $datetime    = new \DateTime( $this->uploadedAt );
            $upload_date = $datetime->format('Y-m-d');
            $upload_time = $datetime->format('H:i:s');
            $date        = date("Y-m-d");
            $time        = date("H:i:s");
            $userId      = !empty( $_SESSION['userId'] ) ? $_SESSION['userId'] : "";
            $id          = "";

            $sql        = "CALL proc_newModel(?,?,?,?,?,?,?)";
            $data       = array(
                                "id"            => $id,
                                "user_id"       => $userId,
                                "hash"          => $this->xmlFile,
                                "upload_date"   => $upload_date,
                                "upload_time"   => $upload_time,
                                "date"          => $date,
                                "time"          => $time
                                );
            $format     = array("iisssss");

            $type       = "create";
            $database   = "quaratio";

            ( new service\Service( $type, $database ) )->dbAction( $sql, $data, $format );

        }

    }

endif;