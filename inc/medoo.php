<?php
/*!
 * Medoo database framework
 * http://medoo.in
 * Version 0.9
 * 
 * Copyright 2013, Angel Lai
 * Released under the MIT license
 */
class medoo{
	protected $database_type = 'mysql';

	// For MySQL, MSSQL, Sybase
	protected $server = 'localhost';
	protected $username = 'username';
	protected $password = 'password';

	// For SQLite
	protected $database_file = '';

	// Optional
	protected $port = 3306;
	protected $charset = 'utf8';
	protected $database_name = '';
	protected $option = array();
	
	// Debug
	protected $debug = false;
	
	public function __construct($options){
		try{
			$type = strtolower($this->database_type);
			if(is_string($options)){
				if($type == 'sqlite'){
					$this->database_file = $options;
				}else{
					$this->database_name = $options;
				}
			}else{
				foreach($options as $option => $value){
					$this->$option = $value;
				}
			}
			$type = strtolower($this->database_type);
			if(isset($this->port) && is_int($this->port * 1)){
				$port = 'port=' . $this->port . ';';
			}
			switch($type){
				case 'mysql':
				case 'pgsql':
					$this->pdo = new PDO(
						$type . ':host=' . $this->server . ';' . $port . 'dbname=' . $this->database_name, 
						$this->username,
						$this->password,
						$this->option
					);
					$this->pdo->exec('SET NAMES \'' . $this->charset . '\'');
					break;
				case 'mssql':
				case 'sybase':
					$this->pdo = new PDO(
						$type . ':host=' . $this->server . ';' . $port . 'dbname=' . $this->database_name . ',' .
						$this->username . ',' .
						$this->password,
						$this->option
					);
					$this->pdo->exec('SET NAMES \'' . $this->charset . '\'');
					break;
				case 'sqlite':
					$this->pdo = new PDO(
						$type . ':' . $this->database_file,
						null,
						null,
						$this->option
					);
					break;
			}
		}catch(PDOException $e){
			throw new Exception($e->getMessage());
		}
	}
	
	public function beginTransaction(){
		$this->pdo->beginTransaction();
	}
	public function commit(){
		$this->pdo->commit();
	}
	public function rollBack(){
		$this->pdo->rollBack();
	}
	
	public function query($query){
		$this->queryString = $query;
		return $this->debug ? $query : $this->pdo->query($query);
	}
	public function _query($query){
		$this->debug = true;
		echo $this->query($query);
	}
	
	public function exec($query){
		$this->queryString = $query;
		return $this->debug ? $query : $this->pdo->exec($query);
	}
	public function _exec($query){
		$this->debug = true;
		echo $this->exec($query);
	}

	public function quote($string){
		return $this->pdo->quote($string);
	}
	
	protected function column_quote($string){
		return '`' . str_replace('.', '`.`', $string) . '`';
	}
	
	protected function array_quote($array){
		$temp = array();
		foreach($array as $value){
			$temp[] = is_int($value) ? $value : $this->pdo->quote($value);
		}
		return implode($temp, ',');
	}
	
	protected function inner_conjunct($data, $conjunctor, $outer_conjunctor){
		$haystack = array();
		foreach($data as $value){
			$haystack[] = '(' . $this->data_implode($value, $conjunctor) . ')';
		}
		return implode($outer_conjunctor . ' ', $haystack);
	}
	
