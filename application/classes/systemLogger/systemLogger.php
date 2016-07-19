<?php

/*
 * System logger:
 *
 * - Logs system(litening) actions only.
 *
 * Currently logging: Register, login
 *
 */

namespace application\classes\systemLogger;

use \application\model\service;

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 20-Jun-16
 * Time: 21:22
 */

if(!class_exists('SystemLogger')):

    class SystemLogger
    {

        protected $group;
        protected $action;
        protected $message;

        /**
         * SystemLogger constructor.
         * @param $group
         * @param $action
         * @param $message
         */

        public function __construct( $group, $action, $message )
        {
            $this->group = $group;
            $this->action = $action;
            $this->message = $message;
        }

        public function createLog()
        {

            $emptyspace = " ";
            $date       = date("Y-m-d");
            $time       = date("H:i:s");
            $userId     = ( !empty( $_SESSION['userId'] ) ? $_SESSION['userId'] : (!empty( $_SESSION['registerId'] ) ? $_SESSION['registerId'] : "") );

            $sql        = "CALL proc_newSystemLog(?,?,?,?,?,?,?)";
            $data       = array("id"  => $emptyspace,
                          "user_id"   => $userId,
                          "group"     => $this->group,
                          "action"    => $this->action,
                          "message"   => $this->message,
                          "date"      => $date,
                          "time"      => $time);
            $format     = array("iiiiiss");

            $type       = "create";

            ( new service\Service( $type, "litening" ) )->dbAction( $sql, $data, $format );
            
        }

        public function countSystemLogs()
        {
            $sql        = "CALL proc_countSystemLogs()";
            $data       = array();
            $format     = array();

            $type       = "read";

            $returnData = ( new service\Service( $type, "litening" ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):
                return(  $returnData );
            else:
                return( false );
            endif;
        }

        /**
         * @param $limit
         * @param $pageNumber
         * @param $params
         * @return bool|\mysqli_result
         */

        public function filterSystemLogs( $limit, $pageNumber, $params, $sysLogFilter_sort, $sysLogFilter_sortType )
        {

            if( !empty( $params["logUserId"] ) ) $userId = $params["logUserId"];
            if( !empty( $params["logGroup"] ) ) $logGroup = $params["logGroup"];
            if( !empty( $params["logAction"] ) ) $logAction = $params["logAction"];
            if( !empty( $params["logMessage"] ) ) $logMessage = $params["logMessage"];
            if( !empty( $params["logStartDate"] ) ) $logStartDate = $params["logStartDate"];
            if( !empty( $params["logEndDate"] ) ) $logEndDate = $params["logEndDate"];
            if( !empty( $params["logStartTime"] ) ) $logStartTime = $params["logStartTime"];
            if( !empty( $params["logEndTime"] ) ) $logEndTime = $params["logEndTime"];

            $totalParams = count( $params );

            $sql = "SELECT id, user_id, log_group, log_action, log_message, log_date, log_time FROM logs_system";

            if( $totalParams > 0 ):
                $sql .= " WHERE 1 = 1";
            endif;

            $format = array();
            $data = array();

            if( !empty( $userId ) ): $sql .= " AND user_id = ?"; $format[] = 'i'; $data[] = $userId; endif;
            if( !empty( $logGroup ) ): $sql .= " AND log_group = ?"; $format[] = 'i'; $data[] = $logGroup; endif;
            if( !empty( $logAction ) ): $sql .= " AND log_action = ?"; $format[] = 'i'; $data[] = $logAction; endif;
            if( !empty( $logMessage ) ): $sql .= " AND log_message = ?"; $format[] = 'i'; $data[] = $logMessage; endif;

            if( !empty( $logStartDate ) && !empty( $logEndDate ) ):

                $sql        .= " AND log_date >= ? AND log_date <= ?";
                $format[]    = "s";
                $format[]    = "s";
                $data[]      = $logStartDate;
                $data[]      = $logEndDate;

            else:

                if( !empty( $logStartDate ) ): $sql .= " AND log_date >= ?"; $format[] = 's'; $data[] = $logStartDate; endif;
                if( !empty( $logEndDate ) ): $sql .= " AND log_date <= ?"; $format[] = 's'; $data[] = $logEndDate; endif;

            endif;

            if( !empty( $logStartTime ) && !empty( $logEndTime ) ):

                $sql        .= " AND log_time >= ? AND log_time <= ?";
                $format[]    = "s";
                $format[]    = "s";
                $data[]      = $logStartTime;
                $data[]      = $logEndTime;

            else:

                if( !empty( $logStartTime ) ): $sql .= " AND log_time >= ?"; $format[] = 's'; $data[] = $logStartTime; endif;
                if( !empty( $logEndTime ) ): $sql .= " AND log_time <= ?"; $format[] = 's'; $data[] = $logEndTime; endif;

            endif;

            if( !empty( $sysLogFilter_sort) && !empty( $sysLogFilter_sortType ) ):

                switch( $sysLogFilter_sort ):

                    case"user_id":

                        switch($sysLogFilter_sortType):
                            case"asc":
                                $sql .= " ORDER BY user_id ASC";
                                break;
                            case"desc":
                                $sql .= " ORDER BY user_id DESC";
                                break;
                        endswitch;

                    break;
                    case"group":

                        switch($sysLogFilter_sortType):
                            case"asc":
                                $sql .= " ORDER BY log_group ASC";
                                break;
                            case"desc":
                                $sql .= " ORDER BY log_group DESC";
                                break;
                        endswitch;

                    break;
                    case"action":

                        switch($sysLogFilter_sortType):
                            case"asc":
                                $sql .= " ORDER BY log_action ASC";
                                break;
                            case"desc":
                                $sql .= " ORDER BY log_action DESC";
                                break;
                        endswitch;

                    break;
                    case"message":

                        switch($sysLogFilter_sortType):
                            case"asc":
                                $sql .= " ORDER BY log_message ASC";
                                break;
                            case"desc":
                                $sql .= " ORDER BY log_message DESC";
                                break;
                        endswitch;

                    break;
                    case"date":

                        switch($sysLogFilter_sortType):
                            case"asc":
                                $sql .= " ORDER BY log_date ASC";
                                break;
                            case"desc":
                                $sql .= " ORDER BY log_date DESC";
                                break;
                        endswitch;

                    break;

                    case"time":

                        switch($sysLogFilter_sortType):
                            case"asc":
                                $sql .= " ORDER BY log_time ASC";
                                break;
                            case"desc":
                                $sql .= " ORDER BY log_time DESC";
                                break;
                        endswitch;

                    break;

                endswitch;

            else:

                $sql .= " ORDER BY log_date DESC, log_time DESC";

            endif;

            if( !empty( $limit ) && !empty( $pageNumber ) ):
                $sql .= " LIMIT ".(($pageNumber -1) * $limit).", ".$limit."";
            endif;

            $type = "read";

            if( empty( $data ) ) $format = array();

            $returnData = ( new service\Service( $type, "litening" ) )->dbAction( $sql, $data, $format );

            return( $returnData );

        }

        /**
         * @return bool|\mysqli_result
         */

        public function getSystemLogsUserIds()
        {

            $sql        = "CALL proc_getSystemLogsUserIds()";
            $data       = array();
            $format     = array();

            $type       = "read";

            $returnData = ( new service\Service( $type, "litening" ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):
                return(  $returnData );
            else:
                return( false );
            endif;
        }

        /**
         * @return bool|\mysqli_result
         */

        public function getSystemLogsGroups()
        {

            $sql        = "CALL proc_getSystemLogsGroups()";
            $data       = array();
            $format     = array();

            $type       = "read";

            $returnData = ( new service\Service( $type, "litening" ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):
                return(  $returnData );
            else:
                return( false );
            endif;
        }

        /**
         * @return bool|\mysqli_result
         */

        public function getSystemLogsActions()
        {

            $sql        = "CALL proc_getSystemLogsActions()";
            $data       = array();
            $format     = array();

            $type       = "read";

            $returnData = ( new service\Service( $type, "litening" ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):
                return(  $returnData );
            else:
                return( false );
            endif;
        }

        /**
         * @return bool|\mysqli_result
         */

        public function getSystemLogsMessages()
        {

            $sql        = "CALL proc_getSystemLogsMessages()";
            $data       = array();
            $format     = array();

            $type       = "read";

            $returnData = ( new service\Service( $type, "litening" ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):
                return(  $returnData );
            else:
                return( false );
            endif;
        }

        /**
         * @return bool|\mysqli_result
         */

        public function getSystemLogsDates()
        {

            $sql        = "CALL proc_getSystemLogsDates()";
            $data       = array();
            $format     = array();

            $type       = "read";

            $returnData = ( new service\Service( $type, "litening" ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):
                return(  $returnData );
            else:
                return( false );
            endif;
        }

        /**
         * @return bool|\mysqli_result
         */

        public function getSystemLogsTimes()
        {

            $sql        = "CALL proc_getSystemLogsTimes()";
            $data       = array();
            $format     = array();

            $type       = "read";

            $returnData = ( new service\Service( $type, "litening" ) )->dbAction( $sql, $data, $format );

            if( !empty( $returnData ) ):
                return(  $returnData );
            else:
                return( false );
            endif;
        }

        /**
         * @return array|bool
         */

        public function convertSystemLogs()
        {

            // Let's switch again, like we did last summer

            $converted = array();

            switch( $this->group ):

                case"1":
                    $converted['group'] = "Register";
                    break;
                case"2":
                    $converted['group'] = "Login";
                    break;

            endswitch;

            switch( $this->action ):

                case"1":
                    switch( $this->group ):
                        case"1":
                            $converted['action'] = "Registered";
                            break;
                        case"2":
                            $converted['action'] = "Logged in";
                            break;
                    endswitch;
                    break;
                case"2":

                    break;

            endswitch;

            switch( $this->message ):

                case"1":
                    switch( $this->group ):

                        case"1":
                            switch( $this->action ):

                                case"1":
                                    $converted['message'] = "The user has registered succesfully";
                                    break;
                                case"2":

                                    break;

                            endswitch;

                            break;
                        case"2":
                            switch( $this->action ):

                                case"1":
                                    $converted['message'] = "The user has logged in";
                                    break;
                                case"2":

                                    break;

                            endswitch;
                            break;
                    endswitch;
                    break;
                case"2":

                    break;

            endswitch;

            if( !empty( $converted ) ):
                return($converted);
            else:
                return(false);
            endif;
        }

    }

endif;