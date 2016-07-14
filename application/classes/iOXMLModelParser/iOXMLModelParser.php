<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 14-7-2016
 * Time: 01:52
 */

namespace application\classes\iOXMLModelParser;

use \application\core;
use application\xmlController;

/**
 * Get the xml controller
 */
( new core\Core( "xmlController", "xmlController", "xmlController" ) )->request();

if( !class_exists( "IOXMLModelParser" ) ):

    class IOXMLModelParser
    {

        protected $xmlFile;

        /**
         * IOXMLModelParser constructor.
         * @param $xmlFile
         */
        public function __construct( $xmlFile )
        {
            $this->xmlFile = $xmlFile;
        }

        /**
         * @return array
         */
        public function parseXMLModel()
        {

            /**
             * Prepare the model
             */
            $model      = $this->xmlFile;
            $xmlObject  = ( new xmlController\XmlController( $model, "fileToSimpleXmlObject", "" ) )->request(); // Convert model to simple xml object

            /**
             * Get the model extension info array
             */
            $modelExtensionInfo = $this->parseModelExtensionInfo( $xmlObject );

            /**
             * Define the elements path and get the elements
             */
            $elementPath    = "//xmi:Extension/elements/element"; // Path for elements in the extension
            $elements       = ( new xmlController\XmlController( $xmlObject, "getNode", $elementPath ) )->request(); // Get the elements

            /**
             * Collect the required model information
             *
             * - Required data is specified in the model interface supplied by the modeller.
             * - Required data has standards which are expressed in the QuaRatio XML Standards(QXS) for Sparx Enterprice UML Modelling.
             */
            foreach ( $elements as $element ):

                /** @var  $namespaces */
                $namespaces = $element->getNameSpaces( true );

                /** Class name */
                $className = ( new xmlController\XmlController( $element, "getNodeAttribute", "name" ) )->request();

                /**
                 * Get the element properties to access the isRoot value
                 */
                $properties = $element->children()->properties;

                foreach ( $properties as $property ):

                    /**
                     * Get the isRoot, this class will be shown first.
                     * The root also defines the base which the template generator will request
                     */
                    $isRoot = ( new xmlController\XmlController( $property, "getNodeAttribute", "isRoot" ) )->request();

                    /**
                     * Detailed class information
                     */

                    if ($isRoot === "true"):
                        $classArray[$className]['Root'] = "true";
                    else:
                        $classArray[$className]['Root'] = "false";
                    endif;

                    /**
                     * Get the model node and add to the model node array
                     */

                    $modelNode = $element->children()->model;
                    $classArray[$className]['package']  = ( new xmlController\XmlController( $modelNode, "getNodeAttribute", "package" ) )->request();
                    $classArray[$className]['package2'] = ( new xmlController\XmlController( $modelNode, "getNodeAttribute", "package2" ) )->request();

                    /** @var  $abstract */
                    $abstract = ( new xmlController\XmlController( $property, "getNodeAttribute", "isAbstract" ) )->request();

                    /**
                     * Get the namespace attributes from the class
                     *
                     * @var  $xmiNamespace
                     * @var  $type
                     * @var  $idref
                     */
                    $xmiNamespace = $namespaces['xmi'];
                    $type         = (string) $element->attributes( $xmiNamespace )->type;
                    $idref        = (string) $element->attributes( $xmiNamespace )->idref;

                    /** Namespaced type and idref to class array*/
                    $classArray[$className]['type']  = $type;
                    $classArray[$className]['idref'] = $idref;

                    /**
                     * Non-namespaced attributes
                     * Scope, documentation and abstract to class array
                     */
                    $classArray[$className]['scope']         = ( new xmlController\XmlController( $element, "getNodeAttribute", "scope" ) )->request();
                    $classArray[$className]['documentation'] = ( new xmlController\XmlController( $property, "getNodeAttribute", "documentation" ) )->request();
                    $classArray[$className]['abstract']      = $abstract;

                endforeach;

                /**
                 * Get the operations of the class.
                 *
                 * Operations are calculator calculations.
                 * The input data will be added to match the collected excel cells
                 *
                 * @var  $operations
                 * @var  $totalOperations
                 */

                $operations      = $element->children()->operations;
                $totalOperations = count( $operations->operation );

                /**
                 * Create an array with the operation name and add it to the operations array
                 */

                for ($i = 0; $i < $totalOperations; $i++):

                    /** @var  $operationName */
                    $operationName  = (string) $operations->operation[$i]->attributes()->name;

                    /**
                     * idref, position, type, abstract, documentation
                     */
                    $idref          = (string) $operations->operation[$i]->attributes( $xmiNamespace )->idref;
                    $position       = (string) $operations->operation[$i]->properties->attributes()->position;
                    $type           = (string) $operations->operation[$i]->type->attributes()->type;
                    $abstract       = (string) $operations->operation[$i]->type->attributes()->isAbstract;
                    $documentation  = (string) $operations->operation[$i]->documentation->attributes()->value;

                    /** Define the operations array */
                    $classArray[$className]['operations'][$operationName] = array();

                    /**
                     * Add idref, position, type, abstract and documentation to the operations array.
                     */
                    $classArray[$className]['operations'][$operationName]['idref']          = $idref;
                    $classArray[$className]['operations'][$operationName]['position']       = $position;
                    $classArray[$className]['operations'][$operationName]['type']           = $type;
                    $classArray[$className]['operations'][$operationName]['abstract']       = $abstract;
                    $classArray[$className]['operations'][$operationName]['documentation']  = $documentation;

                    /**
                     * Behaviour
                     *
                     * Defines the behaviour of the I/O procedure to target the calculator
                     *
                     * Current procedure is PHPExcel using excel sheets as the calculator service
                     */

                    /** Get the value string */
                    $behaviour      =  (string) $operations->operation[$i]->behaviour->attributes()->value;
                    /** Convert string to xml */
                    $behaviour      = "<?xml version='1.0' encoding='UTF-8'?><element>".$behaviour."</element>";
                    /** @var Load the string in a simple xml object */
                    $xml = simplexml_load_string($behaviour);
                    /** Count the total excel files */
                    $totalFiles = count($xml->excel);

                    if(!empty($totalFiles)):

                        /** Add the file, sheet and cells to the excel array */
                        for ($k = 0; $k < $totalFiles; $k++):
                            $classArray[$className]['operations'][$operationName]['behaviour']['excel'.($k+1)]['file']  = (string) $xml->excel[$k]->file;
                            $classArray[$className]['operations'][$operationName]['behaviour']['excel'.($k+1)]['tab']   = (string) $xml->excel[$k]->tab;
                            $classArray[$className]['operations'][$operationName]['behaviour']['excel'.($k+1)]['cells'] = (string) $xml->excel[$k]->cells;
                        endfor;

                    endif;

                endfor;

                /**
                 * Input fields and enumeration/data type fields
                 *
                 * - Data types of fields can be found under their own class in the xml model
                 */
                $attributes      = $element->children()->attributes;
                $totalAttributes = count( $attributes->attribute );

                /** Loop trough the attributes of the class */
                for ($i = 0; $i < $totalAttributes; $i++):

                    /** Input field names /enumeration/data type names */
                    $inputName = (string) $attributes->attribute[$i]->attributes()->name;
                    $classArray[$className]['fields'][$inputName] = array();

                    /** Input field documentation */
                    $inputFieldDocumentation = (string) $attributes->attribute[$i]->documentation->attributes()->value;
                    $classArray[$className]['fields'][$inputName]['documentation'] = $inputFieldDocumentation;

                    /** Input field data type */
                    $inputFieldDataType = (string) $attributes->attribute[$i]->properties->attributes()->type;
                    $classArray[$className]['fields'][$inputName]['data_type'] = $inputFieldDataType;

                endfor;

            endforeach;

            /**
             * Merge the model and the class array and return the parsed xml
             */

            $parsedXML = array_merge( $modelExtensionInfo, $classArray );

            return( $parsedXML );

        }

        private function parseModelExtensionInfo( $xmlObject )
        {

            /**
             * Set xml path and pass them trough the controller to get a specific node
             */
            $xmiPath             = "//xmi:Extension[@extenderID]"; // Path for extender information
            $extensionAttributes = ( new xmlController\XmlController( $xmlObject, "getNode", $xmiPath ) )->request(); // Get the extension attributes

            /**
             * Collect the model's extension information
             */
            $modelExtensionInfo = array();

            foreach ( $extensionAttributes as $extensionAttribute ):

                /** Get the node attribute */
                $extender   = ( new xmlController\XmlController( $extensionAttribute, "getNodeAttribute", "extender" ) )->request();
                $extenderID = ( new xmlController\XmlController( $extensionAttribute, "getNodeAttribute", "extenderID" ) )->request();

                /** Add the extender and extender id to the model array */
                $modelExtensionInfo['model']['extender_info']['extender']   = $extender;
                $modelExtensionInfo['model']['extender_info']['extenderID'] = $extenderID;

            endforeach;

            /** Return the model extension info array */
            return($modelExtensionInfo);

        }
    }

endif;

?>