	protected function data_implode($data, $conjunctor, $outer_conjunctor = null){
		$wheres = array();
		foreach($data as $key => $value){
			if(($key == 'AND' || $key == 'OR') && is_array($value)){
				$wheres[] = 0 !== count(array_diff_key($value, array_keys(array_keys($value)))) ?
					'(' . $this->data_implode($value, ' ' . $key) . ')' :
					'(' . $this->inner_conjunct($value, ' ' . $key, $conjunctor) . ')';
			}else{
				preg_match('/([\w\.]+)(\[(\>|\>\=|\<|\<\=|\!|\<\>)\])?/i', $key, $match);
				if(isset($match[3])){
					if($match[3] == ''){
						$wheres[] = $this->column_quote($match[1]) . ' ' . $match[3] . '= ' . $this->quote($value);
					}
					else if($match[3] == '!'){
						$column = $this->column_quote($match[1]);
						switch(gettype($value)){
							case 'NULL':
								$wheres[] = $column . ' IS NOT NULL';
								break;
							case 'array':
								$wheres[] = $column . ' NOT IN (' . $this->array_quote($value) . ')';
								break;
							case 'integer':
								$wheres[] = $column . ' != ' . $value;
								break;
							case 'string':
								$wheres[] = $column . ' != ' . $this->quote($value);
								break;
						}
					}else{
						if($match[3] == '<>'){
							if(is_array($value)){
								if(is_numeric($value[0]) && is_numeric($value[1])){
									$wheres[] = $this->column_quote($match[1]) . ' BETWEEN ' . $value[0] . ' AND ' . $value[1];
								}else{
									$wheres[] = $this->column_quote($match[1]) . ' BETWEEN ' . $this->quote($value[0]) . ' AND ' . $this->quote($value[1]);
								}
							}
						}else{
							if(is_numeric($value)){
								$wheres[] = $this->column_quote($match[1]) . ' ' . $match[3] . ' ' . $value;
							}else{
								$datetime = strtotime($value);
								if($datetime){
									$wheres[] = $this->column_quote($match[1]) . ' ' . $match[3] . ' ' . $this->quote(date('Y-m-d H:i:s', $datetime));
								}
							}
						}
					}
				}else{
					if(is_int($key)){
						$wheres[] = $this->quote($value);
					}else{
						$column = $this->column_quote($match[1]);
						switch(gettype($value)){
							case 'NULL':
								$wheres[] = $column . ' IS NULL';
								break;
							case 'array':
								$wheres[] = $column . ' IN (' . $this->array_quote($value) . ')';
								break;
							case 'integer':
								$wheres[] = $column . ' = ' . $value;
								break;
							case 'string':
								$wheres[] = $column . ' = ' . $this->quote($value);
								break;
						}
					}
				}
			}
		}
		return implode($conjunctor . ' ', $wheres);
	}

	public function where_clause($where){
		$where_clause = '';
		if(is_array($where)){
			$single_condition = array_diff_key($where, array_flip(
				explode(' ', 'AND OR GROUP ORDER HAVING LIMIT LIKE MATCH')
			));
			if($single_condition != array()){
				$where_clause = ' WHERE ' . $this->data_implode($single_condition, '');
			}
			if(isset($where['AND'])){
				$where_clause = ' WHERE ' . $this->data_implode($where['AND'], ' AND');
			}
			if(isset($where['OR'])){
				$where_clause = ' WHERE ' . $this->data_implode($where['OR'], ' OR');
			}
			if(isset($where['LIKE'])){
				$like_query = $where['LIKE'];
				if(is_array($like_query)){
					$is_OR = isset($like_query['OR']);
					if($is_OR || isset($like_query['AND'])){
						$connector = $is_OR ? 'OR' : 'AND';
						$like_query = $is_OR ? $like_query['OR'] : $like_query['AND'];
					}else{
						$connector = 'AND';
					}
					$clause_wrap = array();
					foreach($like_query as $column => $keyword){
						if(is_array($keyword)){
							foreach($keyword as $key){
								$clause_wrap[] = $this->column_quote($column) . ' LIKE ' . $this->quote('%' . $key . '%');
							}
						}else{
							$clause_wrap[] = $this->column_quote($column) . ' LIKE ' . $this->quote('%' . $keyword . '%');
						}
					}
					$where_clause .= ($where_clause != '' ? ' AND ' : ' WHERE ') . '(' . implode($clause_wrap, ' ' . $connector . ' ') . ')';
				}
			}
			if(isset($where['MATCH'])){
				$match_query = $where['MATCH'];
				if(is_array($match_query) && isset($match_query['columns']) && isset($match_query['keyword'])){
					$where_clause .= ($where_clause != '' ? ' AND ' : ' WHERE ') . ' MATCH (`' . str_replace('.', '`.`', implode($match_query['columns'], '`, `')) . '`) AGAINST (' . $this->quote($match_query['keyword']) . ')';
				}
			}
			if(isset($where['GROUP'])){
				$where_clause .= ' GROUP BY ' . $this->column_quote($where['GROUP']);
			}
			if(isset($where['ORDER'])){
				preg_match('/(^[a-zA-Z0-9_\-\.]*)(\s*(DESC|ASC))?/', $where['ORDER'], $order_match);
				$where_clause .= ' ORDER BY `' . str_replace('.', '`.`', $order_match[1]) . '` ' . (isset($order_match[3]) ? $order_match[3] : '');
				if(isset($where['HAVING'])){
					$where_clause .= ' HAVING ' . $this->data_implode($where['HAVING'], '');
				}
			}
			if(isset($where['LIMIT'])){
				if(is_numeric($where['LIMIT'])){
					$where_clause .= ' LIMIT ' . $where['LIMIT'];
				}
				if(is_array($where['LIMIT']) && is_numeric($where['LIMIT'][0]) && is_numeric($where['LIMIT'][1])){
					$where_clause .= ' LIMIT ' . $where['LIMIT'][0] . ',' . $where['LIMIT'][1];
				}
			}
		}else{
			if($where != null){
				$where_clause .= ' ' . $where;
			}
		}
		return $where_clause;
	}
		
