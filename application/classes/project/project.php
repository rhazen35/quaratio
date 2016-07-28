<?php
/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 28-7-2016
 * Time: 15:46
 */

namespace application\classes\project;

use \application\model\service;

if( !class_exists( "Project" ) ):

    class Project
    {

        protected $type;

        public function __construct( $type )
        {
            $this->type = $type;
        }

        public function request( $params )
        {

            switch( $this->type ):

                case"newProject":
                    return( $this->newProject( $params ) );
                    break;
                case"getProjectById":
                    return( $this->getProjectById( $params ) );
                    break;
                case"saveModelJoinTable":
                    $this->saveModelJoinTable( $params );
                    break;
                case"getAllProjectsByUser":
                    return( $this->getAllProjectsByUser() );
                    break;
                case"getModelIdByProjectId":
                    return( $this->getModelIdByProjectId( $params ) );
                break;
            endswitch;

        }

        private function newProject( $params )
        {

            $date        = date( "Y-m-d" );
            $time        = date( "H:i:s" );
            $userId      = !empty( $_SESSION['userId'] ) ? $_SESSION['userId'] : "";
            $id          = $output = "";

            $sql        = "CALL proc_newProject(?,?,?,?,?,?,?)";
            $data       = array(
                "id"            => $id,
                "user_id"       => $userId,
                'name'          => $params['name'],
                'description'   => $params['description'],
                "date"          => $date,
                "time"          => $time,
                "output"        => $output
            );
            $format     = array("iissssi");

            $type       = "createWithOutput";
            $database   = "quaratio";

            $lastInsertId = ( new service\Service( $type, $database ) )->dbAction( $sql, $data, $format );

            return( $lastInsertId );

        }

        private function getProjectById( $params )
        {

            $sql        = "CALL proc_getProjectById(?)";
            $data       = array("id" => $params['id']);
            $format     = array("i");

            $type       = "read";

            $returnData = ( new service\Service( $type, "quaratio" ) )->dbAction( $sql, $data, $format );

            $returnArray = array();

            foreach( $returnData as $returnDat ):

                $returnArray['name'] = $returnDat['name'];
                $returnArray['description'] = $returnDat['description'];
                $returnArray['date'] = $returnDat['date'];
                $returnArray['time'] = $returnDat['time'];

            endforeach;

            return( $returnArray );

        }

        private function saveModelJoinTable( $params )
        {

            $userId      = !empty( $_SESSION['userId'] ) ? $_SESSION['userId'] : "";
            $projectId   = !empty( $_SESSION['projectId'] ) ? $_SESSION['projectId'] : "";
            $id          = "";

            $sql         = "CALL proc_newProjectModel(?,?,?,?)";
            $data        = array(
                "id"            => $id,
                "user_id"       => $userId,
                'project_id'    => $projectId,
                'model_id'      => $params['model_id']
            );
            $format     = array("iiii");

            $type       = "create";
            $database   = "quaratio";

            ( new service\Service( $type, $database ) )->dbAction( $sql, $data, $format );

        }

        private function getAllProjectsByUser()
        {

            $userId     = !empty( $_SESSION['userId'] ) ? $_SESSION['userId'] : "";
            $sql        = "CALL proc_getAllProjectsByUser(?)";
            $data       = array( "user_id" => $userId );
            $format     = array("i");

            $type       = "read";

            $returnData = ( new service\Service( $type, "quaratio" ) )->dbAction( $sql, $data, $format );

            return( $returnData );

        }

        private function getModelIdByProjectId( $params )
        {

            $sql        = "CALL proc_getModelIdByProjectId(?)";
            $data       = array("id" => $params['project_id']);
            $format     = array("i");

            $type       = "read";

            $returnData = ( new service\Service( $type, "quaratio" ) )->dbAction( $sql, $data, $format );

            $returnArray = array();

            foreach( $returnData as $returnDat ):

                $returnArray['model_id'] = $returnDat['model_id'];

            endforeach;

            return( $returnArray );

        }

    }

endif;