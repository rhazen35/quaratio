<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 17-Jul-16
 * Time: 19:39
 */

namespace application\classes\iOXMLEAValidator;

use \application\controller;
use \application\classes\iOXMLParser;
use application\classes\iOXMLModelParser;
use application\classes\iOXMLPrimitiveTypes;

( new controller\Controller( "class", "iOXMLModelParser", "iOXMLModelParser" ) )->request();
( new controller\Controller( "class", "iOXMLPrimitiveTypes", "iOXMLPrimitiveTypes" ) )->request();

if( !class_exists( "IOXMLEAValidator" ) ):

    class IOXMLEAValidator
    {

        protected $xmlFile;
        protected $matchExcelFormat = '/^[A-Za-z]+[0-9]+(\:)?(?(1)[A-Za-z]+[0-9]+|[0-9]?)$/';

        /**
         * IOXMLEAValidator constructor.
         * @param $xmlFile
         */
        public function __construct($xmlFile )
        {
            $this->xmlFile = $xmlFile;
        }

        /**
         * @return array
         */
        public function validate()
        {

            $parseReport = array();
            $severe = 0;
            $error = 0;
            $warning = 0;
            $info = 0;
            $valid = 0;

            /**
             * Check if the file is a xml
             */
            $isXML = ( new iOXMLParser\IOXMLParser( $this->xmlFile ) )->isXML();

            $parseReport['isXML']              = array();
            $parseReport['isXML']['name']      = "XML file";

            if( $isXML === true ):
                $parseReport['isXML']['type']  = "valid";
                $valid += 1;
            elseif( $isXML === false ):
                $parseReport['isXML']['type']  = "severe";
                $severe += 1;
            endif;

            $parseReport['isXML']['value']     = ( $isXML === true ? 1 : 0 );
            $parseReport['isXML']['valid']     = ( $isXML === true ? true : false );
            $parseReport['isXML']['message']   = ( $isXML === true ? "File is an XML" : "File is not an XML" );
            $parseReport['isXML']['info']      = "The uploaded file is not an XML file. Allowed extension: myModel.xmi [Note:] XMI is an application of XML, meaning it will be accepted as XML.";

            /**
             * Only continue validation when the file is an XML
             */
            if( $isXML === true ):

                /**
                 * Check for the xmi version and add it to the report array
                 */
                $parsedExtensionInfo = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseModelExtensionInfo();
                $extension           = $parsedExtensionInfo['model']['extender_info']['extender'];
                $extensionVersion    = $parsedExtensionInfo['model']['extender_info']['extenderID'];
                $xmi_version         = $parsedExtensionInfo['model']['xmi_version'];

                $parseReport['xmiVersion']              = array();
                $parseReport['xmiVersion']['name']      = "XMI version";

                if( !empty( $xmi_version ) && $xmi_version !== "2.1" ):
                    $parseReport['xmiVersion']['type']  = "info";
                    $info += 1;
                elseif( empty( $xmi_version ) ):
                    $parseReport['xmiVersion']['type']  = "error";
                    $error += 1;
                else:
                    $parseReport['xmiVersion']['type']  = "valid";
                    $valid += 1;
                endif;

                $parseReport['xmiVersion']['value']     = ( !empty( $xmi_version ) ? $xmi_version : "empty" );
                $parseReport['xmiVersion']['valid']     = ( !empty( $xmi_version ) && $xmi_version === "2.1" ? true : false );
                $parseReport['xmiVersion']['message']   = ( !empty( $xmi_version ) ? ( $xmi_version === "2.1" ? "Version found" : "Version found but other then 2.1" ) : "No version found" );
                $parseReport['xmiVersion']['info']      = "Couldn't find the XMI version which is crucial for reproducibility.";

                /**
                 * Check for the extension and add it to the report array
                 */
                $parseReport['extension']              = array();
                $parseReport['extension']['name']      = "EA extension";

                if( !empty( $extension ) && $extension !== "Enterprise Architect" ):
                    $parseReport['extension']['type']  = "info";
                    $info += 1;
                elseif( empty( $extension ) ):
                    $parseReport['extension']['type']  = "error";
                    $error += 1;
                else:
                    $parseReport['extension']['type']  = "valid";
                    $valid += 1;
                endif;

                $parseReport['extension']['value']     = $extension;
                $parseReport['extension']['valid']     = ( !empty( $extension ) && $extension === "Enterprise Architect" ? true : false );
                $parseReport['extension']['message']   = ( !empty( $extension ) ? ( $extension === "Enterprise Architect" ? "Extension found" : "Extension found but other then Enterprise Architect" ) : "No extension found" );
                $parseReport['extension']['info']      = "Couldn't find the EA extension which is crucial for reproducibility.";

                /**
                 * Check for the extension version and add it to the report array
                 */
                $parseReport['extensionVersion']              = array();
                $parseReport['extensionVersion']['name']      = "EA extension version";

                if( !empty( $extensionVersion ) && $extensionVersion !== "6.5" ):
                    $parseReport['extensionVersion']['type']  = "info";
                    $info += 1;
                elseif( empty( $extensionVersion ) ):
                    $parseReport['extensionVersion']['type']  = "error";
                    $error += 1;
                else:
                    $parseReport['extensionVersion']['type']  = "valid";
                    $valid += 1;
                endif;

                $parseReport['extensionVersion']['value']     = $extensionVersion;
                $parseReport['extensionVersion']['valid']     = ( !empty( $extensionVersion ) && $extensionVersion === "6.5" ? true : false );
                $parseReport['extensionVersion']['message']   = ( !empty( $extensionVersion ) ? ( $extensionVersion === "6.5" ? "Version found" : "Version found but other then 6.5" ) : "No version found" );
                $parseReport['extensionVersion']['info']      = "Couldn't find the EA extension version which is crucial for reproducibility.";

                /**
                 * Parse the classes and count them
                 * Check if at least one class can be found
                 */
                $parsedClasses    = ( new iOXMLModelParser\IOXMLModelParser( $this->xmlFile ) )->parseXMLClasses();
                $totalClasses     = count( $parsedClasses );

                $parseReport['totalClasses']              = array();
                $parseReport['totalClasses']['name']      = ( $totalClasses === 1 ? "Class" : "Classes" );

                if( $totalClasses > 0 ):
                    $parseReport['totalClasses']['type']  = "valid";
                    $valid += 1;
                elseif( $totalClasses === 0 ):
                    $parseReport['totalClasses']['type']  = "severe";
                    $severe += 1;
                endif;

                $parseReport['totalClasses']['value']     = $totalClasses;
                $parseReport['totalClasses']['valid']     = ( $totalClasses !== 0 ? true : false );
                $parseReport['totalClasses']['message']   = ( $totalClasses !== 0 ? $totalClasses.' classes found.' : "No classes found.");
                $parseReport['totalClasses']['info']      = "There is no model without classes.";

                if( $parsedClasses['duplicateNames'] > 0 ):

                    $parseReport['duplicateClasses']              = array();
                    $parseReport['duplicateClasses']['name']      = ( $parsedClasses['duplicateNames'] === 1 ? "Duplicate class" : "Duplicate classes" );
                    $parseReport['duplicateClasses']['type']      = "severe";
                    $parseReport['duplicateClasses']['value']     = $parsedClasses['duplicateNames'];
                    $parseReport['duplicateClasses']['valid']     = false;
                    $parseReport['duplicateClasses']['message']   = ( $parsedClasses['duplicateNames'] === 1 ? $parsedClasses['duplicateNames'].' class found.' : $parsedClasses['duplicateNames']." classes found.");
                    $parseReport['duplicateClasses']['info']      = "Duplicate classes are ignored and the model can not be processed.";

                    $severe += 1;

                endif;

                /**
                 * Collect data from the parsed classes
                 */
                $roots            = array();
                $trueRoots        = array();
                $modifiedDates    = array();
                $operations       = array();
                $classNames       = array();

                if( !empty( $parsedClasses ) ):

                    foreach( $parsedClasses as $parsedClass ):

                        /**
                         * Get all classes names
                         */
                        if( !empty( $parsedClass['name'] ) ):
                            $classNames[] = $parsedClass['name'];
                        endif;

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
                        $valid += 1;
                    elseif( $totalTrueRoots === 0 || $totalTrueRoots > 1 ):
                        $parseReport['totalRoots']['type']  = "severe";
                        $severe += 1;
                    endif;

                    $parseReport['totalRoots']['value']     = $totalTrueRoots;
                    $parseReport['totalRoots']['valid']     = ( $totalTrueRoots !== 0 && $totalTrueRoots === 1 ? true : false );
                    $parseReport['totalRoots']['message']   = ( $totalTrueRoots !== 0 && $totalTrueRoots === 1 ? $totalTrueRoots.' root found' : ( $totalTrueRoots > 1 ? $totalTrueRoots.' roots found' : 'No roots found' ) );
                    $parseReport['totalRoots']['info']      = "A root is needed to define the starting point, only one root is allowed.";

                    /**
                     * Get the last modified date and add it to the report array
                     */
                    $maxDate           = max(array_map('strtotime', $modifiedDates));
                    $maxDate           = date('Y-m-j H:i:s', $maxDate);
                    $modifiedClassName = "";

                    foreach( $parsedClasses as $parsedClass):

                        if( isset( $parsedClass['modified'] ) && $parsedClass['modified'] === $maxDate ):
                            $modifiedClassName = $parsedClass['name'];
                            break;
                        endif;

                    endforeach;

                    $parseReport['lastModified']              = array();
                    $parseReport['lastModified']['name']      = ( "Last modified class" );

                    if( !empty( $modifiedDates ) ):
                        $parseReport['lastModified']['type']  = "valid";
                        $valid += 1;
                    else:
                        $parseReport['lastModified']['type']  = "warning";
                        $warning += 1;
                    endif;


                    $parseReport['lastModified']['value']     = $maxDate;
                    $parseReport['lastModified']['valid']     = true;
                    $parseReport['lastModified']['message']   = ( !empty( $modifiedClassName ) ? "<strong>Class: </strong> ".$modifiedClassName : " No class name specified. " );

                    /**
                     * Get the fields from the parsed classes and validate them.
                     */
                    if( !empty( $parsedClasses ) ):

                        $i = 0;
                        $j = 0;
                        foreach( $parsedClasses as $parsedClass ):

                            if( !empty( $parsedClass['fields'] ) ):

                                foreach( $parsedClass['fields'] as $field ):

                                    /**
                                     * Validate the initial value by matching it with the data type.
                                     */
                                    $validInitialValue = ( new iOXMLPrimitiveTypes\IOXMLPrimitiveTypes( $field['data_type'], $field['initialValue'] ) )->validate();

                                    if( isset( $field['documentation'] ) && $field['documentation'] === ""  ):

                                        $parseReport['initialValueType']['documentation']['type'.($i+1)]              = array();
                                        $parseReport['initialValueType']['documentation']['type'.($i+1)]['name']      = "Attribute documentation.";
                                        $parseReport['initialValueType']['documentation']['type'.($i+1)]['type']      = "warning";
                                        $parseReport['initialValueType']['documentation']['type'.($i+1)]['value']     = "empty";
                                        $parseReport['initialValueType']['documentation']['type'.($i+1)]['valid']     = false;
                                        $parseReport['initialValueType']['documentation']['type'.($i+1)]['message']   = "There is no documentation specified with this attribute. <strong>Class:</strong> ".$field['class_name']." <strong>Attribute:</strong> ".$field['input_name'];
                                        $parseReport['initialValueType']['documentation']['type'.($i+1)]['info']      = "The documentation is missing and used to provide user information.";

                                        $warning += 1;
                                        $i++;

                                    endif;

                                    if( $validInitialValue === false ):

                                        $parseReport['initialValueType']['initial']['type'.($j+1)]              = array();
                                        $parseReport['initialValueType']['initial']['type'.($j+1)]['name']      = "Initial value data type";
                                        $parseReport['initialValueType']['initial']['type'.($j+1)]['type']      = "warning";
                                        $parseReport['initialValueType']['initial']['type'.($j+1)]['value']     = $field['initialValue'];
                                        $parseReport['initialValueType']['initial']['type'.($j+1)]['valid']     = false;
                                        $parseReport['initialValueType']['initial']['type'.($j+1)]['message']   = "Invalid data type. Allowed type: ".$field['data_type']." <strong>Class:</strong> ".$field['class_name']." <strong>Attribute:</strong> ".$field['input_name'];
                                        $parseReport['initialValueType']['initial']['type'.($j+1)]['info']      = "The initial value doesn't match the data type.";

                                        $warning += 1;
                                        $j++;

                                    endif;

                                endforeach;

                            endif;

                        endforeach;

                    endif;

                    /**
                     * Get all operations from the parsed classes and validate them
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
                        $o = 0;
                        $p = 0;
                        foreach($operationsArray as $operations):

                            foreach( $operations as $operation ):

                                /**
                                 * Add all operations without documentation to the parse report
                                 */
                                if( $operation['documentation'] === "" ):

                                    $parseReport['operations']['documentation']['operation'.($j+1)]              = array();
                                    $parseReport['operations']['documentation']['operation'.($j+1)]['name']      = ( "Operation documentation" );

                                    $parseReport['operations']['documentation']['operation'.($j+1)]['type']      = "warning";

                                    $parseReport['operations']['documentation']['operation'.($j+1)]['value']     = "empty";
                                    $parseReport['operations']['documentation']['operation'.($j+1)]['valid']     = false;
                                    $parseReport['operations']['documentation']['operation'.($j+1)]['message']   = "<strong>Class: </strong>".$operation['className']." <strong>Operation:</strong> ".$operation['name']. " <strong>Attribute:</strong> Documentation";
                                    $parseReport['operations']['documentation']['operation'.($j+1)]['info']      = "The documentation is missing and used to provide user information.";

                                    $warning += 1;
                                    $j++;

                                endif;

                                if( !empty( $operation['tags'] ) ):

                                    $tagsArray = array();

                                    foreach($operation['tags'] as $tag):

                                        if( !empty( $tag['name'] ) && $tag['name'] === "QR-Volgorde" ):
                                            $tagsArray[] = "QR-Volgorde";
                                        endif;

                                        if( empty( $tag['file'] ) && $tag['name'] !== "QR-Volgorde" ):

                                            $parseReport['operations']['file']['operation'.($k+1)]              = array();
                                            $parseReport['operations']['file']['operation'.($k+1)]['name']      = "Operation file";
                                            $parseReport['operations']['file']['operation'.($k+1)]['tagName']   = $tag['name'];
                                            $parseReport['operations']['file']['operation'.($k+1)]['type']      = "error";
                                            $parseReport['operations']['file']['operation'.($k+1)]['value']     = "empty";
                                            $parseReport['operations']['file']['operation'.($k+1)]['valid']     = false;
                                            $parseReport['operations']['file']['operation'.($k+1)]['message']   = "<strong>Class: </strong>".$operation['className']." <strong>Operation:</strong> ".$operation['name']. " <strong>Attribute:</strong> Tags->file";
                                            $parseReport['operations']['file']['operation'.($k+1)]['info']      = "Without a specified file, input and output can not be handled properly.";

                                            $error += 1;
                                            $k++;

                                        endif;

                                        if( empty( $tag['tab'] ) && $tag['name'] !== "QR-Volgorde" ):

                                            $parseReport['operations']['tab']['operation'.($l+1)]              = array();
                                            $parseReport['operations']['tab']['operation'.($l+1)]['name']      = "Operation tab";
                                            $parseReport['operations']['tab']['operation'.($l+1)]['tagName']   = $tag['name'];
                                            $parseReport['operations']['tab']['operation'.($l+1)]['type']      = "error";
                                            $parseReport['operations']['tab']['operation'.($l+1)]['value']     = "empty";
                                            $parseReport['operations']['tab']['operation'.($l+1)]['valid']     = false;
                                            $parseReport['operations']['tab']['operation'.($l+1)]['message']   = "<strong>Class: </strong>".$operation['className']." <strong>Operation:</strong> ".$operation['name']. " <strong>Attribute:</strong> Tags->tab";
                                            $parseReport['operations']['tab']['operation'.($l+1)]['info']      = "No tab/sheet has been specified, default is first tab/sheet.";

                                            $error += 1;
                                            $l++;

                                        endif;

                                        if( empty( $tag['cell'] ) ):

                                            $parseReport['operations']['cell']['operation'.($m+1)]              = array();
                                            $parseReport['operations']['cell']['operation'.($m+1)]['name']      = "Operation cell";
                                            $parseReport['operations']['cell']['operation'.($m+1)]['tagName']   = $tag['name'];
                                            $parseReport['operations']['cell']['operation'.($m+1)]['type']      = "error";
                                            $parseReport['operations']['cell']['operation'.($m+1)]['value']     = "empty";
                                            $parseReport['operations']['cell']['operation'.($m+1)]['valid']     = false;
                                            $parseReport['operations']['cell']['operation'.($m+1)]['message']   = "<strong>Class: </strong>".$operation['className']." <strong>Operation:</strong> ".$operation['name']. " <strong>Attribute:</strong> Tags->cell";
                                            $parseReport['operations']['cell']['operation'.($m+1)]['info']      = ( $tag['name'] === "QR-Volgorde" ? "Without an order operations can not be handled properly." : "Without a cell input and ouput can not be handled properly." );

                                            $error += 1;
                                            $m++;

                                        elseif( !empty( $tag['cell'] && $tag['name'] !== "QR-Volgorde" ) ):

                                                if( !preg_match( $this->matchExcelFormat , $tag['cell']) ):

                                                    $parseReport['operations']['cell']['operation'.($n+1)]              = array();
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['name']      = "Operation cell format";
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['tagName']   = $tag['name'];
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['type']      = "error";
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['value']     = "invalid";
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['valid']     = false;
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['message']   = "<strong>Class: </strong>".$operation['className']." <strong>Operation:</strong> ".$operation['name']. " <strong>Attribute:</strong> Tags->cell format";
                                                    $parseReport['operations']['cell']['operation'.($n+1)]['info']      = "The format of the specified cell is incorrect. Please us the following formats: A1 or A1:A2";

                                                    $error += 1;
                                                    $n++;

                                                endif;

                                        endif;

                                    endforeach;

                                    if( !in_array( "QR-Volgorde", $tagsArray ) ):

                                        $parseReport['operations']['order']['operation'.($p+1)]              = array();
                                        $parseReport['operations']['order']['operation'.($p+1)]['name']      = "Operation order";
                                        $parseReport['operations']['order']['operation'.($p+1)]['tagName']   = $tag['name'];
                                        $parseReport['operations']['order']['operation'.($p+1)]['type']      = "info";
                                        $parseReport['operations']['order']['operation'.($p+1)]['value']     = "empty";
                                        $parseReport['operations']['order']['operation'.($p+1)]['valid']     = false;
                                        $parseReport['operations']['order']['operation'.($p+1)]['message']   = "<strong>Class: </strong>".$operation['className']." <strong>Operation:</strong> ".$operation['name']. " <strong>Attribute:</strong> Tags->file";
                                        $parseReport['operations']['order']['operation'.($p+1)]['info']      = "Operation order is not specified and will be executed alphabetically.";

                                        $info += 1;
                                        $p++;

                                    endif;

                                else:

                                    $parseReport['operations']['tags']['operation'.($o+1)]              = array();
                                    $parseReport['operations']['tags']['operation'.($o+1)]['name']      = "Operation tags";
                                    $parseReport['operations']['tags']['operation'.($o+1)]['type']      = "info";
                                    $parseReport['operations']['tags']['operation'.($o+1)]['value']     = "empty";
                                    $parseReport['operations']['tags']['operation'.($o+1)]['valid']     = false;
                                    $parseReport['operations']['tags']['operation'.($o+1)]['message']   = "No operations tags found. <strong>Class:</strong> ".$operation['className'];
                                    $parseReport['operations']['tags']['operation'.($o+1)]['info']      = "Operation tags are needed to handle input and output.";

                                    $info += 1;
                                    $o++;

                                endif;

                            endforeach;

                        endforeach;

                    endif;

                endif;

            endif;

            /**
             * Check if all necessary items have been validated and conclude the total validation, also add it to the report array.
             */

            $operationCheck = false;
            if( !empty( $parseReport['operations'] ) ):

                $i = 0;
                foreach( $parseReport['operations'] as $key => $value ):

                    if( isset( $parseReport['operations'][$key]['operation'.($i+1)] ) ):

                        $operation = $parseReport['operations'][$key]['operation'.($i+1)];

                        if( $operation['type'] === "error" ):

                            $operationCheck = false;

                        else:

                            $operationCheck = true;

                        endif;

                    else:

                        break;

                    endif;

                        $i++;
                endforeach;

            endif;

            $parseReport['totalErrorTypes']             = array();
            $parseReport['totalErrorTypes']['severe']   = $severe;
            $parseReport['totalErrorTypes']['error']    = $error;
            $parseReport['totalErrorTypes']['warning']  = $warning;
            $parseReport['totalErrorTypes']['info']     = $info;
            $parseReport['totalErrorTypes']['valid']    = $valid;

            $parseReport['validation']              = array();
            $parseReport['validation']['name']      = "Validation";
            $parseReport['validation']['type']      = "severe";

            if( !empty( $severe )
                || !empty( $error )
            ):
                $validationValue = false;
                $validationValid = false;
                $validationMessage = "Invalid XML";
            else:

                $validationValue = true;
                $validationValid = true;
                $validationMessage = "Valid XML";

            endif;

            $parseReport['validation']['value']     = $validationValue;
            $parseReport['validation']['valid']     = $validationValid;
            $parseReport['validation']['message']   = $validationMessage;

            return( $parseReport );

        }

    }

endif;