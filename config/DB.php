<?php 

/**	
	version 1.5.5
	- Author: quan0265
	- DB pdo full:
		- version 1.2: select, table, join, group by, having, order by, limit
		- version 1.3: whereIn, whereNotIn
		- version 1.4: insertRaw, updateRaw, upsert
		- version 1.5.1: whereNull, whereNotNull
		- version 1.5.2: fetchCount, orderByRaw
		- version 1.5.3 paginate
*/

// config
define('db_host', 'localhost');
define('db_port', '3306');
define('db_user', 'root');
define('db_pass', '');
define('db_name', 'db_test');
define('charset', 'utf8mb4');

class DB {
	private static $host = db_host;
	private static $port = db_port;
	private static $user = db_user;
	private static $pass = db_pass;
	private static $dbname = db_name;
	private static $charset = charset;
	protected static $conn = NULL;

	protected static $sql  = NULL;
	protected static $table = NULL;
	protected static $select = "*";
	protected static $join = NULL;
	protected static $where = NULL;
	protected static $group_by = NULL;
	protected static $having = NULL;
	protected static $order = NULL;
	protected static $limit = NULL;
	protected static $offset = NULL;

	protected static $data_execute = [];
	protected static $comparison_operator_array = ['=', '>', '<', '>=', '<=', '<>', '!=', 'like', 'LIKE', 'not like', 'NOT LIKE'];

	protected static $result = NULL;
	protected static $last_sql = NULL;
	protected static $last_data_execute = [];
	protected static $last_result = NULL;

	function __construct() {
	 	// static::$connect();	
	}

	public static function connect($config=[]) {
		if ($config) {
			static::$host = $config['db_host'];
			static::$port = $config['db_port'];
			static::$user = $config['db_user'];
			static::$pass = $config['db_pass'];
			static::$dbname = $config['db_name'];
			static::$charset = $config['charset'];
		}
		if (!static::$conn) {
			$dsn= "mysql:host=" . static::$host . ";port=" . static::$port . ";dbname=" . static::$dbname . ";charset=" . static::$charset;
			$options= [
				PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	    		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
	    		PDO::ATTR_EMULATE_PREPARES   => false,
			];
			try {
				// echo 'connect';
				static::$conn= new PDO($dsn, static::$user,static::$pass,$options);
				// static::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				// static::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
			} catch (Exception $e) {
				echo "connect failed" . $e->getMessage();
			}
		}
	}

	public static function execute() {
		$stmt= static::$conn->prepare(static::$sql);
		// try {
			$stmt->execute(static::$data_execute);
			static::$result = true;
			static::flushData();
			return true;
		// } catch (Exception $e) {
		// 	trigger_error($e->getMessage(), E_USER_WARNING);
		// 	static::$result = false;
		// 	static::flushData();
		// 	return false;
		// }
	}

	public static function flushData() {
		static::$last_sql = static::$sql;
		static::$last_data_execute = static::$data_execute;
		static::$last_result = static::$result;

		static::$data_execute = [];
		static::$sql = NULL;
		static::$table = NULL;
		static::$select = "*";
		static::$join = NULL;
		static::$where = NULL;
		static::$group_by = NULL;
		static::$having = NULL;
		static::$order = NULL;
		static::$limit = NULL;
		static::$offset = NULL;
	}

	public static function getSql() {
		return static::$last_sql;
	}

	public static function debug() {
		echo '<br>';
		echo 'SQL: ' . '<br>' . static::$last_sql . '<br>' . '<br>';
		echo 'Data execute: <br>';
		echo '<pre>';
		var_dump(static::$last_data_execute);
		echo '</pre>';

		echo 'Result: ';
		echo '<pre>';
		var_dump(static::$last_result);
		echo '</pre>';
	}

	public static function error($text="") {
		if ($text) {
			trigger_error($text, E_USER_ERROR);
		}
		$error = mysqli_error(static::$conn);
		if ($error) {
			// trigger_error(static::$sql."<br>", E_USER_ERROR);
			trigger_error($error, E_USER_ERROR);
		}
	}

