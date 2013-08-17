<?php

/**
 * Database 資料庫
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
     * @param string $sql 
     * @access private
     * @return object
     */
    private function getPDOStatment($sql) {
        $stmt = null;

        $this->initExec();

        try {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException($e);
        }

        return $stmt;
    }

    /**
     * getAll 
     * 
     * @param string $sql 
     * @access public
     * @return array
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
     * @param string $sql 
     * @access public
     * @return array
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
     * @param string $table 
     * @param array  $arrayField 
     * @access public
     * @return boolean
     */
    public function insert($table, $arrayField) {
        return $this->insertUpdateParpare('INSERT', $table, $arrayField);
    }

    /**
     * insertUpdateParpare 
     * 
     * @param string $queryType 
     * @param string $table 
     * @param array  $arrayField 
     * @param string $whereClause 
     * @access private
     * @return boolean
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
     * @param string $e 
     * @access private
     * @return boolean
     */
    private function outputDebugException($e) {
        echo 'PDO exception that "' .$e->getMessage() .'"';
        return false;
    }
}
