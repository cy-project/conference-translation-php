<?php

/**
 * Database 
 * 
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author Tobias Schlitt <toby@php.net> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class Database {

    private $dbh        = null;
    private $encoding     = DB_ENCODING;
    private $fetchModel = PDO::FETCH_ASSOC;

    /**
     * __construct 
     * 
     * @access public
     * @return void
     */
    public function __construct() {
        $connection   = DB_TYPE .':host=' .DB_HOST .';dbname=' .DB_NAME;
        $options      = [PDO::MYSQL_ATTR_INIT_COMMAND => 'set names ' .DB_ENCODING];
        $username     = DB_USERNAME;
        $password     = DB_PASSWORD;

        try {
            $this->dbh  = new PDO($connection, $username, $password, $options);
        } catch (PDOException $e) {
            $this->outputDebugException($e);
        }
    }

    /**
     * initExec 
     * 
     * @access private
     * @return void
     */
    private function initExec() {
        $sql      = 'set names '. $this->encoding;
        $this->dbh->exec($sql);
    }

    /**
     * getPDOStatment 
     * 
     * @param mixed $sql 
     * @access private
     * @return stmt
     */
    private function getPDOStatment($sql) {
        $stmt = null;

        $this->initExec();

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            outputDebugException($e);
        }

        return $stmt;
    }

    /**
     * getAll 
     * 
     * @param mixed $sql 
     * @access public
     * @return rs
     */
    public function getAll($sql) {
        $stmt = $this->getPDOStatment($sql);
        $rs = $stmt->fetchAll($this->fetchModel);

        if (!$rs) {
            return false;
        }

        return $rs;
    }

    /**
     * getOne 
     * 
     * @param mixed $sql 
     * @access public
     * @return rs
     */
    public function getOne($sql) {
        $sql .= ' LIMIT 1';

        $stmt = $this->getPDOStatment($sql);
        $rs   = $stmt->fetch($this->fetchModel);

        if (!$rs) {
            return false;
        }

        return $rs;
    }

    /**
     * insert 
     * 
     * @param mixed $table 
     * @param mixed $arrayField 
     * @access public
     * @return result
     */
    public function insert($table, $arrayField) {
        return $this->insertUpdateParpare('INSERT', $table, $arrayField);
    }

    /**
     * insertUpdateParpare 
     * 
     * @param mixed $queryType 
     * @param mixed $table 
     * @param mixed $arrayField 
     * @param string $whereClause 
     * @access private
     * @return result
     */
    private function insertUpdateParpare( $queryType, $table, $arrayField, $whereClause='' ) {
        if (empty($arrayField) || empty($table)) {
            return false;
        }

        $sql      = null;
        $stmt     = null;

        $arrayColumns  = array_keys($arrayField);
        $arrayValues   = [];


        if ($queryType == 'INSERT') {
            $columns = $arrayColumns[0];
            $values  = "?";
            for ($i = 0; $i < count($arrayColumns); $i++) {
                $arrayValues[$i] = $arrayField[$arrayColumns[$i]];

                if ($i != 0) {
                    $columns .= "," .$arrayColumns[$i];
                    $values  .= ', ?';
                }
            }

            $sql  = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
            $stmt = $this->dbh->prepare($sql);
        }
        else if ($queryType == 'UPDATE') {
        }

        try {
            $this->initExec();
            $result = $stmt->execute($arrayValues);
        } catch (PDOException $e) {
            $this->outputDebugException($e);
        }

        return $result;
    }

    /**
     * outputDebugException 
     * 
     * @param mixed $e 
     * @access private
     * @return boolean
     */
    private function outputDebugException($e) {
        echo 'PDO exception that "' .$e->getMessage() .'"';
        return false;
    }
}
