<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 *
 * @package	Freelancer Office
 */
class bitcoin extends MX_Controller {

	function __construct()
	{
		parent::__construct();		
		$this->load->library('tank_auth');
		$this->load->model('bitcoinpay', 'AppPay');
		$this->invoice_table = 'invoices';
		$this->clients_table = 'companies';
	}
	
	function curl_get_contents($url)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}
	function round_up ( $value, $precision ) 
	{ 
		$pow = pow ( 10, $precision ); 
		return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow;
	}

	function pay($invoice = NULL)
	{
		$userid = $this->tank_auth->get_user_id();
		$reference_no = $this -> applib->get_any_field('invoices',array('inv_id'=>$invoice),'reference_no');
		$currency = $this -> applib->get_any_field('invoices',array('inv_id'=>$invoice),'currency');

		$invoice_due = $this -> applib -> calculate('invoice_due',$invoice);
		if ($invoice_due <= 0) {  $invoice_due = 0.00;	}

		$data['invoice_info'] = array('item_name'=> $reference_no, 
										'item_number' => $invoice,
										'currency' => $currency,
										'amount' => $invoice_due) ;
		$data['bitcoin'] = TRUE;
		$urls = "https://blockchain.info/tobtc?currency=".$data['invoice_info']['currency']."&value=".$data['invoice_info']['amount'];
		$btc_amount = $this->curl_get_contents($urls);
		$data['btc_amount'] = $this->round_up($btc_amount, 3);
		$blockchain_api = "https://blockchain.info/api/receive?api_code=7a4d9254-81ce-496d-98ce-8408082915c9&method=create&cors=true&format=plain&address=".$this->config->item('bitcoin_address')."&shared=false&callback=".base_url()."bitcoin%2Fsuccess%3Fusdamount%3D".$data['invoice_info']['amount']."%26invoicename%3D".$data['invoice_info']['item_name']."%26btcamount%3D".$btc_amount."%26invoice%3D".$data['invoice_info']['item_number']."%26client%3D".$userid;
		$recieve_api = $this->curl_get_contents($blockchain_api);
		
		$decoded = json_decode($recieve_api);
		$data['btc_address'] = $decoded->{'input_address'};
		
		$this->load->view('form',$data);
	}
	function cancel()
	{
		$this->session->set_flashdata('response_status', 'error');
		$this->session->set_flashdata('message', 'Bitcoin payment canceled.');
		redirect('clients');
	}
	
	function success(){
		echo "*ok*";
		function round_up ( $value, $precision ) { 
			$pow = pow ( 10, $precision ); 
			return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow;
		}
		$transactionid = $_GET['transaction_hash'];
		$invoiceid = $_GET['invoice'];
		$invoicename = $_GET['invoicename'];
		$usdamount = $_GET['usdamount'];
		$btcamount = $_GET['btcamount'];
		$client = $_GET['client'];
		$amountsentsatoshi = $_GET['value'];
		$amountsent = $amountsentsatoshi / 100000000;
		$client_username = $this->user_profile->get_user_details($client,'username'); //get client username
		$client_email = $this->user_profile->get_user_details($client,'email'); //get client email
		$ratio = $amountsent / $btcamount;
		$paid = $usdamount * $ratio;
		$paid = round_up($paid, 2);
		
		$p_info = array(
			'invoice' => $invoiceid,
			'paid_by' => $client,
			'payment_method' => '1',
			'amount' => $paid,
			'trans_id' => $transactionid,
			'notes' => 'Amount in BTC: '.$amountsent,
			'month_paid' => date('m'),
			'year_paid' => date('Y'),
		);
		$this->db->insert('payments',$p_info); // insert to payments
		$activity = lang('activity_payment_of').$this->user_profile->get_invoice_details($invoiceid,'currency').' '.$paid.lang('activity_payment_recieved_and_applied').$invoicename;

		$this->_log_activity($invoiceid,$activity,$icon = 'fa-btc',$client); //log activity
				
		$this->_notifyme($client_email,$client_username,$invoicename);
		
	}
	function _notifyme($client_email,$client_username,$invoice_ref)
	{
   
            $data['client_username'] = $client_username;
            $data['invoice_ref'] = $invoice_ref;

            $email_msg = $this->load->view('InvoicePaid',$data,TRUE);
            $email_subject = '['.$this->config->item('company_name').' ] Purchase Confirmation';
            $this->email->from($this->config->item('company_email'), $this->config->item('company_name'));
            $this->email->to($client_email);
            $this->email->reply_to($this->config->item('company_email'), $this->config->item('company_name'));
            $this->email->subject($email_subject);

            $this->email->message_plain($email_msg);
            $this->email->message_html($email_msg);

            $this->email->send();
		
	}
	
	
       function _log_activity($invoice_id,$activity,$icon,$user)
       {
            $this->db->set('module', 'invoices');
            $this->db->set('module_field_id', $invoice_id);
            $this->db->set('user', $user);
            $this->db->set('activity', $activity);
            $this->db->set('icon', $icon);
            $this->db->insert('activities'); 
       	
       }
}


////end 