	public function select($table, $join, $columns = null, $where = null){
		$table = '`' . $table . '`';
		$join_key = is_array($join) ? array_keys($join) : null;
		if(strpos($join_key[0], '[') !== false){
			$table_join = array();
			$join_array = array(
				'>' => 'LEFT',
				'<' => 'RIGHT',
				'<>' => 'FULL',
				'><' => 'INNER'
			);
			foreach($join as $sub_table => $relation){
				preg_match('/(\[(\<|\>|\>\<|\<\>)\])?([a-zA-Z0-9_\-]*)/', $sub_table, $match);
				if($match[2] != '' && $match[3] != ''){
					if(is_string($relation)){
						$relation = 'USING (`' . $relation . '`)';
					}
					if (is_array($relation)){
						if(isset($relation[0])){ // For ['column1', 'column2']
							$relation = 'USING (`' . implode($relation, '`, `') . '`)';
						}else{ // For ['column1' => 'column2']
							$relation = 'ON ' . $table . '.`' . key($relation) . '` = `' . $match[3] . '`.`' . current($relation) . '`';
						}
					}
					$table_join[] = $join_array[ $match[2] ] . ' JOIN `' . $match[3] . '` ' . $relation;
				}
			}
			$table .= ' ' . implode($table_join, ' ');
		}else{
			$where = $columns;
			$columns = $join;
		}
		$where_clause = $this->where_clause($where);
		$query = $this->query('SELECT ' . (is_array($columns) ? $this->column_quote( implode('`, `', $columns) ) : ($columns == '*' ? '*' : '`' . $columns . '`')) . ' FROM ' . $table . $where_clause);
		return is_string($query) ? $query : ($query ? $query->fetchAll((is_string($columns) && $columns != '*') ? PDO::FETCH_COLUMN : PDO::FETCH_ASSOC) : false);
	}
	public function _select($table, $join, $columns = null, $where = null){
		$this->debug = true;
		echo $this->select($table, $join, $columns, $where);
	}
		
	public function insert($table, $datas){
		$lastId = array();
		$query = array();
		// Check indexed or associative array
		if(!isset($datas[0])){
			$datas = array($datas);
		}
		foreach($datas as $data){
			$keys = implode("`, `", array_keys($data));
			$values = array();
			foreach($data as $key => $value){
				switch(gettype($value)){
					case 'NULL':
						$values[] = 'NULL';
						break;
					case 'array':
						$values[] = $this->quote(serialize($value));
						break;
					case 'integer':
					case 'string':
						$values[] = $this->quote($value);
						break;
				}
			}
			$exec = $this->exec('INSERT INTO `' . $table . '` (`' . $keys . '`) VALUES (' . implode($values, ', ') . ')');
			is_string($exec) ? $query[] = $exec : $lastId[] = $this->pdo->lastInsertId();
		}
		return $query ? implode('; ', $query) : (count($lastId) > 1 ? $lastId : $lastId[0]);
	}
	public function _insert($table, $datas){
		$this->debug = true;
		echo $this->insert($table, $datas);
	}
	
	public function update($table, $data, $where = null){
		$fields = array();
		foreach($data as $key => $value){
			$key = '`' . $key . '`';
			if(is_array($value)){
				$fields[] = $key . '=' . $this->quote(serialize($value));
			}else{
				preg_match('/([\w]+)(\[(\+|\-)\])?/i', $key, $match);
				if(isset($match[3])){
					if(is_numeric($value)){
						$fields[] = $match[1] . ' = ' . $match[1] . ' ' . $match[3] . ' ' . $value;
					}
				}else{
					switch(gettype($value)){
						case 'NULL':
							$fields[] = $key . ' = NULL';
							break;
						case 'array':
							$fields[] = $key . ' = ' . $this->quote(serialize($value));
							break;
						case 'integer':
						case 'string':
							$fields[] = $key . ' = ' . $this->quote($value);
							break;
					}
				}
			}
		}
		return $this->exec('UPDATE `' . $table . '` SET ' . implode(', ', $fields) . $this->where_clause($where));
	}
	public function _update($table, $data, $where = null){
		$this->debug = true;
		echo $this->update($table, $data, $where);
	}
	
