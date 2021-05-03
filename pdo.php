<?php
/**
 * Clase para lanzar consultas SQL

 */
class SQL
{
	static private $_mysqli = NULL;
	static private $_ultimoSQL;
	static private $_tiempoDeEjecucionUltimaConsulta;
	static private $_tiempoDeEjecucionGlobalSQL = 0;
	static private $_debug = FALSE;
	static private $_dbnames = [];

	public static function setDBName($_dbname)
	{
		self::$_dbnames[] = $_dbname;
		self::$_mysqli = null;
	}

	public static function popDBName()
	{
		array_pop(self::$_dbnames);
		self::$_mysqli = null;
	}

	/**
	 * Si no esta creada mysqli la crea y devuelve
	 *
	 * @return mysqli conexión MySQLi
	 *
	 * @access private
	 * @static
	 *
	 */
	private static function _conectar()
	{
		if (self::$_mysqli === null) {
			$_dbname = array_values(array_slice(self::$_dbnames, -1))[0];
			self::$_mysqli = new PDO(
				"mysql:host=localhost;dbname=" . $_dbname . ";charset=utf8;",
				"Ruben",
				"sKHuFdtm9WrIuw6q",
				array(
					PDO::MYSQL_ATTR_LOCAL_INFILE => 1,
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
				)
			);
		}
		return (self::$_mysqli);
	}

	private static function _ultimoSQL($sql, $argumentos = false)
	{
		if ($argumentos !== FALSE) {
			foreach ($argumentos as $indice => $argumento) {
				$pos = strpos($sql, "?");
				if ($pos !== false) {
					$sql = substr_replace($sql, "'$argumento'", $pos, strlen("?"));
				}
			}
		}
		self::$_ultimoSQL = $sql;
	}

	public static function debug($debug)
	{
		self::$_debug = $debug;
	}

	public static function getInstance() {
		if (self::$_instance == null) {
			self::$_instance = new SQL();
		}
		return self::$_instance;
	}

	/**
	 * Lanza una consulta SQL y devuelve falso si algo ha fallado o la matriz
	 * bidimensional si no
	 *
	 * @param string $sql Consulta SQL a realizar
	 *
	 * @return array|bool|string
	 *
	 * @access public
	 *
	 */
	public static function consulta($sql)
	{
		try {
			if (self::$_mysqli === null) self::_conectar();
			$argumentos = array_slice(func_get_args(), 1);
			if (count($argumentos) == 1 && gettype($argumentos[0]) == "array") $argumentos = $argumentos[0];
			if (self::$_debug) {
				$contador = microtime(true);
			}
			$rs = self::$_mysqli->prepare($sql);
			$rs->execute($argumentos);
			$resultados = $rs->fetchAll(PDO::FETCH_ASSOC);
			if (self::$_debug) {
				$contadorSQL = microtime(true) - $contador;
				self::_ultimoSQL($rs->queryString, $argumentos);
				self::$_tiempoDeEjecucionUltimaConsulta = $contadorSQL;
				self::$_tiempoDeEjecucionGlobalSQL += self::$_tiempoDeEjecucionUltimaConsulta;
			}
			if (empty($resultados)) {
				return FALSE;
			}
			return ($resultados);
		} catch (PDOException $e) {
			$traza = $e->getTrace()[1];
			$arr = array("fichero" => $traza["file"], "linea" => $traza["line"], "funcion" => $traza["function"]);
			$arr = array_merge($arr, $traza["args"]);
			$arr["mensaje"] = $e->getMessage();
			return (self::$_debug ? $arr["mensaje"] : FALSE);
		}
	}

	public static function select($sql)
	{
		return self::consulta("select $sql", array_slice(func_get_args(), 1));
	}

	/**
	 * Lanza una consulta SQL y devuelve la cantidad
	 * de lineas actualizadas en ella
	 *
	 * @param string $sql Consulta SQL a realizar
	 *
	 * @return int cantidad de filas devueltas
	 */
	public static function actualizacion($sql)
	{
		try {
			if (self::$_mysqli === null) self::_conectar();
			$argumentos = array_slice(func_get_args(), 1);
			if (count($argumentos) == 1 && gettype($argumentos[0]) == "array") $argumentos = $argumentos[0];
			$contador = microtime(true);
			$rs = self::$_mysqli->prepare($sql);
			$rs->execute($argumentos);
			self::_ultimoSQL($rs->queryString, $argumentos);
			self::_ultimoSQL($sql, []);
			// self::$_mysqli->query($sql);
			self::$_tiempoDeEjecucionUltimaConsulta = microtime(true) - $contador;
			self::$_tiempoDeEjecucionGlobalSQL += self::$_tiempoDeEjecucionUltimaConsulta;
			return (self::$_mysqli->lastInsertId());
		} catch (PDOException $e) {
			$traza = $e->getTrace()[1];
			$arr = array("fichero" => $traza["file"], "linea" => $traza["line"], "funcion" => $traza["function"]);
			$arr = array_merge($arr, $traza["args"]);
			$arr["mensaje"] = $e->getMessage();

			return (self::$_debug ? $arr["mensaje"] : FALSE);
		}

	}

	public static function ultimoSQL()
	{
		return self::$_ultimoSQL;
	}

	public static function ultimaConsulta()
	{
		return self::ultimoSQL();
	}

	public static function tiempoEjecucionUltimaConsulta()
	{
		return self::$_tiempoDeEjecucionUltimaConsulta;
	}

	public static function tiempoEjecucionSQL()
	{
		return self::$_tiempoDeEjecucionGlobalSQL;
	}

}