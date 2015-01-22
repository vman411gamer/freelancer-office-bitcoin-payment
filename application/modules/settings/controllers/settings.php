<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
**********************************************************************************
* Copyright: gitbench 2014
* CodeCanyon Project: http://codecanyon.net/item/freelancer-office/8870728
* Package Date: 2014-09-24 09:33:11 
***********************************************************************************
*/

// Includes all users operations
include APPPATH.'/libraries/Requests.php';
class Settings extends MX_Controller {

	function __construct()
	{
		parent::__construct();
		$this -> load -> library(array('tank_auth','form_validation'));

		$this -> user = $this->tank_auth->get_user_id();
		$this -> username = $this -> tank_auth -> get_username(); // Set username
		if (!$this -> user) {
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message', lang('access_denied'));
			redirect('auth/login');
		}
		
		Requests::register_autoloader();
		$this -> auth_key = config_item('api_key'); // Set our API KEY
		
		$this -> load -> module('layouts');
		$this->load->config('rest');
		$this -> load -> library('template');
		$this -> template -> title(lang('settings').' - '.config_item('company_name'). ' '.config_item('version'));
		$this -> page = lang('settings');
		$this -> load ->model('settings_model','settings');	
		$this->general_setting = '?settings=general';
		$this->invoice_setting = '?settings=invoice';
		$this->system_setting = '?settings=system';
	}

	function index()
	{
		$settings = $this->input->get('settings', TRUE)?$this->input->get('settings', TRUE):'general';
		$data['page'] = $this -> page;	
		$data['form'] = TRUE;
		$data['editor'] = TRUE;
		$data['fuelux'] = TRUE;
		$data['datatables'] = TRUE;
		$data['countries'] = $this -> settings -> countries();
		$data['load_setting'] = $settings;

		$this->template
		->set_layout('users')
		->build('settings',isset($data) ? $data : NULL);
	}

	function templates(){
		if ($_POST) {
			$this->_demo_mode('settings/?settings=templates');
			$data = array(
			              'template_body' => $this -> input -> post('email_template'));			
			 $this -> db -> where(array('email_group' => $_POST['email_group'])) -> update('email_templates',$data);
			 $return_url = $_POST['return_url'];

			 $this->session->set_flashdata('response_status', 'success');
			 $this->session->set_flashdata('message', lang('settings_updated_successfully'));
			redirect($return_url);
		}else{
			$this->index();
		}
	}

	function departments(){
		if ($_POST) {
			$settings = $_POST['settings'];
			unset($_POST['settings']);

			 $this -> db -> insert('departments',$_POST);

			 $this->session->set_flashdata('response_status', 'success');
			 $this->session->set_flashdata('message', lang('department_added_successfully'));
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->index();
		}
	}

	function add_custom_field(){
		if ($_POST) {
			if (isset($_POST['targetdept'])) {
				// select department and redirect to creating field
				$this -> applib -> redirect_to('settings/?settings=fields&dept='.$_POST['targetdept'],'success','Department selected');
			}else{
			$_POST['uniqid'] = $this -> _GenerateUniqueFieldValue();

			$this -> db -> insert('fields',$_POST);

			$this -> applib -> redirect_to('settings/?settings=fields&dept='.$_POST['deptid'],'success','Custom field added');
				// Insert to database additional fields

			}

		}else{

		}
	}

	function edit_custom_field($field = NULL){
		if ($_POST) {
			if(isset($_POST['delete_field']) AND $_POST['delete_field'] == 'on'){
				$this -> db -> where('id',$_POST['id']) -> delete('fields');
				$this -> applib -> redirect_to($_SERVER['HTTP_REFERER'],'success',lang('custom_field_deleted'));
			}else{
				$this -> db -> where('id',$_POST['id']) -> update('fields',$_POST);
				$this -> applib -> redirect_to($_SERVER['HTTP_REFERER'],'success',lang('custom_field_updated'));
			}
		}else{
		$data['field_info'] = $this -> db -> where(array('id'=>$field)) -> get('fields') -> result();
		$this->load->view('fields/modal_edit_field',isset($data) ? $data : NULL);
		}
	}

	

	function edit_dept($deptid = NULL){
		if ($_POST) {
			if(isset($_POST['delete_dept']) AND $_POST['delete_dept'] == 'on'){
				$this -> db -> where('deptid',$_POST['deptid']) -> delete('departments');
				$this -> applib -> redirect_to($_SERVER['HTTP_REFERER'],'success',lang('department_deleted'));
			}else{
				$this -> db -> where('deptid',$_POST['deptid']) -> update('departments',$_POST);
				$this -> applib -> redirect_to($_SERVER['HTTP_REFERER'],'success',lang('department_updated'));
			}
		}else{
		$data['deptid'] = $deptid;
		$data['dept_info'] = $this -> db -> where(array('deptid'=>$deptid)) -> get('departments') -> result();
		$this->load->view('modal_edit_department',isset($data) ? $data : NULL);
		}
	}

