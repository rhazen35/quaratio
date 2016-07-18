<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 17-Jul-16
 * Time: 19:39
 */

namespace application\classes\iOXMLValidator;

use \application\controller;
use application\classes\iOXMLModelParser;

( new controller\Controller( "class", "iOXMLModelParser", "iOXMLModelParser" ) )->request();

if( !class_exists( "IOXMLValidator" ) ):

    class IOXMLValidator
    {

        protected $xmlFile;

        public function __construct( $xmlFile )
        {
            $this->xmlFile = $xmlFile;
        }

        public function validate()
        {

            $parseReport = array();
            /**
             * Check if the file is a XML file
             */
            $xml = @simplexml_load_file( $this->xmlFile );
            if ( $xml === false ) {
                $parseReport['xmlValidation'] = "invalid";
                return( $parseReport );
            }

            $parseReport['xmlValidation'] = "valid";

            /**
             * Check if the xml file has an extension
             */
            $modelExtension     = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseModelExtensionInfo();
            $validatedExtension = $this->validateExtension( $modelExtension );

            if( empty( $validatedExtension ) ):



            endif;

            $parsedClasses      = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseXMLClasses();
            $parsedConnectors   = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseConnectors();

            return( $parsedClasses );

        }

        private function validateExtension( $modelExtension )
        {

            if( empty( $modelExtension ) ):

                return( "No extension could be found." );

            else:

                $extender   = $modelExtension['model']['extender_info']['extender'];
                $extenderID = $modelExtension['model']['extender_info']['extenderID'];

                return( $extender );

            endif;

        }

    }

endif;