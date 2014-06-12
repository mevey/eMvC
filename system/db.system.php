<?php
abstract class db {
	protected $properties = array();
	protected $recordAutoLoaded = false;
	protected $pdo;
	protected $primaryKeyField;
	protected $keyWhereClause;
	protected $tableName;

	public static $dbPrefix;
	
	public static $lastSql = '';
	
	public function db($id = null) {
		$this->pdo = pdo_db::getInstance();
		self::$dbPrefix = Registry::getInstance()->config['database']['db_prefix'];
		
		$this->tableName = get_class($this);
		$this->primaryKeyField = get_class($this).'_id';
		
		if (is_numeric($id)) {
			$dt = self::select($this->tableName, '*', $this->primaryKeyField.' = '.$id, '', '1');
			if (empty($dt)) {
				throw new Exception('Record not auto-loaded using ID '.$this->primaryKeyField.' = '.$id);
			}
			$this->keyWhereClause = $this->primaryKeyField.' = '.$id;
			$this->recordAutoLoaded = true;
			$this->properties = $dt[0];
		} else if (!empty($id)) {
			$dt = self::select($this->tableName, '*', $id, '', '1');
			if (empty($dt)) {
				throw new Exception('Record not auto-loaded using clause '.$id);
			}
			$this->keyWhereClause = $id;
			$this->recordAutoLoaded = true;
			$this->properties = $dt[0];
		}
	}
	
	public function save() {
		//print_r($this->properties);exit;
		if ($this->recordAutoLoaded) {
			return self::update(get_class($this), $this->properties, $this->keyWhereClause, '1');
		} else {
			return self::insert(get_class($this), $this->properties);
		}
	}
	
	public function deleteMe() {
		if ($this->recordAutoLoaded) {
			return self::delete(get_class($this), $this->keyWhereClause, '1');
		} else {
			throw new Exception('The object was not initialized in a way that allows automatic deletion :)');
		}
	}
	
	
	public function getProperties() {
		return $this->properties;
	}
	
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////
	
	public static function getRows($tableName,$fieldData,$where,$orderBy,$itemsPerPage,$pageNumber) {
		$dt = self::getRowCount($tableName, $where);
		if ($itemsPerPage <= 0 || !is_numeric($itemsPerPage)) {
			$itemsPerPage= 10;
		}
		$x['offset']['total_number_of_rows'] = $dt;
		$x['offset']['total_number_of_pages'] = ceil($dt/$itemsPerPage);
		$x['offset']['items_per_page'] = $itemsPerPage;
		
		if ($pageNumber > $x['offset']['total_number_of_pages']) {
			$pageNumber = $x['offset']['total_number_of_pages'];
		}
		if ($pageNumber <= 0 || !is_numeric($pageNumber)){
			$pageNumber = 1;
		}
		
		$x['offset']['current_page_number'] = $pageNumber;
		$x['offset']['row_from'] = (($pageNumber-1) * $itemsPerPage) + 1;
		$x['data'] = self::select($tableName, $fieldData, $where, $orderBy, (($pageNumber-1) * $itemsPerPage).','.$itemsPerPage);
		$x['offset']['row_to']= $x['offset']['row_from'] + count($x['data']) - 1;
		$x['offset']['current_page_row_count'] = count($x['data']);
		return $x;
	}
	
	public static function getRowCount($tableName, $where='') {
		$dt = self::select($tableName, 'count(*)', $where);
		return $dt[0]['count(*)'];
	}
	
	public function __get($propertyName) {
		if (isset($this->properties[$propertyName])) return $this->properties[$propertyName];
		throw new Exception('DB: Property "'.$propertyName.'" not set');
	}
	
	public function __set($propertyName, $propertyValue) {
		if ($propertyName =='config' && isset($this->properties['config'])) {
			throw new Exception('Modifying config not allowed!');
		}
		$this->properties[$propertyName] = $propertyValue;
	}
	
	public function __isset($propertyName) {
		return isset($this->properties[$propertyName]);
	}
	
