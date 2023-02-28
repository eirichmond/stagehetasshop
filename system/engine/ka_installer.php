<?php
/*
	Project: Ka-Extensions
	Author : karapuz <support@ka-station.com>

	Version: 2 ($Revision: 35 $)
*/

require_once(DIR_SYSTEM . 'engine/ka_controller.php');

class KaInstaller extends KaController {

	// contstants
	//
	protected $extension_version = '0.0.0';
	protected $min_store_version = '1.5.1';
	protected $max_store_version = '1.5.5.9';
	
	protected $tables;
	protected $xml_file   = 'ka_file.xml';

	// temporary variables
	//
	protected $db_patched = false;
	
	
	protected function init() {
		return true;
	}

	
	public function safeQuery($query) {
	
		if (class_exists('MijoShop')) {
			$prefix = DB_PREFIX;
			$prefix = MijoShop::get('db')->getDbo()->replacePrefix($prefix);
			$query = str_replace(DB_PREFIX, $prefix, $query);
		}
		
		$result = $this->db->query($query);
		
		return $result;
	}
	
	/*
		Compatible db may be fully patched or not patched at all. Partial changes are
		treated as a corrupted db.

		Returns
			true  - db is compatible
			false - db is not compatible

	*/
	protected function checkDBCompatibility(&$messages) {

		$this->db_patched = false;
		if (empty($this->tables)) {
			return true;
		}

		foreach ($this->tables as $tk => $tv) {

			$tbl = DB_PREFIX . $tk;
			$res = $this->safeQuery("SHOW TABLES LIKE '$tbl'");

			if (!empty($res->rows)) {
				$this->tables[$tk]['exists'] = true;
			} else {
				continue;
			}

			$fields = $this->safeQuery("DESCRIBE `$tbl`");
			if (empty($fields->rows)) {
				$messages .= "Table '$tbl' exists in the database but it is empty. Please remove this table and try to install the extension again.";
				return false;
			}

			// check fields 

			$db_fields = array();
			foreach ($fields->rows as $v) {
				$db_fields[$v['Field']] = array(
					'type'  => $v['Type']
				);
			}

			foreach ($tv['fields'] as $fk => $field) {
			
				if (empty($db_fields[$fk])) {
					continue;
				}

				// if the field is found we validate its type

				$db_field = $db_fields[$fk];

				if ($field['type'] != $db_field['type']) {
					$messages .= "Field type '$db_field[type]' for '$fk' in the table '$tbl' does not match the required field type '$field[type]'.";
					return false;
				} else {
					$this->tables[$tk]['fields'][$fk]['exists'] = true;
					$this->db_patched = true;
				}
			}

			// check indexes
			/*
				We do not compare index fields yet, just ensure that the index with the appropriate
				name exists for the table.
			*/
			if (!empty($tv['indexes'])) {

				$rec = $this->safeQuery("SHOW INDEXES FROM `$tbl`");
				$db_indexes = array();
				foreach ($rec->rows as $v) {
					$db_indexes[$v['Key_name']]['columns'][] = $v['Column_name'];
				}

				foreach ($tv['indexes'] as $ik => $index) {
					if (!empty($db_indexes[$ik])) {
						$this->tables[$tk]['indexes'][$ik]['exists'] = true;
					}
				}
			}
		}

		return true;
	}

	protected function patchDB(&$messages) {

		// create db
		if (empty($this->tables)) {
			return true;
		}

		foreach ($this->tables as $tk => $tv) {
			if (empty($tv['exists'])) {
				$this->safeQuery($tv['query']);
				continue;
			}

			if (!empty($tv['fields'])) {
				foreach ($tv['fields'] as $fk => $fv) {
					if (empty($fv['exists'])) {
 						if (empty($fv['query'])) {
 							$messages .= "Installation error. Cannot create '$tk.$fk' field. Try to create it manually and run the installation again.";
 							return false;
 						}
						$this->safeQuery($fv['query']);
					}
				}
			}

			if (!empty($tv['indexes'])) {
				foreach ($tv['indexes'] as $ik => $iv) {
					if (empty($iv['exists'])) {
						$this->safeQuery($iv['query']);
					}
				}
			}
		}
		
		return true;
	}

	
	protected function getVQModFolder($vqmod) {
	
		$vqmod_folder = '';
		if (is_object($vqmod)) {
			if (!empty($vqmod->logFilePath)) {
				$vqmod_folder = dirname($vqmod->logFilePath);
			} else {
				$vqmod_folder = dirname($vqmod->logFolder);
			}
		} else {
			$vqmod_folder = VQMod::$logFolder;
		}
		
		return $vqmod_folder;
	}


