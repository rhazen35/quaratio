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
use \application\classes\iOXMLParser;

( new controller\Controller( "class", "iOXMLModelParser", "iOXMLModelParser" ) )->request();
//

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
            $parseReport['validation_success'] = "false";

            /**
             * Check if the file is a xml
             */
            $isXML = ( new iOXMLParser\IOXMLParser( $this->xmlFile ) )->isXML();

            if( $isXML === "valid" ):

                $parseReport['isXML'] = $isXML;

            /**
             * Check if a root is available and if only one root is available
             */
            $parsedClasses    = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseXMLClasses();
            $parsedConnectors = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseConnectors();

            $roots            = array();
            $trueRoots        = array();
            $totalClasses     = count( $parsedClasses );

            if( $totalClasses < 1 ):
                $parseReport['classes'] = "noClasses";
            elseif( $totalClasses === 1 ):
                $parseReport['classes'] = "oneClass";
            elseif( $totalClasses > 1 ):
                $parseReport['classes'] = "manyClasses";
            endif;

            foreach( $parsedClasses as $parsedClass ):
                if( !empty( $parsedClass['Root'] ) ):
                    $roots[] = $parsedClass['Root'];
                endif;
            endforeach;

            $totalRoots = count( $roots );

            for( $i = 0; $i < $totalRoots; $i++ ):
                if( $roots[$i] === "true" ):
                    $trueRoots[] = $i;
                endif;
            endfor;

            $totalTrueRoots = count( $trueRoots );

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
            $xmi_version         = $parsedExtensionInfo['model']['xmi_version'];

            if( !empty( $xmi_version ) ):
                $parseReport['xmi_version'] = $xmi_version;
            else:
                $parseReport['xmi_version'] = "noVersion";
            endif;

            if( !empty( $extensionVersion ) ):

                if( $extensionVersion === "6.5" ):
                    $parseReport['extensionVersion'] = $extensionVersion;
                else:
                    $parseReport['extensionVersion'] = "different";
                endif;

            else:
                $parseReport['extensionVersion'] = "invalid";
            endif;

            /**
             * Check if all necessary items have been validated
             */
            $parseReport['validation_success'] = true;

            elseif( $isXML === "invalid" ):

                $parseReport['isXML'] = $isXML;

            endif;

            return( $parseReport );

        }

    }

endif;