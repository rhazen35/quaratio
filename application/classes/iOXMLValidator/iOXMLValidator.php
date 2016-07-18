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

            if ( $xml === false ):
                $parseReport['validateFileType'] = "invalid";
                return( $parseReport );
            else:
                $parseReport['validateFileType'] = "valid";
            endif;

            /**
             * Check if a root is available and if only one root is available
             */
            $parsedClasses = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseXMLClasses();
            $roots         = array();
            $trueRoots     = array();

            foreach( $parsedClasses as $parsedClass):
                if( !empty( $parsedClass['Root'] ) ):
                    $roots[] = $parsedClass['Root'];
                endif;
            endforeach;

            $totalRoots = count($roots);

            for($i = 0; $i < $totalRoots; $i++):
                if($roots[$i] === "true"):
                    $trueRoots[] = $i;
                endif;
            endfor;

            $totalTrueRoots = count($trueRoots);

            if( $totalTrueRoots < 1 ):
                $parseReport['validateRoot'] = "noRoot";
            elseif( $totalTrueRoots > 1 ):
                $parseReport['validateRoot'] = "manyRoot";
            else:
                $parseReport['validateRoot'] = "valid";
            endif;

            /**
             * Check for the extension version
             */
            $parsedExtensionInfo = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseModelExtensionInfo();
            $extensionVersion    = $parsedExtensionInfo['model']['extender_info']['extenderID'];

            if( !empty( $extensionVersion ) ):

                if( $extensionVersion === "6.5" ):
                    $parseReport['extensionVersion'] = "valid";
                else:
                    $parseReport['extensionVersion'] = "different";
                endif;

            else:
                $parseReport['extensionVersion'] = "invalid";
            endif;

            return( $parsedClasses );

        }

    }

endif;