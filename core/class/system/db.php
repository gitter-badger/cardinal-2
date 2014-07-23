<?
if(!defined("IS_CORE")) {
echo "403 ERROR";
die;
}

final class db {

	private static $mc;
	private static $qid;
	private static $type = "mysql";
	private static $type_error = 1;
	private static $param = array("sql" => "", "param" => array());
	public static $time = 0;
	public static $num = 0;
	public static $querys = array();

	function __construct() {
	global $config;
		if(function_exists("mysqli_connect")) {
			self::$type = "mysqli";

			if(!@self::$mc = mysqli_init()) {
				echo "[error]";
				die();
			}
			self::$mc->options(MYSQLI_INIT_COMMAND, "SET NAMES 'utf8'");
			self::$mc->options(MYSQLI_INIT_COMMAND, "SET CHARACTER SET 'utf8'");
			if(!self::$mc->real_connect($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['db'], 3306, false, MYSQLI_CLIENT_COMPRESS)) {
				echo "[".self::$mc->connect_errno."]: ".self::$mc->connect_error;
				die();
			}
		} else {
			if(!@self::$mc = mysql_connect($config['db']['host'], $config['db']['user'], $config['db']['pass'])) {
				echo "[".mysql_errno(self::$mc)."]: ".mysql_error(self::$mc);
				die();
			}
			mysql_select_db($config['db']['db'], self::$mc);
			self::doquery("SET NAMES 'utf8'", true);
			self::doquery("SET CHARACTER SET 'utf8'", true);
		}
	}

	private function time() {
		return microtime();
	}

	function set_type($int = 2) {
		self::$type_error = intval($int);
	}

	function query($query) {
		$stime = self::time();
		if(self::$type == "mysqli") {
			if(!(self::$qid = $return = self::$mc->query($query))) {
				self::error($query);
			}
		} else {
			if(!(self::$qid = $return = mysql_query($query, self::$mc))) {
				self::error($query);
			}
		}
		
		$etime = self::time()-$stime;
		self::$time += $etime;
		self::$num += 1;
		self::$querys[] = array("time" => $etime, "query" => htmlspecialchars($query));
	return $return;
	}

	function prepare($sql) {
		self::$param['sql'] = $sql;
	}

	function param() {
		$params = func_get_args();
		if(is_array($params[0])) {
			$param = $params[0];
		} else {
			$param = array_merge(self::$param['param'], array($params[0] => $params[1]));
		}
		self::$param['param'] = $param;
	}

	function execute() {
		$sql = self::$param['sql'];
		foreach(self::$param['param'] as $n => $v) {
			$sql = str_replace(array("::".$n, ":".$n, "$".$n), $v, $sql);
		}
		unset(self::$param);
	return self::$query($sql);
	}

