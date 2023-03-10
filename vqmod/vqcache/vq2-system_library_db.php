<?php
class DB {
	private $driver;
	
	public function __construct($driver, $hostname, $username, $password, $database) {
		if (file_exists(DIR_DATABASE . $driver . '.php')) {
			require_once(VQMod::modCheck(DIR_DATABASE . $driver . '.php'));
		} else {
			exit('Error: Could not load database file ' . $driver . '!');
		}
				
		$this->driver = new $driver($hostname, $username, $password, $database);
	}
		
  	public function query($sql) {
		return $this->driver->query($sql);
  	}
	
	public function escape($value) {
		return $this->driver->escape($value);
	}
	
  	public function countAffected() {
		return $this->driver->countAffected();
  	}

  	//karapuz
  	public function isKaInstalled($extension) {
		static $installed = array();
		
		if (isset($installed[$extension])) {
			return $installed[$extension];
		}
		
		$query = $this->query("SELECT * FROM " . DB_PREFIX . "extension WHERE code = '$extension'");
		if (empty($query->num_rows)) {
			$installed[$extension] = false;
			return false;
		}
		
		$installed[$extension] = true;
		
		return true;
  	}
  	///karapuz
  	public function getLastId() {
		return $this->driver->getLastId();
  	}	
}
?>
