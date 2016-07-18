<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 17-Jul-16
 * Time: 19:07
 */

namespace application\classes\iOXMLModelUpload;

use \application\controller;
use application\classes\iOXMLValidator;

( new controller\Controller( "class", "iOXMLValidator", "iOXMLValidator" ) )->request();

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

            endswitch;

        }

        private function newModel()
        {

            $validateXMLFile    = ( new iOXMLValidator\IOXMLValidator( $this->xmlFile ) )->validate();

            return( $validateXMLFile );

        }

    }

endif;