	function doquery($query, $only = null, $check = false) {
		$table = preg_replace("/(.*)(FROM|TABLE|UPDATE|INSERT INTO) (.+?) (.*)/", "$3", $query);
		$badword = false;
		if((stripos($query, 'RUNCATE TABL') != FALSE) && ($table != 'shoutbox')) {
			$badword = true;
		} elseif(stripos($query, 'ROP TABL') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'ENAME TABL') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'REATE DATABAS') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'REATE TABL') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'ET PASSWOR') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'EOAD DAT') != FALSE) {
			$badword = true;
		} elseif(stripos($query, 'AUTHLEVEL') != FALSE && stripos($query, 'SELECT') !== 0) {
			$badword = true;
		}
		if($badword) {
			$message = '������, � �� ���� ��, ��� �� ��������� �������, �� �������, ������� �� ������ ������� ���� ������, �� ��������� ����� ������������� � ��� ���� ��������������.<br /><br />��� IP, � ������ ������ ��������� ������������� �������. �����!.';
			$report  = "Hacking attempt (".date("d.m.Y H:i:s")." - [".time()."]):\n";
			$report .= ">Database Inforamation\n";
			$report .= "\tID - ".$user['id']."\n";
			$report .= "\tUser - ".$user['username']."\n";
			$report .= "\tAuth level - ".$user['authlevel']."\n";
			$report .= "\tUser IP - ".$user['ragip']."\n";
			$report .= "\tUser IP at Reg - ".$user['lastip']."\n";
			$report .= "\tUser Agent - ".$user['user_agent']."\n";
			$report .= "\tRegister Time - ".$user['register_time']."\n";
			$report .= "\n";
			$report .= ">Query Information\n";
			$report .= "\tTable - ".$table."\n";
			$report .= "\tQuery - ".$query."\n";
			$report .= "\n";
			$report .= ">\$_SERVER Information\n";
			$report .= "\tIP - ".$_SERVER['REMOTE_ADDR']."\n";
			$report .= "\tHost Name - ".$_SERVER['HTTP_HOST']."\n";
			$report .= "\tUser Agent - ".$_SERVER['HTTP_USER_AGENT']."\n";
			$report .= "\tRequest Method - ".$_SERVER['REQUEST_METHOD']."\n";
			$report .= "\tCame From - ".$_SERVER['HTTP_REFERER']."\n";
			$report .= "\tPage is - ".$_SERVER['SCRIPT_NAME']."\n";
			$report .= "\tUses Port - ".$_SERVER['REMOTE_PORT']."\n";
			$report .= "\tServer Protocol - ".$_SERVER['SERVER_PROTOCOL']."\n";
			$report .= "\n--------------------------------------------------------------------------------------------------\n";
			$fp = fopen(ROOT_PATH.'core/cache/badqrys.txt', 'a');
			fwrite($fp, $report);
			fclose($fp);
			die($message);
		}
		self::$qid = self::query($query);
		if(!$check) {
			if(strpos($query, "SELECT") !== false || strpos($query, "SHOW TABLE") !== false) {
				if(!empty($only)) {
					return self::$qid;
				} else {
					return self::fetch_array();
				}
			} else {
				return self::$qid;
			}
		} else {
			return $this;
		}
	}

	function affected_rows() {
		if(self::$type == "mysqli") {
			return self::$mc->affected_rows;
		} else {
			return mysql_affected_rows(self::$mc);
		}
	}

	function insert_id() {
		if(self::$type == "mysqli") {
			return self::$mc->insert_id;
		} else {
			return mysql_insert_id(self::$mc);
		}
	}

	function last_id($table) {
		$table = self::doquery("SHOW TABLE STATUS LIKE '".$table."'");
		return $table['Auto_increment'];
	}

	function num_fields() {
		if(self::$type == "mysqli") {
			return self::$mc->field_count;
		} else {
			return mysql_num_fields(self::$mc);
		}
	}

	function select_query($query) {
		if(strpos($query, "SELECT") !== false || strpos($query, "SHOW TABLE") !== false) {
			$qid = self::query($query);
			$array = array();
			while($row=self::fetch_assoc($qid)) {
				$array[] = $row;
			}
			return $array;
		} else {
			return false;
		}
	}

	function fetch_row($query = null) {
		if(empty($query)) {
			$query = self::$qid;
		}
		if(self::$type == "mysqli") {
			return $query->fetch_row();
		} else {
			return mysql_fetch_row($query);
		}
	}

	function fetch_array($query = null) {
		if(empty($query)) {
			$query = self::$qid;
		}
		if(self::$type == "mysqli") {
			return $query->fetch_array(MYSQLI_BOTH);
		} else {
			return mysql_fetch_array($query);
		}
	}

	function fetch_assoc($query = null) {
		if(empty($query)) {
			$query = self::$qid;
		}
		if(self::$type == "mysqli") {
			return $query->fetch_assoc();
		} else {
			return mysql_fetch_assoc($query);
		}
	}

	function fetch_object($query = null, $class_name = null, $params = array()) {
		if(empty($query)) {
			$query = self::$qid;
		}
		if(self::$type == "mysqli") {
			return $query->fetch_object($class_name, $params);
		} else {
			return mysql_fetch_object($query, $class_name, $params);
		}
	}

	function num_rows($query = null) {
		if(empty($query)) {
			$query = self::$qid;
		}
		if(self::$type == "mysqli") {
			return $query->num_rows;
		} else {
			return mysql_num_rows($query);
		}
	}

	function free($query = null){
		if(empty($query)) {
			$query = self::$qid;
		}
		if(self::$type == "mysqli") {
			return @mysqli_free_result($query);
		} else {
			return @mysql_free_result($query);
		}
	}

	function close() {
		if(!self::$mc) {
			return;
		}
		if(self::$type == "mysqli") {
			self::$mc->close();
		} else {
			mysql_close(self::$mc);
		}
	}

	private function error($query) {
		if(self::$type == "mysqli") {
			$mysql_error = self::$mc->error;
			$mysql_error_num = self::$mc->errno;
		} else {
			$mysql_error = mysql_error(self::$mc);
			$mysql_error_num = mysql_errno(self::$mc);
		}

		if($query) {
			// Safify query
			$query = preg_replace("/([0-9a-f]){32}/", "********************************", $query); // Hides all hashes
		}

		$query = htmlspecialchars($query, ENT_QUOTES, 'ISO-8859-1');
		$mysql_error = htmlspecialchars($mysql_error, ENT_QUOTES, 'ISO-8859-1');

		$trace = debug_backtrace();

		$level = 0;
		if ($trace[1]['function'] == "query" ) $level = 1;
		if ($trace[2]['function'] == "doquery" ) $level = 2;

		$trace[$level]['file'] = str_replace(ROOT_PATH, "", $trace[$level]['file']);

		if(self::$type_error === 1) {
			modules::init_templates()->assign_vars(array(
				"query" => $query,
				"error" => $mysql_error,
				"error_num" => $mysql_error_num,
				"file" => $trace[$level]['file'],
				"line" => $trace[$level]['line'],
			));
			echo modules::init_templates()->complited_assing_vars("mysql_error", null);
		} else {
			echo "<center><br />".$trace[$level]['file'].":".$trace[$level]['line']."<hr />Query:<br /><textarea cols=\"40\" rows=\"5\">".$query."</textarea><hr />[".$mysql_error_num."] ".$mysql_error."<br />";
		}
		modules::init_templates()->__destruct();
		exit();
	}

	function __destruct() {
		self::close();
	}

}