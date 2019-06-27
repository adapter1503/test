<?php
require_once('MyLogger.php');
class PDOAdapter
{
  public $rowCount;
  public $errorInfo;
  private $dbh;
  private $errorLogger;
  private $sql;
  /**
  * @param string $dsn - информация для подключения
  * @param string $username - имя пользователя 
  * @param string $password - пароль
  * @param $errorLogger - объеут класса MyLogger
  * устанавливаем соединение с бд
  * при ошибках отлавливаем исключения и выводим их
  * устанавилваем кодировку utf-8 для текущего соединения
  */
  function __construct($dsn, $username, $password, $errorLogger)
  {
    try {
      $this->dbh = new PDO($dsn, $username, $password);
      $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->dbh->exec("SET NAMES 'utf8'");
      $this->dbh->exec("SET character_set_client = utf8");
    } catch (PDOException $e) {
      echo 'PDO Error: ' . $e->getMessage();
    }
    $this->errorLogger = $errorLogger;
  }
  /**
  * @return object PDO
  */
  public function getDbh()
  {
    return $this->dbh;
  }
  /**
  * @return получаем запрос, связанный с последней операцией над дескриптором бд
  */
  public function getLastInsertId()
  {
    return $this->dbh->lastInsertId();
  }
  /**
  * @param $method
  * @param string @sql - запрос
  * @param $args = null
  * принимает sql запрос и выполяет его
  * выполнят один из методов, выбранного с помощью переданного $method
  */
  public function execute($method, $sql, $args = null)
  {
    try {
      $this->errorInfo = null;
      switch ($method) {
        case 'selectAll':
          return $this->selectAll($sql, $args);
        case 'selectOne':
          return $this->selectOne($sql, $args);
        case 'execute':
          return $this->execute_($sql, $args);
      }

    } catch (PDOException $e) {
      $this->errorLogger->error("PDOAdapter $method() error: sql=\n$sql" .
        (isset($args) ? "\nargs=" . print_r($args, true) : '') . "\n" .
        $e->__toString());
      $this->errorInfo = $e->errorInfo;
      throw $e;
    }
  }
  /**
  * @param string $sql
  * @param $args = null
  * если передаются аргументы, то подготавливает и отправляет запрос
  * @return array содержащий все строки результирующего набора
  */
  private function selectAll($sql, $args = null)
  {
//$this->errorLogger->info('PDOAdapter -> selectAll() $sql='.$sql. ' $args='.print_r($args, true));
    $result = null;
    if (isset($args)) {
      $stmt = $this->dbh->prepare($sql);
      $result = $stmt->execute($args);
//      if ($result)
      $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
      $result = $this->dbh->query($sql, PDO::FETCH_OBJ)->fetchAll();
    }
//    if ($result === false)
//			$this->errorLogger->error(print_r($this->dbh->errorInfo(), true));
    return $result;
  }
  /**
  * @param string $sql запрос
  * @param $args = null
  * если передаются аргументы, то подготавливает и отправляет запрос
  * @return mixed строку и результирующего набора
  */
  private function selectOne($sql, $args = null)
  {
$this->errorLogger->info('PDOAdapter -> selectOne() $sql='.$sql. ' $args='.print_r($args, true));
    $result = null;
    if (isset($args)) {
      $stmt = $this->dbh->prepare($sql);
      $stmt->execute($args);
      $result = $stmt->fetch(PDO::FETCH_OBJ);
    } else {
      $result = $this->dbh->query($sql, PDO::FETCH_OBJ)->fetch();
    }
    return $result;
  }
  /**
   * @param $sql
   * @param args = null
   * @return boolean
   * подготавливает и выполняет запрос, если true - обращается к методу rowCount
   * если false - присваивает rowCount = 0 и записывает ошибку
   */
  private function execute_($sql, $args = null)
  {
    $stmt = $this->dbh->prepare($sql);
    $success = $stmt->execute($args);
    if ($success)
      $this->rowCount = $stmt->rowCount();
    else {
      $this->rowCount = 0;
      $this->errorInfo = $this->dbh->errorInfo;
    }
    return $success;
  }
  /**
   * @return mixed кол-во строк, затронутых последним запросом 
   */
  public function rowCount()
  {
    return $this->stmt->rowCount();
  }
  /**
   * @param $sql
   * @return подготовленный запрос
   */
  public function prepare($sql)
  {
    $this->sql = $sql;
    return $this->dbh->prepare($sql);
  }
  /**
   * @param $stmt
   * @param $args = null
   * @return null
   * выполняет запрос. если удачно - обращается к методу rowCount
   * eсли нет - присваивает $rowCount = 0 и записывает ошибку
   */
  public function executePrepared($stmt, $args = null)
  {
    try {
      $this->errorInfo = null;
      $success = $stmt->execute($args);
      if ($success)
        $this->rowCount = $stmt->rowCount();
      else {
        $this->rowCount = 0;
        $this->errorInfo = $this->dbh->errorInfo;
      }
      return $success;
    } catch (PDOException $e) {
      $this->errorLogger->error("PDOAdapter executePrepared() error: sql=\n".$this->sql .
        (isset($args) ? "\nargs=" . print_r($args, true) : '') . "\n" .
        $e->__toString());
      $this->errorInfo = $e->errorInfo;
      return null;
    }
  }
  /**
   * @param $stmt
   * @param $args = null
   * @return mixed
   * выполняет запрос
   * возвращает массив, содержащий все строки результирующего набора в виде объекта
   */
  public function selectPrepared($stmt, $args = null)
  {
    $stmt->execute($args);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }
  /**
   * @param $tableName
   * @param $dbName = null
   * @return array
   * обращается к бд
   * выполняет запрос методом selectAll и записывает результат в $rows
   * разбивает массив
   * создает новый объект класса stdclass
   * указывает значение по умолчанию
   * указывает, что поле может быть NULL
   * возвращает массив с указанием типа данных для каждого поля
   */
  public function getColumnMetadata($tableName, $dbName = null)
  {
    if (!$dbName) {
      $dbName = $this->selectColumn('select database()');
    }
    //$this->info("getColumnMetadata($tableName, $dbName)");
    $sql = "select column_name, column_default, is_nullable, data_type
							from information_schema.columns
							where table_schema='$dbName' and table_name='$tableName'";
    $rows = $this->selectAll($sql);
    //$this->info("getColumnMetadata(): rows=".print_r($rows, true));
    $colmeta = array();
    foreach ($rows as $row) {
      $row = (array)$row;
      $cm = new stdclass();
      $cm->default = $row['column_default'];
      $cm->nullable = $row['is_nullable'] == 'YES';
      switch ($row['data_type']) {
        case 'varchar':
        case 'longtext':
        case 'char':
        case 'mediumtext':
        case 'text':
          $type = 'string';
          break;
        case 'datetime':
        case 'timestamp':
        case 'time':
        case 'date':
          $type = 'datetime';
          break;
        case 'bigint':
        case 'int':
        case 'tinyint':
        case 'smallint':
        case 'mediumint':
          $type = 'integer';
          break;
        case 'decimal':
          $type = 'double';
          break;
        case 'bigint':
        case 'int':
        case 'tinyint':
        case 'smallint':
        case 'mediumint':
          $type = 'integer';
          break;
        default:
          $type = 'unknown type';
      }
      $cm->type = $type;
      $colmeta[$row['column_name']] = $cm;
    }
    return $colmeta;
  }
  /**
  * @param $sql
  * @param $args = null
  * @return mixed
  * присваивает $result = null
  * если есть аргументы записываем в номер колонки значение 0ого аргумента,
  * если нет, то приваиваем $column_number 0 и возвращаем данные этого столбца
  */
  private function selectColumn($sql, $args = null)
  {
    $result = null;
    if (isset($args))
      $column_number = $args[0];
    else
      $column_number = 0;
    $result = $this->dbh->query($sql)->fetchColumn($column_number);
    return $result;
  }
  /**
  * @param $values
  * @return array
  * перезаписывает значения $values из null в ''
  */
  public function nullToEmptyStrings($values)
  {
    foreach ($values as $key => $value) {
      if (!isset($value)) {
        $values[$key] = '';
      }
    }
    return $values;
  }
  /**
  * @param $values
  * @param $fieldNameOrder
  * @param $colmeta
  * @return mixed
  * $cm присваивается объект в котором хранятся параметры поля
  * без примера получаемых данных тяжело определить что находится в $value
  * если $values существует придет проверка типа,
  * если тип равен string или datetime обрезаем values по краям строки
  * либо если тип строка ничего не происходит, а так же если тип int или double
  * $value = 0
  * иначе, если тип datetime записываем null
  * иначе записываем дефолтное значение
  */
  public function quot_insert_values($values, $fieldNameOrder, $colmeta)
  {
    foreach ($fieldNameOrder as $name => $order) {
      $cm = $colmeta[$name];
      $value = $values[$order];
      if ($value) {
        if ($cm->type == 'string' || $cm->type == 'datetime')
          $value = trim($value); //  $value = $conn->quote($value);
      } else {
        if ($cm->type == 'string')
          ; // $value = $conn->quote($value);
        else if ($cm->type == 'integer' || $cm->type == 'double') {
          if (!($cm->default))
            $value = 0;
        } else if ($cm->type == 'datetime')
          $value = null;
        else
          $value = $cm->default;

      }
      $values[$order] = $value;
    }
    return $values;
  }

}
?>