	public function delete($table, $where){
		return $this->exec('DELETE FROM `' . $table . '`' . $this->where_clause($where));
	}
	public function _delete($table, $where){
		$this->debug = true;
		echo $this->delete($table, $where);
	}
	
	public function replace($table, $columns, $search = null, $replace = null, $where = null){
		if(is_array($columns)){
			$replace_query = array();
			foreach($columns as $column => $replacements){
				foreach($replacements as $replace_search => $replace_replacement){
					$replace_query[] = $column . ' = REPLACE(`' . $column . '`, ' . $this->quote($replace_search) . ', ' . $this->quote($replace_replacement) . ')';
				}
			}
			$replace_query = implode(', ', $replace_query);
			$where = $search;
		}else{
			if(is_array($search)){
				$replace_query = array();
				foreach($search as $replace_search => $replace_replacement){
					$replace_query[] = $columns . ' = REPLACE(`' . $columns . '`, ' . $this->quote($replace_search) . ', ' . $this->quote($replace_replacement) . ')';
				}
				$replace_query = implode(', ', $replace_query);
				$where = $replace;
			}else{
				$replace_query = $columns . ' = REPLACE(`' . $columns . '`, ' . $this->quote($search) . ', ' . $this->quote($replace) . ')';
			}
		}
		return $this->exec('UPDATE `' . $table . '` SET ' . $replace_query . $this->where_clause($where));
	}
	public function _replace($table, $columns, $search = null, $replace = null, $where = null){
		$this->debug = true;
		echo $this->replace($table, $columns, $search, $replace, $where);
	}

	public function get($table, $columns, $where = null){
		if(!isset($where)){
			$where = array();
		}
		$where['LIMIT'] = 1;
		$data = $this->select($table, $columns, $where);
		return is_string($data) ? $data : (isset($data[0]) ? $data[0] : false);
	}
	public function _get($table, $columns, $where = null){
		$this->debug = true;
		echo $this->get($table, $columns, $where);
	}

	public function has($table, $where){
		$query = $this->query('SELECT EXISTS(SELECT 1 FROM `' . $table . '`' . $this->where_clause($where) . ')');
		return is_string($query) ? $query : $query->fetchColumn() === '1';
	}
	public function _has($table, $where){
		$this->debug = true;
		echo $this->has($table, $where);
	}

	public function count($table, $where = null){
		$query = $this->query('SELECT COUNT(*) FROM `' . $table . '`' . $this->where_clause($where));
		return is_string($query) ? $query : 0 + ($query->fetchColumn());
	}
	public function _count($table, $where = null){
		$this->debug = true;
		echo $this->count($table, $where);
	}

	public function max($table, $column, $where = null){
		$query = $this->query('SELECT MAX(`' . $column . '`) FROM `' . $table . '`' . $this->where_clause($where));
		return is_string($query) ? $query : 0 + ($query->fetchColumn());
	}
	public function _max($table, $column, $where = null){
		$this->debug = true;
		echo $this->max($table, $column, $where);
	}

	public function min($table, $column, $where = null){
		$query = $this->query('SELECT MIN(`' . $column . '`) FROM `' . $table . '`' . $this->where_clause($where));
		return is_string($query) ? $query : 0 + ($query->fetchColumn());
	}
	public function _min($table, $column, $where = null){
		$this->debug = true;
		echo $this->min($table, $column, $where);
	}

	public function avg($table, $column, $where = null){
		$query = $this->query('SELECT AVG(`' . $column . '`) FROM `' . $table . '`' . $this->where_clause($where));
		return is_string($query) ? $query : 0 + ($query->fetchColumn());
	}
	public function _avg($table, $column, $where = null){
		$this->debug = true;
		echo $this->avg($table, $column, $where);
	}

	public function sum($table, $column, $where = null){
		$query = $this->query('SELECT SUM(`' . $column . '`) FROM `' . $table . '`' . $this->where_clause($where));
		return is_string($query) ? $query : 0 + ($query->fetchColumn());
	}
	public function _sum($table, $column, $where = null){
		$this->debug = true;
		echo $this->sum($table, $column, $where);
	}

	public function error(){
		return $this->pdo->errorInfo();
	}

	public function last_query(){
		return $this->queryString;
	}

	public function info(){
		return array(
			'server' => $this->pdo->getAttribute(PDO::ATTR_SERVER_INFO),
			'client' => $this->pdo->getAttribute(PDO::ATTR_CLIENT_VERSION),
			'driver' => $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
			'version' => $this->pdo->getAttribute(PDO::ATTR_SERVER_VERSION),
			'connection' => $this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS)
		);
	}
}
?>