	function permissions(){
		if ($_POST) {
			 $permissions = json_encode($_POST);
			 $data = array(
			              'allowed_modules' => $permissions);			
			 $this -> db -> where(array('user_id' => $_POST['user_id'])) -> update('account_details',$data);

			 $this->session->set_flashdata('response_status', 'success');
			 $this->session->set_flashdata('message', lang('settings_updated_successfully'));
			redirect(base_url().'settings/?settings=permissions&staff='.$_POST['user_id']);
		}else{
			$this->index();
		}
	}

	function update(){
		if ($_POST) {
			$this->_demo_mode('settings');
			 switch ($_POST['settings'])
			        {
			            case 'general':			                
			                if(file_exists($_FILES['userfile']['tmp_name']) || is_uploaded_file($_FILES['userfile']['tmp_name'])) {
			                	$this->upload_logo($_POST);
			            	}
			            	$this->_update_general_settings($this->general_setting);
			                break;
			            case 'email':
			                $this->_update_email_settings('email');
			                break;
			            case 'payments':
			                $this->_update_payment_settings('payment');
			                break;
			            case 'system':
			                $this->_update_system_settings('system');
			                break;
			            case 'invoice':
			            	if(file_exists($_FILES['userfile']['tmp_name']) || is_uploaded_file($_FILES['userfile']['tmp_name'])) {
			                	$this->upload_invoice_logo($_POST);
			            	}
			                $this->_update_invoice_settings('invoice');
			                break;
			        }

		}else{
			$this->index();
		}
	}

	function _update_general_settings($setting){
			$this->_demo_mode('settings/'.$this->general_setting);

		$this->form_validation->set_rules('company_name', 'Company Name', 'required');
		$this->form_validation->set_rules('company_address', 'Company Address', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message', lang('settings_update_failed'));
			redirect('settings/'.$this->general_setting);
		}else{
			foreach ($_POST as $key => $value) {
				$data = array('value' => $value); 
				$this->db->where('config_key', $key)->update('config', $data); 
			}
			$this->session->set_flashdata('response_status', 'success');
			$this->session->set_flashdata('message', lang('settings_updated_successfully'));
			redirect('settings/'.$this->general_setting);
		}
		
	}