	public static function select($tableName, $fields = '', $where='', $orderBy = '', $limit='') {
		//single table names have no space characters, while join statements would have them.
		//So we only set the prefix when a single table name is specified.
		if (self::$dbPrefix == null) self::$dbPrefix = Registry::getInstance()->config['database']['db_prefix'];
		if (strpos($tableName,' ') == false) $tableName = self::$dbPrefix.$tableName;
		
		if (is_array($fields) && !empty($fields)) {
			$fieldData = implode(', ', $fields);
		} else if (!empty($fields)) {
			$fieldData = $fields;
		} else {
			$fieldData = '*';
		}
		
		$whereData = '';
		if (!empty($where)) {
			$whereData = ' WHERE '.$where.' ';
		}
		
		$orderData = '';
		if (!empty($orderBy)) {
			$orderData = ' ORDER BY '.$orderBy.' ';
		}
		
		$limitData = '';
		if (!empty($limit)) {
			$limitData = ' LIMIT '.$limit.' ';
		}
		
		$sql = 'SELECT '.$fieldData.' FROM '.$tableName.$whereData.$orderData.$limitData;
		self::$lastSql = $sql;
		return pdo_db::getInstance()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function insert($tableName, $fields) {
		if (self::$dbPrefix == null) self::$dbPrefix = Registry::getInstance()->config['database']['db_prefix'];
		$tableName = self::$dbPrefix.$tableName;
		$strFields = '';
		$strValues = '';
		if (!is_array($fields) || empty($fields)) {
			throw new exception('The fields argument needs to be an array');
		} else {
			foreach ($fields as $fieldName => $fieldValue) {
				$strFields .= $fieldName.',';
				$strValues .= pdo_db::getInstance()->quote($fieldValue).',';
			}
			$strFields = substr($strFields,0,-1);
			$strValues = substr($strValues,0,-1);
		}
		
		$sql = 'INSERT INTO '.$tableName.' ('.$strFields.') VALUES ('.$strValues.')';
		self::$lastSql = $sql;
		pdo_db::getInstance()->exec($sql);
		return pdo_db::getInstance()->lastInsertId();
	}
	
	public static function update($tableName, $set, $where, $limit='1') {
		if (self::$dbPrefix == null) self::$dbPrefix = Registry::getInstance()->config['database']['db_prefix'];
		$tableName = self::$dbPrefix.$tableName;
		$strValues = '';
		if (!is_array($set) || empty($set) ) {
			throw new exception('The Set statement in the update statement needs to be an array');
		}
		
		foreach ($set as $fieldName => $fieldValue) {
			$strValues .= $fieldName.' = '.pdo_db::getInstance()->quote($fieldValue).',';
		}
		$strValues = substr($strValues,0,-1);
		
		$whereData = '';
		if (!empty($where)) {
			$whereData = ' WHERE '.$where.' ';
		}
		
		$limitData = '';
		if (!empty($limit)) {
			$limitData = ' LIMIT '.$limit.' ';
		}
		
		$sql = 'UPDATE '.$tableName.' SET '.$strValues.$whereData.$limitData;
		self::$lastSql = $sql;
		return pdo_db::getInstance()->exec($sql);
	}
	
	public static function delete($tableName, $where, $limit='1') {
		if (self::$dbPrefix == null) self::$dbPrefix = Registry::getInstance()->config['database']['db_prefix'];
		$tableName = self::$dbPrefix.$tableName;
		$whereData = '';
		if (!empty($where)) {
			$whereData = ' WHERE '.$where.' ';
		} else {
			throw new exception('WHERE clause in the delete statement should not be empty');
		}
		
		$limitData = '';
		if (!empty($limit)) {
			$limitData = ' LIMIT '.$limit.' ';
		}
		
		$sql = 'DELETE FROM '.$tableName.$whereData.$limitData;
		self::$lastSql = $sql;
		return pdo_db::getInstance()->exec($sql);
	}
}
