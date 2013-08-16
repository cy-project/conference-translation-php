<?php
class DB {
  
  private $dbh        = null;
  private $encode     = SYS_DBENCODE;
  private $fetchModel = PDO::FETCH_ASSOC;

  public function __construct() {
    $dbConnection   = SYS_DBTYPE .':host=' .SYS_DBHOST .';dbname=' .SYS_DBNAME;
    $dbOptions      = [PDO::MYSQL_ATTR_INIT_COMMAND => 'set names ' .SYS_DBENCODE];
    $dbUsername     = SYS_DBUSER;
    $dbPassword     = SYS_DBPASSWORD;

    try {
      $this->dbh  = new PDO($dbConnection, $dbUsername, $dbPassword, $dbOptions);
    } catch (PDOException $e) {
      $this->debugOutputException($e);
    }
  }

  private function initExec() {
    $sql      = 'set names '. $this->encode;
    $this->dbh->exec($sql);
  }

  private function _pdoStatment( $sql ) {
    $stmt = null;

    $this->initExec();
    
    try {
      $stmt = $this->dbh->prepare($sql);
      $stmt->execute();
    } catch (PDOException $e) {
      debugOutputException($e);
    }

    return $stmt;
  }

  public function dbGetAll( $sql ) {
    $stmt = $this->_pdoStatment($sql);
    $rs = $stmt->fetchAll($this->fetchModel);

    if ( !$rs ) {
      return false;
    }

    return $rs;
  }

  public function dbGetOne( $sql ) {
    $sql .= ' LIMIT 1';

    $stmt = $this->_pdoStatment($sql);
    $rs   = $stmt->fetch($this->fetchModel);

    if ( !$rs ) {
      return false;
    }

    return $rs;
  }

  public function dbInsert( $table, $arrayField) {
    return $this->insertUpdateParpare('INSERT', $table, $arrayField);
  }

  private function insertUpdateParpare( $queryType, $table, $arrayField, $whereClause='' ) {
    if ( empty($arrayField) || empty($table) ) {
      return false;
    }

    $sql      = null;
    $stmt     = null;

    $arrayColumns  = array_keys($arrayField);
    $arrayValues   = [];


    if ( $queryType == 'INSERT' ) {
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
      $stmt = $this->dbh->prepare( $sql );
    }
    else if ( $queryType == 'UPDATE' ) {
    }

    try {
      $this->initExec();
      $result = $stmt->execute($arrayValues);
    } catch (PDOException $e) {
      $this->debugOutputException($e);
    }

    return $result;
  }

  private function debugOutputException( $e ) {
    echo 'PDO exception that "' .$e->getMessage() .'"';
    return false;
  }
}
