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
        public function parseXMLClasses()
        {

            /**
             * Prepare the model
             */
            $model      = $this->xmlFile;
            $xmlObject  = ( new xmlController\XmlController( $model, "fileToSimpleXmlObject", "" ) )->request(); // Convert model to simple xml object

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
                $xmiNamespace = $namespaces['xmi'];

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
                     * @var  $type
                     * @var  $idref
                     */
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

                    /** Tags for excel file, sheet and cell */
                    $tags = $element->tags->tag;
                    $totalTags = count($tags);

                    if( !empty( $totalTags ) ):

                        for($l = 0; $l < $totalTags; $l++):

                            $tagName  = (string) $element->tags->tag[$l]->attributes()->name;
                            $tagValue = (string) $element->tags->tag[$l]->attributes()->value;

                            if( !empty( $tagValue ) ):
                                list($cell, $tab, $file) = array_pad(explode(",", $tagValue, 3),3, null);

                                $classArray[$className]['tags'][$tagName]['file'] = trim($file);
                                $classArray[$className]['tags'][$tagName]['tab']  = trim($tab);
                                $classArray[$className]['tags'][$tagName]['cell'] = trim($cell);

                            endif;

                        endfor;

                    endif;


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

                    /**
                     * Tags for excel address
                     */
                    $tags = $operations->operation[$i]->tags->tag;
                    $totalTags = count($tags);

                    if( !empty( $tags ) && $tags !== 0 ):

                        for($l = 0; $l < $totalTags; $l++):

                            if( !empty( $operations->operation[$l]->tags->tag[$l] ) ):
                                $tagName  = (string) $operations->operation[$l]->tags->tag[$l]->attributes()->name;
                                $tagValue = (string) $operations->operation[$i]->tags->tag[$l]->attributes()->value;
                            endif;

                            if( !empty( $tagValue ) ):
                                list($cell, $tab, $file) = array_pad(explode(",", $tagValue, 3),3, null);

                                $classArray[$className]['operations'][$operationName]['tags'][$tagName]['file'] = trim($file);
                                $classArray[$className]['operations'][$operationName]['tags'][$tagName]['tab']  = trim($tab);
                                $classArray[$className]['operations'][$operationName]['tags'][$tagName]['cell'] = trim($cell);

                            endif;

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

                    /**
                     * Tags for excel address
                     */
                    $tags = $attributes->attribute[$i]->tags->tag;
                    $totalTags = count($tags);

                    if( !empty( $totalTags ) ):

                        for($l = 0; $l < $totalTags; $l++):

                            $tagName  = (string) $attributes->attribute[$i]->tags->tag[$l]->attributes()->name;
                            $tagValue = (string) $attributes->attribute[$i]->tags->tag[$l]->attributes()->value;

                            if( !empty( $tagValue ) ):

                                list($cell, $tab, $file) = array_pad(explode(",", $tagValue, 3),3, null);

                                $classArray[$className]['tags'][$inputName]['tags'][$tagName]['file'] = trim($file);
                                $classArray[$className]['tags'][$inputName]['tags'][$tagName]['tab']  = trim($tab);
                                $classArray[$className]['tags'][$inputName]['tags'][$tagName]['cell'] = trim($cell);

                            endif;

                        endfor;

                    endif;

                endfor;

                $links      = $element->children()->links;
                $totalLinks = count( $links->Aggregation );

                $classArray[$className]['links'] = array();

                for($i = 0; $i < $totalLinks; $i++):

                    $id     = (string) $links->Aggregation[$i]->attributes($xmiNamespace)->id;
                    $end    = (string) $links->Aggregation[$i]->attributes()->end;
                    $start  = (string) $links->Aggregation[$i]->attributes()->start;

                    $classArray[$className]['links']['link'.($i+1)]['id']      = $id;
                    $classArray[$className]['links']['link'.($i+1)]['end']     = $end;
                    $classArray[$className]['links']['link'.($i+1)]['start']   = $start;

                endfor;

            endforeach;

            /**
             * Return the class array
             */

            return( $classArray );

        }

        public function parseModelExtensionInfo()
        {

            /**
             * Set xml path, xml object and pass it trough the controller to get a specific node
             */
            $xmiPath             = "//xmi:Extension[@extenderID]"; // Path for extender information
            $xmlObject           = ( new xmlController\XmlController( $this->xmlFile, "fileToSimpleXmlObject", "" ) )->request(); // Convert model to simple xml object
            $extensionAttributes = ( new xmlController\XmlController( $xmlObject, "getNode", $xmiPath ) )->request(); // Get the extension attributes

            $xmiVersionPath      = "//xmi:XMI"; // Path for extender information
            $xmiVersions         = ( new xmlController\XmlController( $xmlObject, "getNode", $xmiVersionPath ) )->request(); // Get the extension attributes

            $modelExtensionInfo = array();

            foreach ( $xmiVersions as $xmiVersion ):

                $namespaces   = $xmiVersion->getNameSpaces( true );
                $xmiNamespace = $namespaces['xmi'];
                $version      = (string) $xmiVersion->attributes( $xmiNamespace )->version;

                $modelExtensionInfo['model']['xmi_version'] = $version;

            endforeach;

            /**
             * Collect the model's extension information
             */

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

        public function parseConnectors()
        {

            $model      = $this->xmlFile;
            $xmlObject  = ( new xmlController\XmlController( $model, "fileToSimpleXmlObject", "" ) )->request(); // Convert model to simple xml object

            /**
             * Define the connectors path and get the connectors
             */
            $connectorPath    = "//xmi:Extension/connectors/connector"; // Path for connectors in the extension
            $connectors       = ( new xmlController\XmlController( $xmlObject, "getNode", $connectorPath ) )->request(); // Get the elements

            $connectorArray['connectors'] = array();

            $i = 0;
            foreach($connectors as $connector):

                /** @var  $namespaces */
                $namespaces   = $connector->getNameSpaces( true );
                $xmiNamespace = $namespaces['xmi'];

                /** Add the connector idref to the connectors array */
                $idref = (string) $connector->attributes( $xmiNamespace )->idref;
                $connectorArray['connectors']['connector'.($i+1)]['idref'] = $idref;

                /**
                 * Get the connector source and add it as an array to the connectors array
                 */
                $source = $connector->children()->source;
                $connectorArray['connectors']['connector'.($i+1)]['source'] = array();

                /** Add the source idref to the source array */
                $idref = (string) $source->attributes( $xmiNamespace )->idref;
                $connectorArray['connectors']['connector'.($i+1)]['source']['idref'] = $idref;

                $modelName      = (string) $source->children()->model->attributes()->name;
                $modelType      = (string) $source->children()->model->attributes()->type;
                $modelEALocalId = (string) $source->children()->model->attributes()->ea_localid;
                $multiplicity   = (string) $source->children()->type->attributes()->multiplicity;

                $connectorArray['connectors']['connector'.($i+1)]['source']['model']['name']         = $modelName;
                $connectorArray['connectors']['connector'.($i+1)]['source']['model']['type']         = $modelType;
                $connectorArray['connectors']['connector'.($i+1)]['source']['model']['ea_localid']   = $modelEALocalId;
                $connectorArray['connectors']['connector'.($i+1)]['source']['model']['multiplicity'] = $multiplicity;

                /**
                 * Get the connector target and add it as an array to the connectors array
                 */
                $target = $connector->children()->target;
                $connectorArray['connectors']['connector'.($i+1)]['target'] = array();

                /** Add the target idref to the target array */
                $idref = (string) $target->attributes( $xmiNamespace )->idref;
                $connectorArray['connectors']['connector'.($i+1)]['target']['idref'] = $idref;

                $modelName      = (string) $target->children()->model->attributes()->name;
                $modelType      = (string) $target->children()->model->attributes()->type;
                $modelEALocalId = (string) $target->children()->model->attributes()->ea_localid;
                $multiplicity   = (string) $target->children()->type->attributes()->multiplicity;

                $connectorArray['connectors']['connector'.($i+1)]['target']['model']['name']         = $modelName;
                $connectorArray['connectors']['connector'.($i+1)]['target']['model']['type']         = $modelType;
                $connectorArray['connectors']['connector'.($i+1)]['target']['model']['ea_localid']   = $modelEALocalId;
                $connectorArray['connectors']['connector'.($i+1)]['target']['model']['multiplicity'] = $multiplicity;

                $i++;

            endforeach;

            return( $connectorArray );

        }
    }

endif;

?>