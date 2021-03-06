<?php

class ControllerExtensionModulePortitImport extends Controller {
	private $error = array(); 
	
	public function index() {   

		$this->document->addStyle('/admin/view/javascript/extension/module/portit_import.css');
		$this->document->addScript('https://cdn.jsdelivr.net/npm/dropzone@5.2.0/dist/dropzone.min.js');
		$this->document->addScript('/admin/view/javascript/extension/module/portit_import.js');

		//Load language file
		$this->load->language('extension/module/portit_import');

		//Set title from language file
		$this->document->setTitle($this->language->get('heading_title'));
		
		//Load settings model
		$this->load->model('setting/setting');
		$this->load->model('extension/portit_import');
		
		//Save settings
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('portit_import', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$text_strings = array(
				'heading_title',
				'button_save',
				'button_cancel',
				'button_add_module',
				'button_remove',
				'placeholder',
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['success']);
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
	
		//error handling
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/module/portit_import', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$data['action'] = $this->url->link('extension/module/portit_import', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$data['marketFilesList'] = $this->model_extension_portit_import->getListOfMarket();
		$data['marketFilesOptions'] = $this->model_extension_portit_import->getListOfOptions();
		$data['marketFilesPrices'] = $this->model_extension_portit_import->getListOfPrices();
		$data['marketFilesDone'] = $this->model_extension_portit_import->getListOfDone();

		$data['linkProcessPriceses'] = $this->url->link('extension/module/portit_import/processpriceses', 'token=' . $this->session->data['token'], 'SSL');
		$data['linkProcessAttribute'] = $this->url->link('extension/module/portit_import/processattribute', 'token=' . $this->session->data['token'], 'SSL');
		$data['linkClearAttribute'] = $this->url->link('extension/module/portit_import/clearattribute', 'token=' . $this->session->data['token'], 'SSL');
		$data['linkGenerateDone'] = $this->url->link('extension/module/portit_import/generateDone', 'token=' . $this->session->data['token'], 'SSL');
		$data['linkСlearOptions'] = $this->url->link('extension/module/portit_import/clearOptions', 'token=' . $this->session->data['token'], 'SSL');

		$data['token'] = $this->session->data['token'];

	
		//Check if multiple instances of this module
		$data['modules'] = array();
		
		if (isset($this->request->post['portit_import_module'])) {
			$data['modules'] = $this->request->post['portit_import_module'];
		} elseif ($this->config->get('portit_import_module')) { 
			$data['modules'] = $this->config->get('portit_import_module');
		}		

		//Prepare for display
		$this->load->model('design/layout');
		
		$data['layouts'] = $this->model_design_layout->getLayouts();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		//Send the output
		$this->response->setOutput($this->load->view('extension/module/portit_import.tpl', $data));
	}

	public function addFileMarket(){
		$this->load->model('extension/portit_import');
		$json = array();
		$uploadfile = dirname(DIR_APPLICATION).'/script/market/' . basename($_FILES['file']['name']);
		return $this->model_extension_portit_import->addFile($uploadfile);
	}

	public function addFileOptions(){
		$this->load->model('extension/portit_import');
		$json = array();
		$uploadfile = dirname(DIR_APPLICATION).'/script/options/' . basename($_FILES['file']['name']);
		return $this->model_extension_portit_import->addFile($uploadfile);
	}

	public function addFilePrices(){
		$this->load->model('extension/portit_import');
		$json = array();
		$uploadfile = dirname(DIR_APPLICATION).'/script/prices/' . basename($_FILES['file']['name']);
		return $this->model_extension_portit_import->addFile($uploadfile);
	}

	public function removeFile() {
		$this->load->model('extension/portit_import');
		$this->model_extension_portit_import->removeFile($_POST['filename'], $_POST['dir']);
		$json['success'] = 1;
		echo json_encode($json);
	}

	public function listOfMarket(){
		$this->load->model('extension/portit_import');
		echo json_encode( $this->model_extension_portit_import->getListOfMarket() );
	}

	public function listOfOptions(){
		$this->load->model('extension/portit_import');
		echo json_encode( $this->model_extension_portit_import->getListOfOptions() );
	}

	public function listOfPrices(){
		$this->load->model('extension/portit_import');
		echo json_encode( $this->model_extension_portit_import->getListOfPrices() );
	}

	public function processPriceses(){
		$this->load->model('extension/portit_import');
		if($this->model_extension_portit_import->script()){
			$this->session->data['success'] = 'Обработка прайсов успешно завершена!';
		} else {
			$this->session->data['error'] = 'Произошла ошибка!';
		}
		$this->response->redirect($this->url->link('extension/module/portit_import', 'token=' . $this->session->data['token'], true));
	}

	public function processAttribute() {
		$this->load->model('extension/portit_import');
		if($this->model_extension_portit_import->scriptAttribute()){
			$this->session->data['success'] = 'Файлы опций успешно обработаны!';
		} else {
			$this->session->data['error'] = 'Произошла ошибка!';
		}
		$this->response->redirect($this->url->link('extension/module/portit_import', 'token=' . $this->session->data['token'], true));
	}	

	public function clearAttribute(){
		$this->load->model('extension/portit_import');
		if($this->model_extension_portit_import->clearAttribute()){
			$this->session->data['success'] = 'Очистка атрибутов успешно завершина!';
		} else {
			$this->session->data['error'] = 'Произошла ошибка!';
		}
		$this->response->redirect($this->url->link('extension/module/portit_import', 'token=' . $this->session->data['token'], true));
	}

	public function clearOptions(){
		$this->load->model('extension/portit_import');
		if($this->model_extension_portit_import->clearOptions()){
			$this->session->data['success'] = 'Очистка опций успешно завершина!';
		} else {
			$this->session->data['error'] = 'Произошла ошибка!';
		}
		$this->response->redirect($this->url->link('extension/module/portit_import', 'token=' . $this->session->data['token'], true));
	}

	public function generateDone(){
		$this->load->model('extension/portit_import');
		if($this->model_extension_portit_import->generateDone()){
			$this->session->data['success'] = 'Генерация файлов успешно завершена!';
		} else {
			$this->session->data['error'] = 'Произошла ошибка!';	
		}
		$this->response->redirect($this->url->link('extension/module/portit_import', 'token=' . $this->session->data['token'], true));
	}
	/*
	 * 
	 * Check that user actions are authorized
	 * 
	 */
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/portit_import')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}

}
?>