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
            $xmlObject  = ( new xmlController\XmlController( $model, "fileToSimpleXmlObject", "" ) )->request(); // convert model to simple xml object

            /**
             * Set xml paths and pass them trough the controller to get a specific node
             */
            $xmiPath     = "//xmi:Extension[@extenderID]"; // Path for extender information
            $elementPath = "//xmi:Extension/elements/element"; // Path for elements

            $extensionAttributes = ( new xmlController\XmlController( $xmlObject, "getNode", $xmiPath ) )->request(); // Get the extension attributes
            $elements            = ( new xmlController\XmlController( $xmlObject, "getNode", $elementPath ) )->request(); // Get the elements

            /**
             * Collect the model's extension information
             */
            $modelExtensionInfo = array();

            foreach ( $extensionAttributes as $extensionAttribute ):

                $extender   = ( new xmlController\XmlController( $extensionAttribute, "getNodeAttribute", "extender" ) )->request();
                $extenderID = ( new xmlController\XmlController( $extensionAttribute, "getNodeAttribute", "extenderID" ) )->request();

                // Add the extender and extender id to the model array
                $modelExtensionInfo['model']['extender_info']['extender']   = $extender;
                $modelExtensionInfo['model']['extender_info']['extenderID'] = $extenderID;

            endforeach;

            /**
             * Collect the required model information
             *
             * - Required data is specified in the model interface supplied by the modeller.
             * - Required data has standards which are expressed in the QuaRatio XML Standards(QXS)
             */

            foreach ( $elements as $element ):

                $namespaces = $element->getNameSpaces( true );

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

                    /** Class name */
                    $className = ( new xmlController\XmlController( $element, "getNodeAttribute", "name" ) )->request();

                    if ($isRoot === "true"):
                        $input_fields[$className]['Root'] = "true";
                    else:
                        $input_fields[$className]['Root'] = "false";
                    endif;

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
                    $input_fields[$className]['type']  = $type;
                    $input_fields[$className]['idref'] = $idref;

                    /**
                     * Non-namespaced attributes
                     * Scope, documentation and abstract to class array
                     */
                    $input_fields[$className]['scope']         = ( new xmlController\XmlController( $element, "getNodeAttribute", "scope" ) )->request();
                    $input_fields[$className]['documentation'] = ( new xmlController\XmlController( $property, "getNodeAttribute", "documentation" ) )->request();
                    $input_fields[$className]['abstract']      = $abstract;

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

                    /** Create an array with the operation name and add it to the operations array */

                    for ($i = 0; $i < $totalOperations; $i++):

                        $operationName = (string) $operations->operation[$i]->attributes()->name;
                        $input_fields[$className]['operations'][$operationName] = array();

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
                        $input_fields[$className]['fields'][$inputName] = array();

                        /** input field documentation */
                        $inputFieldDocumentation = (string) $attributes->attribute[$i]->documentation->attributes()->value;
                        $input_fields[$className]['fields'][$inputName]['documentation'] = $inputFieldDocumentation;

                        /** Input field data type */
                        $inputFieldDataType = (string) $attributes->attribute[$i]->properties->attributes()->type;
                        $input_fields[$className]['fields'][$inputName]['data_type'] = $inputFieldDataType;

                    endfor;

                endforeach;

            endforeach;

            /**
             * Merge the model and the class array and return the parsed xml
             */

            $parsedXML = array_merge($modelExtensionInfo, $input_fields);

            return($parsedXML);

        }
    }

endif;

?>