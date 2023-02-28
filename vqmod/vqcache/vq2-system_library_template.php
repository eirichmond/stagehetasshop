<?php
class Template {
	public $data = array();
	
  	//karapuz
	public function showTemplate($tpl_name, $data) {
	
		if (!file_exists(DIR_TEMPLATE . $tpl_name)) {
			$tpl_name = "default/template/" . $tpl_name;
		}
		
		$template = new Template();
		$template->data = $data;
		$text = $template->fetch($tpl_name);
		echo $text;
 	}
	///karapuz
	public function fetch($filename) {
		$file = DIR_TEMPLATE . $filename;
    
		if (file_exists($file)) {
			extract($this->data);
			
      		ob_start();
      
	  		include(VQMod::modCheck($file));
      
	  		$content = ob_get_contents();

      		ob_end_clean();

      		return $content;
    	} else {
			trigger_error('Error: Could not load template ' . $file . '!');
			exit();				
    	}	
	}
}
?>