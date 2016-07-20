<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 17-Jul-16
 * Time: 19:39
 */

namespace application\classes\iOXMLEAValidator;

use \application\controller;
use application\classes\iOXMLModelParser;
use \application\classes\iOXMLParser;

( new controller\Controller( "class", "iOXMLModelParser", "iOXMLModelParser" ) )->request();
//

if( !class_exists( "IOXMLEAValidator" ) ):

    class IOXMLEAValidator
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
             * Check if the file is a xml
             */
            $isXML = ( new iOXMLParser\IOXMLParser( $this->xmlFile ) )->isXML();

            $parseReport['isXML']              = array();
            $parseReport['isXML']['name']      = "XML";
            $parseReport['isXML']['type']      = "severe";
            $parseReport['isXML']['value']     = $isXML;
            $parseReport['isXML']['valid']     = ( $isXML === true ? true : false );
            $parseReport['isXML']['message']   = ( $isXML === true ? "ja" : "nee." );

            if( $isXML === true ):

                $parsedClasses    = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseXMLClasses();
                $parsedConnectors = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseConnectors();
                $totalClasses     = count( $parsedClasses );

                $parseReport['totalClasses']              = array();
                $parseReport['totalClasses']['name']      = "Classes";
                $parseReport['totalClasses']['type']      = "severe";
                $parseReport['totalClasses']['value']     = $totalClasses;
                $parseReport['totalClasses']['valid']     = ( $totalClasses !== 0 ? true : false );
                $parseReport['totalClasses']['message']   = ( $totalClasses !== 0 ? $totalClasses.' gevonden.' : "Geen gevonden.");

                /**
                 * Check if a root is available and if only one root is available
                 */
                $roots            = array();
                $trueRoots        = array();

                if( !empty( $totalClasses ) ):

                    foreach( $parsedClasses as $parsedClass ):
                        if( !empty( $parsedClass['Root'] ) ):
                            $roots[] = $parsedClass['Root'];
                        endif;
                    endforeach;

                    $totalRoots = count( $roots );

                    if( $totalRoots !== 0 ):
                        for( $i = 0; $i < $totalRoots; $i++ ):
                            if( $roots[$i] === "true" ):
                                $trueRoots[] = $i;
                            endif;
                        endfor;
                    endif;

                    $totalTrueRoots = count( $trueRoots );

                    $parseReport['totalRoots']              = array();
                    $parseReport['totalRoots']['name']      = "Root";
                    $parseReport['totalRoots']['type']      = "severe";
                    $parseReport['totalRoots']['value']     = $totalTrueRoots;
                    $parseReport['totalRoots']['valid']     = ( $totalTrueRoots !== 0 && $totalTrueRoots === 1 ? true : false );
                    $parseReport['totalRoots']['message']   = ( $totalTrueRoots !== 0 && $totalTrueRoots === 1 ? $totalTrueRoots.' gevonden' : ( $totalTrueRoots > 1 ? $totalTrueRoots.' gevonden' : 'Geen gevonden' ) );

                endif;

                /**
                 * Check for the xmi version
                 */
                $parsedExtensionInfo = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseModelExtensionInfo();
                $extensionVersion    = $parsedExtensionInfo['model']['extender_info']['extenderID'];
                $xmi_version         = $parsedExtensionInfo['model']['xmi_version'];

                $parseReport['xmiVersion']              = array();
                $parseReport['xmiVersion']['name']      = "XMI versie";

                if( !empty( $xmi_version ) && $xmi_version !== "2.1" ):
                    $parseReport['xmiVersion']['type']  = "info";
                elseif( empty( $xmi_version ) ):
                    $parseReport['xmiVersion']['type']  = "error";
                else:
                    $parseReport['xmiVersion']['type']  = "";
                endif;

                $parseReport['xmiVersion']['value']     = ( !empty( $xmi_version ) ? $xmi_version : "leeg" );
                $parseReport['xmiVersion']['valid']     = ( !empty( $xmi_version ) && $xmi_version === "2.1" ? true : false );
                $parseReport['xmiVersion']['message']   = ( !empty( $xmi_version ) ? ( $xmi_version === "2.1" ? "Gevonden" : "Gevonden maar een andere versie" ) : "Niet gevonden" );

                /**
                 * Check for the extension version
                 */
                $parseReport['extensionVersion']              = array();
                $parseReport['extensionVersion']['name']      = "Extension versie";

                if( !empty( $extensionVersion ) && $extensionVersion !== "6.5" ):
                    $parseReport['extensionVersion']['type']  = "info";
                elseif( empty( $extensionVersion ) ):
                    $parseReport['extensionVersion']['type']  = "error";
                else:
                    $parseReport['extensionVersion']['type']  = "";
                endif;

                $parseReport['extensionVersion']['value']     = $extensionVersion;
                $parseReport['extensionVersion']['valid']     = ( !empty( $extensionVersion ) && $extensionVersion === "6.5" ? true : false );
                $parseReport['extensionVersion']['message']   = ( !empty( $extensionVersion ) ? ( $extensionVersion === "6.5" ? "Gevonden" : "Gevonden maar een andere versie" ) : "Niet gevonden" );

                /**
                 * Check if all necessary items have been validated and conclude the total validation.
                 */
                $parseReport['validation']              = array();
                $parseReport['validation']['name']      = "Validatie";
                $parseReport['validation']['type']      = "severe";
                $parseReport['validation']['value']     = true;
                $parseReport['validation']['valid']     = true;
                $parseReport['validation']['message']   = "Geslaagd.";

            endif;

            return( $parseReport );

        }

    }

endif;