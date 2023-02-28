<?php
/*
	Project : Ka Extensions
	Author  : karapuz <support@ka-station.com>

	Version : 2 ($Revision: 36 $)
*/

require_once(DIR_SYSTEM . 'engine/ka_controller.php');

class ControllerExtensionKaExtensions extends KaController {

	protected $extension_version = '2.2.0';

	public function index() {
		$this->loadLanguage('extension/ka_extensions');
		 
		$this->document->setTitle($this->language->get('heading_title'));

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/ka_extensions', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['heading_title']   = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_confirm']    = $this->language->get('text_confirm');

		$this->data['column_name']     = $this->language->get('column_name');
		$this->data['column_action']   = $this->language->get('column_action');

		$this->data['extension_version'] = $this->extension_version;
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['error'])) {
			$this->data['error'] = $this->session->data['error'];
		
			unset($this->session->data['error']);
		} else {
			$this->data['error'] = '';
		}

		$this->load->model('setting/extension');

		$extensions = $this->model_setting_extension->getInstalled('ka_extensions');
		
		foreach ($extensions as $key => $value) {
			if (!file_exists(DIR_APPLICATION . 'controller/ka_extensions/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('ka_extensions', $value);
				
				unset($extensions[$key]);
			}
		}
	
		$this->data['extensions'] = array();
		
		$files = glob(DIR_APPLICATION . 'controller/ka_extensions/*.php');
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				
				$this->loadLanguage('ka_extensions/' . $extension);

				require_once(DIR_APPLICATION . 'controller/ka_extensions/' . $extension . '.php');
				$class = 'ControllerKaExtensions' . str_replace('_', '', $extension);
				$class = new $class($this->registry);
				
				$action = array();
				
				if (!in_array($extension, $extensions)) {
					$action[] = array(
						'text' => $this->language->get('text_install'),
						'href' => $this->url->link('extension/ka_extensions/install', 'token=' . $this->session->data['token'] . '&extension=' . $extension, 'SSL')
					);
				} else {
					$action[] = array(
						'text' => $this->language->get('text_edit'),
						'href' => $this->url->link('ka_extensions/' . $extension . '', 'token=' . $this->session->data['token'], 'SSL')
					);
								
					$action[] = array(
						'text' => $this->language->get('text_uninstall'),
						'href' => $this->url->link('extension/ka_extensions/uninstall', 'token=' . $this->session->data['token'] . '&extension=' . $extension, 'SSL')
					);
				}

				
				if (method_exists($class, 'getTitle')) {
					$heading_title = $class->getTitle();
				} else {
					$heading_title = $this->language->get('heading_title');
				}
				
				$this->data['extensions'][] = array(
					'name'   => $heading_title,
					'action' => $action
				);
			}
		}

		$this->template = 'extension/ka_extensions.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	
	public function install() {
		$this->loadLanguage('extension/ka_extensions');

		if (!$this->user->hasPermission('modify', 'extension/ka_extensions') &&
			!$this->user->hasPermission('modify', 'user/user_permission')
		) {
			$this->session->data['error'] = $this->language->get('error_permission'); 
			
			$this->redirect($this->url->link('extension/ka_extensions', 'token=' . $this->session->data['token'], 'SSL'));
		} else {
		
			require_once(DIR_APPLICATION . 'controller/ka_extensions/' . $this->request->get['extension'] . '.php');
			
			$class = 'ControllerKaExtensions' . str_replace('_', '', $this->request->get['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'prepareInstallation')) {
				$res = $class->prepareInstallation();
				
				if (empty($res)) {
					$this->redirect($this->url->link('extension/ka_extensions', 'token=' . $this->session->data['token'], 'SSL'));
				} elseif ($res == 'redirect') {
					$this->redirect($this->url->link('extension/ka_extensions/install', 'extension=' . $this->request->get['extension'] . '&token=' . $this->session->data['token'], 'SSL'));
				}
			}
			
			$success = false;
			if (method_exists($class, 'install')) {
				$success = $class->install();
			}
		
			if ($success) {
				$this->load->model('setting/extension');
				$this->model_setting_extension->install('ka_extensions', $this->request->get['extension']);

				$this->load->model('user/user_group');
			
				$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'ka_extensions/' . $this->request->get['extension']);
				$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'ka_extensions/' . $this->request->get['extension']);
				
				$this->addTopMessage('Extension is installed successfully.');
			} else {
				$this->addTopMessage("Extension is not installed", 'E');
			}
			
			$this->redirect($this->url->link('extension/ka_extensions', 'token=' . $this->session->data['token'], 'SSL'));
		}
	}
	
	
	public function uninstall() {
		$this->loadLanguage('extension/ka_extensions');

		if (!$this->user->hasPermission('modify', 'extension/ka_extensions') &&
			!$this->user->hasPermission('modify', 'user/user_permission')
		) {
			$this->session->data['error'] = $this->language->get('error_permission'); 
			
			$this->redirect($this->url->link('extension/ka_extensions', 'token=' . $this->session->data['token'], 'SSL'));
		} else {		
			$this->load->model('setting/extension');
			$this->load->model('setting/setting');
					
			$this->model_setting_extension->uninstall('ka_extensions', $this->request->get['extension']);
		
			$this->model_setting_setting->deleteSetting($this->request->get['extension']);
		
			require_once(DIR_APPLICATION . 'controller/ka_extensions/' . $this->request->get['extension'] . '.php');
			
			$class = 'ControllerKaExtensions' . str_replace('_', '', $this->request->get['extension']);
			$class = new $class($this->registry);
			
			if (method_exists($class, 'uninstall')) {
				$class->uninstall();
			}
		
			$this->redirect($this->url->link('extension/ka_extensions', 'token=' . $this->session->data['token'], 'SSL'));	
		}
	}
}
?>