	public static function isValidMysqlColumnName(string $name): bool {
		// Chỉ chấp nhận a-z, A-Z, 0-9, _ 
		// Không cho phép bắt đầu bằng số
		return preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) === 1;
	}

	public static function checkColumnNameIsValid($name) {
		if (static::isValidMysqlColumnName($name)) {
			return true;
		}
		static::error("Error syntax method checkColumnNameIsValid argument name must is valid mysql column name: $name");
		return false;
	}

	public static function table($table) {
		// static::flushData();
		// if (preg_match('/^tbl_/', $table)) {
		// 	static::$table = $table;
		// }
		// else {
		// 	static::$table = 'tbl_' . $table;
		// }
		static::$table = $table;
		return new static();
	}

	public static function insert($data= []){
		static::connect();
		$field = "";
		$value = "";
		foreach($data as $k=> $v){
			$field.= ", `$k`";
			$value.= ", :$k";
		}
		$field = ltrim($field, ", ");
		$field = "($field)";
		$value = ltrim($value, ", ");
		$value = "($value)";
		static::$sql = "INSERT INTO " . static::$table . " $field VALUES $value";
		static::$data_execute = $data;
		return static::execute();
	}

	public static function lastInsertId() {
        return static::$conn->lastInsertId();
    }

    public static function update($data= []){
    	static::connect();
    	if (static::$where == '') {
    		exit('Error syntax method update, please add method where before update');
    	}
		$var = "";
		$data_execute = [];
		foreach($data as $key=> $value){
			$var.= ", $key=?";
			$data_execute[] = $value;
		}
		$var= ltrim($var, ", ");
		foreach (static::$data_execute as $k => $v) {
			$data_execute[] = $v;
		}
		static::$sql = "UPDATE " . static::$table . " SET $var " . static::$where;
		static::$data_execute = $data_execute;
		return static::execute();
	}

	public static function upsert($data=[], $conds=[], $update_cols=[]) {
		// conds = [a, b], update_cols = [a, b]
		if (empty($data)) {
    		exit('Error syntax method upsert, please add data');
    	}
		if (empty($conds)) {
    		exit('Error syntax method upsert, please add conditions');
    	}
		if (empty(static::$table)) {
    		exit('Empty table');
    	}
		$table = static::$table;
		foreach ($data as $k => $c) {
			if (!preg_match('/[0-9a-zA-Z_]+/', $k)) {
				exit('Error syntax method upsert, key data error syntax [0-9a-zA-Z_]: ' . $k);
			}
		}
		$item_has = static::table(static::$table);
		foreach ($conds as $c) {
			if (!preg_match('/[0-9a-zA-Z_]+/', $c)) {
				exit('Error syntax method upsert, name condition error syntax [0-9a-zA-Z_]: ' . $c);
			}
			$item_has = $item_has->where($c, $data[$c]);
			// $item_has = $item_has->where('name', '2');
		}
		$item_has = $item_has->first();
		static::$table = $table;
		if ($item_has) {
			// update
			$data_update = [];
			if (empty($update_cols)) {
				$data_update = $data;
			}
			else {
				foreach ($data as $k => $v) {
					if (in_array($k, $update_cols)) {
						$data_update[$k] = $v;
					}
				}
			}
			$item_where = static::table(static::$table);
			foreach ($conds as $c) {
				$item_where = $item_where->where($c, $data[$c]);
			}
			return $item_where->update($data_update);
		}
		else {
			// insert
			static::table(static::$table)->insert($data);
			return DB::lastInsertId();
		}
	}

	public static function increment($col, $count=1){
		checkColumnNameIsValid($col);

    	static::connect();
    	if (static::$where == '') {
    		exit('Error syntax method increment, please add method where before increment');
    	}
    	if (!preg_match("/^[a-z_]+$/i", $col)) {
    		exit('Error syntax method increment, var $col only allow contains character a-z_');
    	}
    	$count = (int)$count;
		static::$sql = "UPDATE " . static::$table . " SET $col=$col + $count " . static::$where;
		return static::execute();
	}

	public static function delete(){
		static::connect();
		if (static::$where == '') {
    		exit('Error syntax method delete, please add method where before delete');
    	}
		static::$sql= "DELETE FROM " . static::$table . ' ' . static::$where;
		return static::execute();
	}

	public static function raw($string) {
		return $string;
	}

	public static function select(...$selects) {
		foreach ($selects as $item) {
			if (static::$select == '*') {
				static::$select = $item;
			}
			else {
				static::$select .= ", $item";
			}
		}
		return new static();
	}

	public static function join($table, $col1, $operator, $col2) {
		checkColumnNameIsValid($col1);
		checkColumnNameIsValid($col2);

		$operator = trim($operator);
		if (in_array($operator, static::$comparison_operator_array)) {
			static::$join .= "JOIN $table ON $col1 $operator $col2";
		}
		else {
			$text = 'Error syntax method join use comparison operator: '.implode(', ', static::$comparison_operator_array);
			trigger_error($text, E_USER_WARNING);
			return false;
		}
		return new static();
	}

	public static function leftJoin($table, $col1, $operator, $col2) {
		checkColumnNameIsValid($col1);
		checkColumnNameIsValid($col2);

		$operator = trim($operator);
		if (in_array($operator, static::$comparison_operator_array)) {
			static::$join .= "LEFT JOIN $table ON $col1 $operator $col2";
		}
		else {
			$text = 'Error syntax method leftJoin use comparison operator: '.implode(', ', static::$comparison_operator_array);
			trigger_error($text, E_USER_WARNING);
			return false;
		}
		return new static();
	}

	public static function rightJoin($table, $col1, $operator, $col2) {
		checkColumnNameIsValid($col1);
		checkColumnNameIsValid($col2);

		$operator = trim($operator);
		if (in_array($operator, static::$comparison_operator_array)) {
			static::$join .= "RIGHT JOIN $table ON $col1 $operator $col2";
		}
		else {
			$text = 'Error syntax method rightJoin use comparison operator: '.implode(', ', static::$comparison_operator_array);
			trigger_error($text, E_USER_WARNING);
			return false;
		}
		return new static();
	}

	public static function where($field, $comparison_operator, $value=false) {
		checkColumnNameIsValid($field);

		if ($value === false) {
			$value = $comparison_operator;
			$comparison_operator = '=';
		}
		$comparison_operator = strtoupper($comparison_operator);
		if (in_array($comparison_operator, static::$comparison_operator_array)) {
			if (static::$where === NUll) {
				static::$where = "WHERE";
			}
			else {
				static::$where .= " AND";
			}
			static::$where .= " $field $comparison_operator ?";
			static::$data_execute[] = $value;
		}
		else {
			$text = 'Error syntax method where use comparison operator: '.implode(', ', static::$comparison_operator_array);
			static::error($text);
			return false;
		}
		return new static();
	}

	public static function orWhere($field, $comparison_operator, $value=false) {
		checkColumnNameIsValid($field);

		if ($value === false) {
			$value = $comparison_operator;
			$comparison_operator = '=';
		}
		$comparison_operator = strtoupper($comparison_operator);
		if (in_array($comparison_operator, static::$comparison_operator_array)) {
			if (static::$where === NUll) {
				static::$where = "WHERE";
			}
			else {
				static::$where .= " OR";
			}
			static::$where .= " $field $comparison_operator ?";
			static::$data_execute[] = $value;
		}
		else {
			$text = 'Error syntax method where use comparison operator: '.implode(', ', static::$comparison_operator_array);
			static::error($text);
			return false;
		}
		return new static();
	}

	public static function whereIn($field, $arr=[]) {
		checkColumnNameIsValid($field);

		if (is_string($arr)) {
			$array_strings = explode(',', $arr);
			$arr = [];
			foreach ($array_strings as $k => $v) {
				if (!empty(trim($v))) {
					$arr[] = trim($v);
				}
			}
		}
		if (empty($arr)) return new static();
		if (count($arr) == 1) {
			$instance = new static();
			return $instance->where($field, $arr[0]);
		}

		$in = '(' . implode(', ', $arr) . ')';
		$in = '';
		foreach ($arr as $value) {
			if ($in == '') {
				$in = '?';
			}
			else {
				$in .= ', ?';
			}
			static::$data_execute[] = $value;
		}
		$in = '(' . $in . ')';
		if (static::$where === NUll) {
			static::$where = "WHERE";
		}
		else {
			static::$where .= " AND";
		}
		static::$where .= " $field IN $in";
		return new static();
	}

	public static function whereNotIn($field, $arr=[]) {
		checkColumnNameIsValid($field);

		if (is_string($arr)) {
			$array_strings = explode(',', $arr);
			$arr = [];
			foreach ($array_strings as $k => $v) {
				if (!empty(trim($v))) {
					$arr[] = trim($v);
				}
			}
		}
		if (empty($arr)) {
			return new static();
		}
		if (count($arr) == 1) {
			$instance = new static();
			return $instance->where($field, '<>', $arr[0]);
		}

		$in = '(' . implode(', ', $arr) . ')';
		$in = '';
		foreach ($arr as $value) {
			if ($in == '') {
				$in = '?';
			}
			else {
				$in .= ', ?';
			}
			static::$data_execute[] = $value;
		}
		$in = '(' . $in . ')';
		if (static::$where === NUll) {
			static::$where = "WHERE";
		}
		else {
			static::$where .= " AND";
		}
		static::$where .= " $field NOT IN $in";
		return new static();
	}

	public static function whereRaw($where, $data=[]) {
		// if (static::$where !== NULL) {
		// 	$text = "Error syntax use only once whereRaw and not use method: where, orWhere, whereNot, whereIn, whereNotIn,...";
		// 	static::error($text);
		// 	return false;
		// }
		$where_explode = explode('?', $where);
		if (count($where_explode) != (count($data) +1)) {
			$text = "Error syntax method where: count data != count ? in argument where: $where <br> data: " . json_encode($data);
			static::error($text);
			return false;
		}
		foreach ($data as $k => $v) {
			static::$data_execute[] = $v;
		}
		if (static::$where === NUll) {
			static::$where = "WHERE";
		}
		else {
			static::$where .= " AND";
		}
		static::$where .= " $where";
		return new static();
	}

	public static function groupBy($field) {
		checkColumnNameIsValid($field);

		if (static::$group_by === NULL) {
			static::$group_by = "GROUP BY $field";
		}
		else {
			static::$group_by .= ", $field";
		}
		return new static();
	}

	public static function whereNull($field) {
		checkColumnNameIsValid($field);

		if (static::$where === NUll) {
			static::$where = "WHERE";
		}
		else {
			static::$where .= " AND";
		}
		static::$where .= " $field IS NULL";
		return new static();
	}

	public static function whereNotNull($field) {
		checkColumnNameIsValid($field);
		
		if (static::$where === NUll) {
			static::$where = "WHERE";
		}
		else {
			static::$where .= " AND";
		}
		static::$where .= " $field IS NOT NULL";
		return new static();
	}

	public static function having($field, $operator, $value=false) {
		// checkColumnNameIsValid($field);

		if ($value === false) {
			$value = $operator;
			$operator = '=';
		}
		if (in_array($operator, static::$comparison_operator_array)) {
			if (static::$having === NUll) {
				static::$having = "HAVING";
			}
			else {
				static::$having .= " AND";
			}
			static::$having .= " $field $operator ?";
			static::$data_execute[] = $value;
		}
		else {
			echo 'Method having use comparison operator: '.implode(', ', static::$comparison_operator_array);
			die();
		}
		return new static();
	}

	public static function orHaving($field, $operator, $value=false) {
		// checkColumnNameIsValid($field);

		if ($value === false) {
			$value = $operator;
			$operator = '=';
		}
		if (in_array($operator, static::$comparison_operator_array)) {
			if (static::$having === NUll) {
				static::$having = "HAVING";
			}
			else {
				static::$having .= " OR";
			}
			static::$having .= " $field $operator ?";
			static::$data_execute[] = $value;
		}
		else {
			echo 'Method having use comparison operator: '.implode(', ', static::$comparison_operator_array);
			die();
		}
		return new static();
	}

	public static function orderBy($field, $direction='') {
		checkColumnNameIsValid($field);

		$direction = strtoupper($direction);
		if ($direction == 'ASC' || $direction == 'DESC') {
			if (static::$order === NULL) {
				static::$order = "ORDER BY $field $direction";
			}
			else {
				static::$order .= ", $field $direction";
			}
		}
		else {
			$text = "Error syntax method orderBy argument direction must is asc, desc ";
			trigger_error($text, E_USER_WARNING);
			return false;
		}
		return new static();
	}

	public static function orderByRand() {
		static::$order = 'ORDER BY RAND()';
		return new static();
	}

	public static function orderByRaw($sql) {
		if (static::$order === NULL) {
			static::$order = "ORDER BY $sql";
		} else {
			static::$order .= ", $sql";
		}
		return new static();
	}

	public static function limit(?int $limit=0) {
		static::$limit = "LIMIT $limit";
		return new static();
	}

	public static function offset(?int $offset) {
		static::$offset = "OFFSET $offset";
		return new static();
	}

	public static function get($type = 'object') {
		static::connect();
		static::$sql = "SELECT " . static::$select . " FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit . ' ' . static::$offset;
		$stmt= static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		if ($type == 'object') {
			static::$result = $stmt->fetchAll(PDO::FETCH_OBJ);
		}
		else {
			static::$result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
		}
		static::flushData();
		return static::$result;
	}

	public static function first($type = 'object'){
		static::connect();
		static::$sql = "SELECT " . static::$select . " FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit . ' ' . static::$offset;
		$stmt= static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		if ($type == 'object') {
			static::$result = $stmt->fetch(PDO::FETCH_OBJ);
		}
		else {
			static::$result = $stmt->fetch(PDO::FETCH_ASSOC); 
		}
		static::flushData();
		return static::$result;
	}

	public static function count($field='id'){
		if ($field != '*') {
			static::checkColumnNameIsValid($field);
		}

		static::connect();
		static::$sql = "SELECT COUNT($field) as total FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit . ' ' . static::$offset;
		$stmt= static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		static::$result = !empty($result) ? (int)$result->total : 0;
		static::flushData();
		return static::$result;
	}

	public static function max($field='id'){
		checkColumnNameIsValid($field);

		static::connect();
		static::$sql = "SELECT MAX($field) as max FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit . ' ' . static::$offset;
		$stmt= static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		static::$result = (float)$result->max;
		static::flushData();
		return static::$result;
	}

	public static function sum($field='id'){
		checkColumnNameIsValid($field);

		static::connect();
		static::$sql = "SELECT SUM($field) as total FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit . ' ' . static::$offset;
		$stmt= static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		$result = $stmt->fetch(PDO::FETCH_OBJ);
		static::$result = $result->total === NULL ? 0 : $result->total;
		static::flushData();
		return static::$result;
	}

	public static function find(?int $id, $type = 'object'){
		static::connect();
		static::$sql = "SELECT " . static::$select . " FROM " . static::$table . " WHERE id=$id";
		$stmt= static::$conn->query(static::$sql);
		if ($type == 'object') {
			static::$result = $stmt->fetch(PDO::FETCH_OBJ);
		}
		else {
			static::$result = $stmt->fetch(PDO::FETCH_ASSOC); 
		}
		static::flushData();
		return static::$result;
	}

	public static function fetchOne($sql , $data=[], $type='object') {
		static::connect();
		static::flushData();
		$sql_explode = explode('?', $sql);
		if (count($sql_explode) != (count($data) +1)) {
			$text = "Error syntax method fetchOne: count data != count ? in argument fetchOne: $sql <br> data: " . json_encode($data);
			static::error($text);
			return false;
		}
		static::$sql = $sql;
		$stmt= static::$conn->prepare(static::$sql);
		$stmt->execute($data);
		if ($type == 'object') {
			static::$result = $stmt->fetch(PDO::FETCH_OBJ);
		}
		else {
			static::$result = $stmt->fetch(PDO::FETCH_ASSOC); 
		}
		static::flushData();
		return static::$result;
	}

	public static function fetchAll($sql, $data=[], $type='object') {
		static::connect();
		static::flushData();
		$sql_explode = explode('?', $sql);
		if (count($sql_explode) != (count($data) +1)) {
			$text = "Error syntax method fetchAll: count data != count ? in argument fetchAll: $sql <br> data: " . json_encode($data);
			static::error($text);
			return false;
		}
		static::$sql = $sql;
		$stmt= static::$conn->prepare(static::$sql);
		$stmt->execute($data);
		if ($type == 'object') {
			static::$result = $stmt->fetchAll(PDO::FETCH_OBJ);
		}
		else {
			static::$result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
		}
		static::flushData();
		return static::$result;
	}

	public static function fetchCount($sql, $data=[]) {
		$query = static::fetchOne($sql, $data);
		$total = 0;
		if (isset($query->total)) {
			if (preg_match('/^[0-9]+$/', $query->total)) {
				$query->total = (int)$query->total;
			}
			return $query->total;
		}
		return $total;
	}

	public static function truncate() {
		static::flushData();
		static::connect();
		static::$sql = "TRUNCATE TABLE " . static::$table;
		return static::execute();
	}

	public static function isSqlInjection($str, $keywords=null) {
		if ($keywords === null) {
			$keywords = ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'FROM', 'WHERE', 'OR', 'AND'];
		}
		foreach ($keywords as $keyword) {
			if (stripos($str, $keyword) !== false) {
				return true;
			}
		}
		$specialChars = [';', '--', '/*', '*/'];
		foreach ($specialChars as $char) {
			if (strpos($str, $char) !== false) {
				return true;
			}
		}
		return false;
	}

	public static function insertRaw($sql, $data=[]){
		if (static::isSqlInjection($sql, ['SELECT', 'DELETE', 'FROM', 'OR', 'AND'])) {
    		exit('Error syntax sql: ' . $sql);
    	}
		static::connect();
		static::$sql = $sql;
		static::$data_execute = $data;
		return static::execute();
	}

	public static function updateRaw($sql, $data=[]){
		if (static::isSqlInjection($sql, ['SELECT', 'DELETE', 'FROM', 'OR', 'AND'])) {
    		exit('Error syntax sql: ' . $sql);
    	}
		static::connect();
		static::$sql = $sql;
		static::$data_execute = $data;
		return static::execute();
	}

	public function paginate(int $page_size = 20, int $page = 1, $type='object'){
		static::connect();
		$sql_count = "SELECT count(*) as total FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' LIMIT 1';

		$stmt = static::$conn->prepare($sql_count);
		$stmt->execute(static::$data_execute);
		static::$result = $stmt->fetch(PDO::FETCH_OBJ);
		$total = static::$result->total;

		static::limit($page_size);
		static::offset(($page - 1) * $page_size);
		
		static::$sql = "SELECT " . static::$select . " FROM " . static::$table . ' ' . static::$join . ' ' . static::$where . ' ' . static::$group_by . ' ' . static::$having . ' ' . static::$order . ' ' . static::$limit . ' ' . static::$offset;

		$stmt = static::$conn->prepare(static::$sql);
		$stmt->execute(static::$data_execute);
		static::$result = $stmt->fetchAll(PDO::FETCH_OBJ);
		$result = !empty(static::$result) ? static::$result : [];
		$paginate = [
			"data" => $result,
			"current_page" => $page,
			"row_per_page" => $page_size,
		];

		static::flushData();

		$paginate["total"] = $total;
		$paginate["total_page"] = ceil($total / $page_size);
		return $paginate;
	}

}
