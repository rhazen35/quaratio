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

if( !class_exists( "IOXMLEAValidator" ) ):

    class IOXMLEAValidator
    {

        protected $xmlFile;
        protected $matchExcelFormat = '/^[A-Za-z]+[0-9]+(\:)?(?(1)[A-Za-z]+[0-9]+|[0-9]?)$/';


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
            $parseReport['isXML']['name']      = "XML file";

            if( $isXML === true ):
                $parseReport['isXML']['type']  = "valid";
            elseif( $isXML === false ):
                $parseReport['isXML']['type']  = "severe";
            endif;

            $parseReport['isXML']['value']     = ( $isXML === true ? 1 : 0 );
            $parseReport['isXML']['valid']     = ( $isXML === true ? true : false );
            $parseReport['isXML']['message']   = ( $isXML === true ? "File is an XML" : "File is not an XML" );

            /**
             * Only continue validation when the file is an XML
             */
            if( $isXML === true ):

                /**
                 * Parse the classes and count them
                 * Check if at least one class can be found
                 */
                $parsedClasses    = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseXMLClasses();
                $totalClasses     = count( $parsedClasses );

                //var_dump($parsedClasses);

                $parseReport['totalClasses']              = array();
                $parseReport['totalClasses']['name']      = ( $totalClasses === 1 ? "Class" : "Classes" );

                if( $totalClasses > 0 ):
                    $parseReport['totalClasses']['type']  = "valid";
                elseif( $totalClasses === 0 ):
                    $parseReport['totalClasses']['type']  = "severe";
                endif;

                $parseReport['totalClasses']['value']     = $totalClasses;
                $parseReport['totalClasses']['valid']     = ( $totalClasses !== 0 ? true : false );
                $parseReport['totalClasses']['message']   = ( $totalClasses !== 0 ? $totalClasses.' classes found.' : "No classes found.");

                /**
                 * Collect data from the parsed classes
                 */
                $roots            = array();
                $trueRoots        = array();
                $modifiedDates    = array();
                $operations       = array();

                if( !empty( $totalClasses ) ):

                    foreach( $parsedClasses as $parsedClass ):

                        /**
                         * Get all the roots out of the classes array
                         */
                        if( !empty( $parsedClass['Root'] ) ):
                            $roots[] = $parsedClass['Root'];
                        endif;

                        /**
                         * Get all the modified dates out of the classes array
                         */
                        if( !empty( $parsedClass['modified'] ) ):
                            $modifiedDates[] = $parsedClass['modified'];
                        endif;

                        /**
                         * Get all the operations out of the classes array
                         */
                        if( !empty( $parsedClass['operations'] ) ):
                            $operations[] = $parsedClass['operations'];
                        endif;

                    endforeach;

                    /**
                     * Get all operations without from the parsed classes
                     */
                    $operationsArray = array();

                    if( !empty( $operations ) ):

                        $i = 0;
                        foreach( $operations as $operation ):
                            $operationsArray[$i] = $operation;
                            $i++;
                        endforeach;

                        $j = 0;
                        $k = 0;
                        $l = 0;
                        $m = 0;
                        $n = 0;
                        foreach($operationsArray as $operations):

                            foreach( $operations as $operation ):

                                /**
                                 * Add all operations without documentation to the parse report
                                 */
                                if( $operation['documentation'] === "" ):

                                    $parseReport['operations']['documentation']['operation'.($j+1)]              = array();
                                    $parseReport['operations']['documentation']['operation'.($j+1)]['name']      = ( "Operation documentation" );

                                    $parseReport['operations']['documentation']['operation'.($j+1)]['type']      = "info";

                                    $parseReport['operations']['documentation']['operation'.($j+1)]['value']     = "empty";
                                    $parseReport['operations']['documentation']['operation'.($j+1)]['valid']     = false;
                                    $parseReport['operations']['documentation']['operation'.($j+1)]['message']   = "Operation: " .$operation['name'].", Class: ".$operation['className'];

                                    $j++;

                                endif;

                                if( !empty( $operation['tags'] ) ):

                                    foreach($operation['tags'] as $tag):

                                        if( empty( $tag['file'] ) ):

                                            $parseReport['operations']['file']['operation'.($k+1)]              = array();
                                            $parseReport['operations']['file']['operation'.($k+1)]['name']      = "Operation file";
                                            $parseReport['operations']['file']['operation'.($k+1)]['tagName']   = $tag['name'];

                                            $parseReport['operations']['file']['operation'.($k+1)]['type']       = "info";

                                            $parseReport['operations']['file']['operation'.($k+1)]['value']     = "empty";
                                            $parseReport['operations']['file']['operation'.($k+1)]['valid']     = false;
                                            $parseReport['operations']['file']['operation'.($k+1)]['message']   = "Operation: " .$operation['name'].", Class: ".$operation['className'];

                                            $k++;

                                        endif;

                                        if( empty( $tag['tab'] ) ):

                                            $parseReport['operations']['tab']['operation'.($l+1)]              = array();
                                            $parseReport['operations']['tab']['operation'.($l+1)]['name']      = "Operation tab";
                                            $parseReport['operations']['tab']['operation'.($l+1)]['tagName']   = $tag['name'];

                                            $parseReport['operations']['tab']['operation'.($l+1)]['type']       = "info";

                                            $parseReport['operations']['tab']['operation'.($l+1)]['value']     = "empty";
                                            $parseReport['operations']['tab']['operation'.($l+1)]['valid']     = false;
                                            $parseReport['operations']['tab']['operation'.($l+1)]['message']   = "Operation: " .$operation['name'].", Class: ".$operation['className'];

                                            $l++;

                                        endif;

                                        if( empty( $tag['cell'] ) ):

                                            $parseReport['operations']['cell']['operation'.($m+1)]              = array();
                                            $parseReport['operations']['cell']['operation'.($m+1)]['name']      = "Operation cell";
                                            $parseReport['operations']['cell']['operation'.($m+1)]['tagName']   = $tag['name'];

                                            $parseReport['operations']['cell']['operation'.($m+1)]['type']       = "info";

                                            $parseReport['operations']['cell']['operation'.($m+1)]['value']     = "empty";
                                            $parseReport['operations']['cell']['operation'.($m+1)]['valid']     = false;
                                            $parseReport['operations']['cell']['operation'.($m+1)]['message']   = "Operation: " .$operation['name'].", Class: ".$operation['className'];

                                            $m++;

                                        elseif( !empty( $tag['cell'] && $tag['name'] !== "QR-Volgorde" ) ):

                                                if( !preg_match( $this->matchExcelFormat , $tag['cell']) ):

                                                    $parseReport['operations']['cell']['operation'.($n+1)]              = array();
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['name']      = "Operation cell format";
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['tagName']   = $tag['name'];

                                                    $parseReport['operations']['cell']['operation'.($n+1)]['type']       = "info";

                                                    $parseReport['operations']['cell']['operation'.($n+1)]['value']     = "invalid";
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['valid']     = false;
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['message']   = "Operation: " .$operation['name'].", Class: ".$operation['className'];

                                                    $n++;

                                                endif;

                                        endif;

                                    endforeach;

                                endif;

                            endforeach;

                        endforeach;

                    endif;

                    /**
                     * Get the last modified date and add it to the report array
                     */
                    $maxDate           = max(array_map('strtotime', $modifiedDates));
                    $maxDate           = date('Y-m-j H:i:s', $maxDate);
                    $modifiedClassName = "";

                    foreach( $parsedClasses as $parsedClass):

                        if( $parsedClass['modified'] === $maxDate ):
                            $modifiedClassName = $parsedClass['name'];
                            break;
                        endif;

                    endforeach;

                    $parseReport['lastModified']              = array();
                    $parseReport['lastModified']['name']      = ( "Last modified class" );

                    $parseReport['lastModified']['type']       = "valid";

                    $parseReport['lastModified']['value']     = $maxDate;
                    $parseReport['lastModified']['valid']     = true;
                    $parseReport['lastModified']['message']   = "Class name: ".$modifiedClassName;

                    /**
                     * Get all true roots and add them to the true roots array
                     */
                    $totalRoots = count( $roots );

                    if( $totalRoots !== 0 ):
                        for( $i = 0; $i < $totalRoots; $i++ ):
                            if( $roots[$i] === "true" ):
                                $trueRoots[] = $i;
                            endif;
                        endfor;
                    endif;

                    /**
                     * Count all true roots and determine if there is only one, also add it to the report array
                     */
                    $totalTrueRoots = count( $trueRoots );

                    $parseReport['totalRoots']              = array();
                    $parseReport['totalRoots']['name']      = ( $totalTrueRoots === 1 ? "Root" : "Roots" );

                    if( $totalTrueRoots === 1 ):
                        $parseReport['totalRoots']['type']  = "valid";
                    elseif( $totalTrueRoots === 0 || $totalTrueRoots > 1 ):
                        $parseReport['totalRoots']['type']  = "severe";
                    endif;

                    $parseReport['totalRoots']['value']     = $totalTrueRoots;
                    $parseReport['totalRoots']['valid']     = ( $totalTrueRoots !== 0 && $totalTrueRoots === 1 ? true : false );
                    $parseReport['totalRoots']['message']   = ( $totalTrueRoots !== 0 && $totalTrueRoots === 1 ? $totalTrueRoots.' root found' : ( $totalTrueRoots > 1 ? $totalTrueRoots.' roots found' : 'No roots found' ) );

                endif;

                /**
                 * Check for the xmi version and add it to the report array
                 */
                $parsedExtensionInfo = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseModelExtensionInfo();
                $extensionVersion    = $parsedExtensionInfo['model']['extender_info']['extenderID'];
                $xmi_version         = $parsedExtensionInfo['model']['xmi_version'];

                $parseReport['xmiVersion']              = array();
                $parseReport['xmiVersion']['name']      = "XMI version";

                if( !empty( $xmi_version ) && $xmi_version !== "2.1" ):
                    $parseReport['xmiVersion']['type']  = "info";
                elseif( empty( $xmi_version ) ):
                    $parseReport['xmiVersion']['type']  = "error";
                else:
                    $parseReport['xmiVersion']['type']  = "valid";
                endif;

                $parseReport['xmiVersion']['value']     = ( !empty( $xmi_version ) ? $xmi_version : "empty" );
                $parseReport['xmiVersion']['valid']     = ( !empty( $xmi_version ) && $xmi_version === "2.1" ? true : false );
                $parseReport['xmiVersion']['message']   = ( !empty( $xmi_version ) ? ( $xmi_version === "2.1" ? "Version found" : "Version found but other then 2.1" ) : "No version found" );

                /**
                 * Check for the extension version and add it to the report array
                 */
                $parseReport['extensionVersion']              = array();
                $parseReport['extensionVersion']['name']      = "EA extension version";

                if( !empty( $extensionVersion ) && $extensionVersion !== "6.5" ):
                    $parseReport['extensionVersion']['type']  = "info";
                elseif( empty( $extensionVersion ) ):
                    $parseReport['extensionVersion']['type']  = "error";
                else:
                    $parseReport['extensionVersion']['type']  = "valid";
                endif;

                $parseReport['extensionVersion']['value']     = $extensionVersion;
                $parseReport['extensionVersion']['valid']     = ( !empty( $extensionVersion ) && $extensionVersion === "6.5" ? true : false );
                $parseReport['extensionVersion']['message']   = ( !empty( $extensionVersion ) ? ( $extensionVersion === "6.5" ? "Version found" : "Version found but other then 6.5" ) : "No version found" );

            endif;

            /**
             * Check if all necessary items have been validated and conclude the total validation, also add it to the report array.
             */

            $parseReport['validation']              = array();
            $parseReport['validation']['name']      = "Validation";
            $parseReport['validation']['type']      = "severe";

            if( $isXML === false || $parseReport['xmiVersion']['valid'] === false):
                $validationValue = false;
                $validationValid = false;
                $validationMessage = "Validation failed";
            else:

                if( $isXML === true ):
                    $validationValue = true;
                    $validationValid = true;
                    $validationMessage = "Validation successful";
                endif;

            endif;

            $parseReport['validation']['value']     = $validationValue;
            $parseReport['validation']['valid']     = $validationValid;
            $parseReport['validation']['message']   = $validationMessage;

            return( $parseReport );

        }

    }

endif;