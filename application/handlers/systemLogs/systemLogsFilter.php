<?php

namespace application\handlers\systemLogs\systemLogsFilter;

use \application\controller;
use \application\classes\validator;

/**
 * Created by PhpStorm.
 * User: Ruben Hazenbosch
 * Date: 22-Jun-16
 * Time: 21:28
 */

if( $_SERVER['REQUEST_METHOD'] === 'POST' ):

    // Set variables for the filter

    $userId                 = ( !empty( $_POST['userIds'] ) ? $_POST['userIds'] : "" );
    $logGroup               = ( !empty( $_POST['groups'] ) ? $_POST['groups'] : "" );
    $logAction              = ( !empty( $_POST['actions'] ) ? $_POST['actions'] : "" );
    $logMessage             = ( !empty( $_POST['messages'] ) ? $_POST['messages'] : "" );
    $logStartDate           = ( !empty( $_POST['startDates'] ) ? $_POST['startDates'] : "" );
    $logEndDate             = ( !empty( $_POST['endDates'] ) ? $_POST['endDates'] : "" );
    $logStartTime           = ( !empty( $_POST['startTimes'] ) ? $_POST['startTimes'] : "" );
    $logEndTime             = ( !empty( $_POST['endTimes'] ) ? $_POST['endTimes'] : "" );
    $sysLogFilter_sort      = ( !empty( $_POST['sysLogFilter_sort'] ) ? $_POST['sysLogFilter_sort'] : "" );
    $sysLogFilter_sortType  = ( !empty( $_POST['sysLogFilter_sort'] ) ? $_POST['sysLogFilter_sort'] : "" );

    // Validate all variables

    $userId                 = ( new validator\Validator( $userId ) )->validateIsDigit();
    $logGroup               = ( new validator\Validator( $logGroup ) )->validateIsDigit();
    $logAction              = ( new validator\Validator( $logAction ) )->validateIsDigit();
    $logMessage             = ( new validator\Validator( $logMessage ) )->validateIsDigit();
    $logStartDate           = ( new validator\Validator( $logStartDate ) )->validateIsRealDate();
    $logEndDate             = ( new validator\Validator( $logEndDate ) )->validateIsRealDate();
    $logStartTime           = ( new validator\Validator( $logStartTime ) )->validateIsRealTime();
    $logEndTime             = ( new validator\Validator( $logEndTime ) )->validateIsRealTime();

    $params = array();

    if($userId !== false) $params["logUserId"] = $userId;
    if($logGroup !== false) $params["logGroup"] = $logGroup;
    if($logAction !== false) $params["logAction"] = $logAction;
    if($logMessage !== false) $params["logMessage"] = $logMessage;
    if($logStartDate !== false) $params["logStartDate"] = $logStartDate;
    if($logEndDate !== false) $params["logEndDate"] = $logEndDate;
    if($logStartTime !== false) $params["logStartTime"] = $logStartTime;
    if($logEndTime !== false) $params["logEndTime"] = $logEndTime;

    // Create data url

    $urlData                            = array();
    $params                             = serialize($params);

    $urlData['sysLogFilterParams']      = $params;
    $urlData['sysLogFilter']            = true;
    $urlData['sysLogFilter_sort']       = $sysLogFilter_sort;
    $urlData['sysLogFilter_sortType']   = $sysLogFilter_sortType;

    $urlData                            = base64_encode( json_encode( $urlData ) );

    header("Location: ".LITENING_SELF."?data=".$urlData."");
    exit();

endif;