<?php

/*
==========================================================
 SimuRithm
 System Logger
 Release 1.3
==========================================================
*/

if (!defined('SIMURITHM_LOG_DIR')) {

    define(
        'SIMURITHM_LOG_DIR',
        dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs'
    );
}


/*
----------------------------------------------------------
 Ensure Log Directory Exists
----------------------------------------------------------
*/

if (!is_dir(SIMURITHM_LOG_DIR)) {

    @mkdir(
        SIMURITHM_LOG_DIR,
        0777,
        true
    );
}


/*
----------------------------------------------------------
 Main Logger
----------------------------------------------------------
*/

function writeLog(
    string $level,
    string $message,
    array $context = []
): bool {

    $logFile =
        SIMURITHM_LOG_DIR .
        DIRECTORY_SEPARATOR .
        'system.log';


    /*
    ------------------------------------------------------
     Timestamp
    ------------------------------------------------------
    */

    $timestamp = date(
        'Y-m-d H:i:s'
    );


    /*
    ------------------------------------------------------
     Client IP
    ------------------------------------------------------
    */

    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';


    /*
    ------------------------------------------------------
     User Agent
    ------------------------------------------------------
    */

    $userAgent =
        $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';


    /*
    ------------------------------------------------------
     Context
    ------------------------------------------------------
    */

    $contextText = '';

    if (!empty($context)) {

        $encodedContext = json_encode(
            $context,
            JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
        );

        if ($encodedContext !== false) {

            $contextText =
                ' | Context: ' .
                $encodedContext;
        }
    }


    /*
    ------------------------------------------------------
     Log Entry
    ------------------------------------------------------
    */

    $entry =
        '[' . $timestamp . ']' .
        ' [' . strtoupper($level) . ']' .
        ' [IP: ' . $ip . ']' .
        ' ' . $message .
        $contextText .
        PHP_EOL;


    /*
    ------------------------------------------------------
     Write
    ------------------------------------------------------
    */

    return @file_put_contents(
        $logFile,
        $entry,
        FILE_APPEND | LOCK_EX
    ) !== false;
}


/*
----------------------------------------------------------
 Convenience Functions
----------------------------------------------------------
*/

function logInfo(
    string $message,
    array $context = []
): bool {

    return writeLog(
        'INFO',
        $message,
        $context
    );
}


function logSuccess(
    string $message,
    array $context = []
): bool {

    return writeLog(
        'SUCCESS',
        $message,
        $context
    );
}


function logWarning(
    string $message,
    array $context = []
): bool {

    return writeLog(
        'WARNING',
        $message,
        $context
    );
}


function logError(
    string $message,
    array $context = []
): bool {

    return writeLog(
        'ERROR',
        $message,
        $context
    );
}
