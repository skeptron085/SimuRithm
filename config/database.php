<?php

/*
|--------------------------------------------------------------------------
| SimuRithm Database Connection
|--------------------------------------------------------------------------
| Version : Release 1.1
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/config.php';

class Database
{

    /*
    |--------------------------------------------------------------------------
    | Database Credentials
    |--------------------------------------------------------------------------
    */

    private $host = "localhost";

    private $username = "root";

    private $password = "";

    /*
    |--------------------------------------------------------------------------
    | Database Name
    |--------------------------------------------------------------------------
    | Leave blank if not yet created.
    |--------------------------------------------------------------------------
    */

    private $database = "simurithm";

    /*
    |--------------------------------------------------------------------------
    | Connection
    |--------------------------------------------------------------------------
    */

    private $connection = null;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {

        mysqli_report(
            MYSQLI_REPORT_ERROR |
                MYSQLI_REPORT_STRICT
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Connect
    |--------------------------------------------------------------------------
    */

    public function connect()
    {

        if ($this->connection !== null) {

            return $this->connection;
        }

        try {

            $this->connection = new mysqli(

                $this->host,

                $this->username,

                $this->password,

                $this->database

            );

            $this->connection->set_charset("utf8mb4");

            return $this->connection;
        } catch (Exception $e) {

            error_log(

                $e->getMessage(),

                3,

                LOG_DIR . "database.log"

            );

            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Check Connection
    |--------------------------------------------------------------------------
    */

    public function isConnected()
    {

        return $this->connection !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | Close Connection
    |--------------------------------------------------------------------------
    */

    public function close()
    {

        if ($this->connection) {

            $this->connection->close();
        }
    }
}