	protected function getVQModLogSize() {
		global $vqmod;
	
		if (is_object($vqmod)) {
			$vqmod_log_file   = (!empty($vqmod->logFilePath) ? $vqmod->logFilePath : '');
			$vqmod_log_folder = (!empty($vqmod->logFolder) ? $vqmod->logFolder : '');		
		} else {
			$vqmod_log_file   = '';
			$vqmod_log_folder = VQMod::$logFolder;
		}
		
		$log_size     = 0;
		$dir_store    = dirname(DIR_APPLICATION) . "/";
	
		// check size for the log file in vqmod directory
		//
		if (!empty($vqmod_log_file)) {
			$file = $dir_store . $vqmod_log_file;
			if (is_file($file) && file_exists($file)) {
				$log_size += filesize($file);
			}
		}
		
		// check log sizes in vqmod/logs folder
		//
		if (!empty($vqmod_log_folder)) {
			$files = glob($dir_store . $vqmod_log_folder . '*.log');
			if (!empty($files)) {
				foreach ($files as $file) {
					if (is_file($file) && file_exists($file)) {
						$log_size += filesize($file);
					}
				}
			}
		}

		return $log_size;
	}


	/*
		
	*/		
	protected function applyXml(&$messages) {
		global  $vqmod;
		
		$dir_store = dirname(DIR_APPLICATION) . "/";
		
		$vqmod_folder = $this->getVQModFolder($vqmod);
		$xml_folder   = $dir_store . $vqmod_folder . "/xml/";
		
		$xml_file = $xml_folder . $this->xml_file;
		$patched_files = $this->getPatchedFiles($xml_file);
		if (empty($patched_files)) {
			return true;
		}
		
		foreach($patched_files as $pfk => $pfv) {
			$file = KaVQMod::modCheck($dir_store . $pfv);
		}

		return true;
	}

		
	protected function getPatchedFiles($xml_file) {
			
		if (!file_exists($xml_file)) {
			return false;
		}
		
		$xml = file_get_contents($xml_file);
		
		if (!preg_match_all("/<file name=\"([^\"]*)\"/", $xml, $matches)) {
			return false;
		}

		return $matches[1];		
	}
	
	
	protected function checkCompatibility(&$messages) {

		// check store version 
		if (version_compare(VERSION, $this->min_store_version, '<')
			|| version_compare(VERSION, $this->max_store_version, '>'))
		{
			$messages .= "compatibility of this extension with your store version (" . VERSION . ") was not checked.
				Please contact an author of the extension for update.";
			return false;
		}

		$proceed_url = $this->url->link('extension/ka_extensions/install', 'extension=' . $this->request->get['extension'] . '&ignore_xml=1&token=' . $this->session->data['token'], 'SSL');
		
		if ($this->params['log_size'] < $this->getVQModLogSize() && empty($this->request->get['ignore_xml'])) {
			$messages .= "Possible errors in applying xml file. VQMod log has got new records. Check the log files and 
				contact ka-support at <b>support@ka-station.com</b> for assistance.
				<br />
				You can proceed with installation by clicking on <a href='$proceed_url'>this link</a>.";

			return false;
		}
		
		//check database
		//
		if (!$this->checkDBCompatibility($messages)) {
			return false;
		}
    
		return true;
	}
	
	
	/*
		This fuction is called by an external class.
	
		returns:
			'redirect'  - reload the page and continue installation;
			'continue'  - continue installation;
			false       - error 
	*/
	public function prepareInstallation() {

		if (!class_exists('VQModObject')) {
			$this->addTopMessage("VQMod is not installed. This module depends on this utility. Please install vqmod and continue", 'E');
			return false;
		}

		if (!empty($this->request->get['ignore_xml'])) {
			$this->params['log_size'] = 0;
			return 'continue';
		}
				
		if (isset($this->params['log_size'])) {
			return 'continue';
		}

		$this->params['log_size'] = $this->getVQModLogSize();

		$messages = array();
		if (!$this->applyXml($messages)) {
			$this->addTopMessage('Extension is not installed. List of errors are below:<br/>' . $messages, 'E');
			return false;
		}

		return 'redirect';		
	}


	public function install() {

		$this->init();
		
		if (!isset($this->params['log_size'])) {
			$this->addTopMessage("you cannot run this function directly", 'E');
			return false;
		}
				
		$success = false;

		$messages = '';
		if ($this->checkCompatibility($messages)) {
			if ($this->patchDB($messages)) {
				$success = true;			
			} else {
				$success = false;
			}
		}
		
		unset($this->params['log_size']);

		if (!$success) {
			$this->addTopMessage($messages, 'E');
		}
				
		return $success;
	}
	
	public function uninstall() {
	
		return true;
	}
}

?>