	function _update_system_settings($setting){
		$this->_demo_mode('settings/'.$this->system_setting);

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
		$this->form_validation->set_rules('base_url', 'Base URL', 'required');
		$this->form_validation->set_rules('language', 'Default Language', 'required');
		$this->form_validation->set_rules('file_max_size', 'File Max Size', 'required');		
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message', lang('settings_update_failed'));
			$_POST = '';
			$this->index();
		}else{
		foreach ($_POST as $key => $value) {
				$data = array('value' => $value); 
				$this->db->where('config_key', $key)->update('config', $data); 
			}

			$this->session->set_flashdata('response_status', 'success');
			$this->session->set_flashdata('message', lang('settings_updated_successfully'));
			redirect('settings/'.$this->system_setting);
		}
		
	}
	function _update_invoice_settings($setting){
			$this->_demo_mode('settings/'.$this->invoice_setting);

		$this->form_validation->set_rules('invoice_color', 'Invoice Color', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message', lang('settings_update_failed'));
			redirect('settings/'.$this->invoice_setting);
		}else{
			foreach ($_POST as $key => $value) {
				$data = array('value' => $value); 
				$this->db->where('config_key', $key)->update('config', $data); 
			}
			$this->session->set_flashdata('response_status', 'success');
			$this->session->set_flashdata('message', lang('settings_updated_successfully'));
			redirect('settings/'.$this->invoice_setting);
		}
		
	}

	private function _update_email_settings($setting){
		$this->_demo_mode('settings/?settings=email');

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
		$this->form_validation->set_rules('company_email', 'Company Email', 'required');
		$this->form_validation->set_rules('protocol', 'Email Protocol', 'required');		
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message', lang('settings_update_failed'));
			$_POST = '';
			$this->index();
		}else{
			$this->load->library('encrypt');
			$raw_smtp_pass =  $this->input->post('smtp_pass');
			$smtp_pass = $this->encrypt->encode($raw_smtp_pass);
			foreach ($_POST as $key => $value) {
				$data = array('value' => $value); 
				$this->db->where('config_key', $key)->update('config', $data); 
			}
		$data = array('value' => $smtp_pass); $this->db->where('config_key', 'smtp_pass')->update('config', $data); 
		

			$this->session->set_flashdata('response_status', 'success');
			$this->session->set_flashdata('message', lang('settings_updated_successfully'));
			redirect('settings/?settings=email');
		}
		
	}
	function _update_payment_settings(){
		if ($this->input->post()) {
			$this->_demo_mode('settings');

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
		$this->form_validation->set_rules('default_currency', 'Default Currency', 'required');
		$this->form_validation->set_rules('default_currency_symbol', 'Default Currency Symbol', 'required');	
		$this->form_validation->set_rules('paypal_email', 'Paypal Email', 'required');		
		$this->form_validation->set_rules('paypal_cancel_url', 'Paypal Cancel', 'required');	
		$this->form_validation->set_rules('paypal_ipn_url', 'Paypal IPN', 'required');	
		$this->form_validation->set_rules('paypal_success_url', 'Paypal Success', 'required');
		$this->form_validation->set_rules('bitcoin_address', 'Your Bitcoin Address', 'required');
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message', lang('settings_update_failed'));
			$_POST = '';
			$this->index();
		}else{
			foreach ($_POST as $key => $value) {
				$data = array('value' => $value); 
				$this->db->where('config_key', $key)->update('config', $data); 
			}


			$this->session->set_flashdata('response_status', 'success');
			$this->session->set_flashdata('message', lang('settings_updated_successfully'));
			redirect('settings/?settings=payments');
		}
	}else{
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message', lang('settings_update_failed'));
			redirect('settings/?settings=payments');
	}
		
	}

	function update_email_templates(){
		if ($this->input->post()) {
			$this->_demo_mode('settings/?settings=email');

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<span style="color:red">', '</span><br>');
		$this->form_validation->set_rules('email_estimate_message', 'Estimate Message', 'required');
		$this->form_validation->set_rules('email_invoice_message', 'Invoice Message', 'required');	
		$this->form_validation->set_rules('reminder_message', 'Reminder Message', 'required');	
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message', lang('settings_update_failed'));
			$_POST = '';
			$this->update('email');
		}else{
			foreach ($_POST as $key => $value) {
				$data = array('value' => $value); 
				$this->db->where('config_key', $key)->update('config', $data); 
			}

			$this->session->set_flashdata('response_status', 'success');
			$this->session->set_flashdata('message', lang('settings_updated_successfully'));
			redirect('settings/update/email');
		}
	}else{
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message', lang('settings_update_failed'));
			redirect('settings/update/email');
	}
		
	}
	function upload_logo($files){
		$this->_demo_mode('settings/?settings=general');

		if ($files) {
						$config['upload_path']   = './resource/images/';
            			$config['allowed_types'] = 'jpg|jpeg|png';
            			$config['max_width']  = '300';
            			$config['max_height']  = '300';
            			$config['remove_spaces'] = TRUE;
            			$config['file_name']  = 'logo';
            			$config['overwrite']  = TRUE;
            			$config['max_size']      = '300';
            			$this->load->library('upload', $config);
						if ($this->upload->do_upload())
						{
							$data = $this->upload->data();
							$file_name = $data['file_name'];
							$data = array(
								'value' => $file_name);
							$this->db->where('config_key', 'company_logo')->update('config', $data); 
							return TRUE;
						}else{
							$this->session->set_flashdata('response_status', 'error');
							$this->session->set_flashdata('message', lang('logo_upload_error'));
							redirect('settings/'.$this->general_setting);
						}
			}else{
							return FALSE;
			}
	}
	function upload_invoice_logo($files){
		$this->_demo_mode('settings/?settings=invoice');

		if ($files) {
						$config['upload_path']   = './resource/images/logos/';
            			$config['allowed_types'] = 'jpg|jpeg|png';
            			$config['remove_spaces'] = TRUE;
            			$config['file_name']  = 'invoice_logo';
            			$config['overwrite']  = TRUE;
            			$this->load->library('upload', $config);
						if ($this->upload->do_upload())
						{
							$data = $this->upload->data();
							$file_name = $data['file_name'];
							$data = array(
								'value' => $file_name);
							$this->db->where('config_key', 'invoice_logo')->update('config', $data); 
							return TRUE;
						}else{
							$this->session->set_flashdata('response_status', 'error');
							$this->session->set_flashdata('message', lang('logo_upload_error'));
							redirect('settings/'.$this->invoice_setting);
						}
			}else{
							return FALSE;
			}
	}


	function _GenerateUniqueFieldValue()
	{
		$uniqid = uniqid('f');
		// Id should start with an character other than digit

		$this -> db -> where('uniqid', $uniqid) -> get('fields');

		if ($this -> db -> affected_rows() > 0)
		{
			$this -> GetUniqueFieldValue();
			// Recursion
		}
		else
		{
			return $uniqid;
		}

	}

	function database()
	{
		if ($this->config->item('demo_mode') == 'FALSE') { 
		$this->load->dbutil();
		$prefs = array(
                'format'      => 'txt',             // gzip, zip, txt
                'filename'    => 'latest-freelancerbackup.sql',    // File name - NEEDED ONLY WITH ZIP FILES
                'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
                'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
                'newline'     => "\n"               // Newline character used in backup file
              );
			$backup =& $this->dbutil->backup($prefs);
			$this->load->helper('file');
			write_file('resource/database.backup/latest-freelancerbackup.sql', $backup); 
			$this->load->helper('download');
			force_download('latest-freelancerbackup.sql', $backup);
		}else{
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message',lang('demo_warning'));
			redirect('settings');
		}
	}
	function _demo_mode($redirect_url){
		if ($this->config->item('demo_mode') == 'TRUE') {
			$this->session->set_flashdata('response_status', 'error');
			$this->session->set_flashdata('message', lang('demo_warning'));
			redirect($redirect_url);
		}
	}
	
}

/* End of file settings.php */
