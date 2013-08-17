<?php
class Database {

    private $dbh        = null;
    private $encode     = DB_ENCODE;
    private $fetchModel = PDO::FETCH_ASSOC;

    public function __construct() {
        $connection   = DB_TYPE .':host=' .DB_HOST .';dbname=' .DB_NAME;
        $options      = [PDO::MYSQL_ATTR_INIT_COMMAND => 'set names ' .DB_ENCODE];
        $username     = SYS_USERNAME;
        $password     = SYS_PASSWORD;

        try {
            $this->dbh  = new PDO($connection, $username, $password, $options);
        } catch (PDOException $e) {
            $this->outputDebugException($e);
        }
    }

    private function initExec() {
        $sql      = 'set names '. $this->encode;
        $this->dbh->exec($sql);
    }

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

    public function getAll($sql) {
        $stmt = $this->getPDOStatment($sql);
        $rs = $stmt->fetchAll($this->fetchModel);

        if (!$rs) {
            return false;
        }

        return $rs;
    }

    public function getOne($sql) {
        $sql .= ' LIMIT 1';

        $stmt = $this->getPDOStatment($sql);
        $rs   = $stmt->fetch($this->fetchModel);

        if (!$rs) {
            return false;
        }

        return $rs;
    }

    public function insert($table, $arrayField) {
        return $this->insertUpdateParpare('INSERT', $table, $arrayField);
    }

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

    private function outputDebugException($e) {
        echo 'PDO exception that "' .$e->getMessage() .'"';
        return false;
    }
}
