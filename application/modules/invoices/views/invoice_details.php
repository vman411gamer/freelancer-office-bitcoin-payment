<section id="content">
	<section class="hbox stretch">
		
		<aside class="aside-md bg-white b-r hidden-print" id="subNav">
			<header class="dk header b-b">
			 <?php
                $username = $this -> tank_auth -> get_username();
                if($role == '1' OR $this -> applib -> allowed_module('add_invoices',$username)) { ?>
				<a href="<?=base_url()?>invoices/add" data-original-title="<?=lang('new_invoice')?>" data-toggle="tooltip" data-placement="bottom" class="btn btn-icon btn-default btn-sm pull-right"><i class="fa fa-plus"></i></a>
				<?php } ?>
				<p class="h4"><?=lang('all_invoices')?></p>
			</header>
			
			<section class="vbox">
				<section class="scrollable w-f">
					<div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
						<?=$this->load->view('sidebar/invoices',$invoices)?>
						</div>
						</section>
					</section>
				</aside>
				
				<aside>
					<section class="vbox">
						<header class="header bg-white b-b clearfix hidden-print">
							<div class="row m-t-sm">
								<div class="col-sm-8 m-b-xs">

								<div class="btn-group">
										<button class="btn btn-sm btn-<?=config_item('button_color')?> dropdown-toggle" data-toggle="dropdown">
										<?=lang('sort_by')?>
										<span class="caret"></span></button>
								<ul class="dropdown-menu">											
									<li><a href="<?=base_url()?><?=uri_string()?>/?order_by=reference_no&order=desc"><?=lang('reference_no')?></a></li>
									<li><a href="<?=base_url()?><?=uri_string()?>?order_by=due_date&order=desc"><?=lang('due_date')?></a></li>
									<li><a href="<?=base_url()?><?=uri_string()?>?order_by=tax&order=desc"><?=lang('tax')?></a></li>
									<li><a href="<?=base_url()?><?=uri_string()?>?order_by=status&order=desc"><?=lang('status')?></a></li>
									<li><a href="<?=base_url()?><?=uri_string()?>?order_by=date_sent&order=desc"><?=lang('date_sent')?></a></li>
									<li class="divider"></li>
									<li><a href="<?=base_url()?><?=uri_string()?>?order_by=date_saved&order=desc"><?=lang('created')?></a></li>
								</ul>
							</div>
									<?php
									if (!empty($invoice_details)) {
									foreach ($invoice_details as $key => $inv) { ?>
									<a href="#" class="btn btn-sm btn-default" onClick="window.print();"><i class="fa fa-print"></i></a>
							<?php if($role == '1' OR $this -> applib -> allowed_module('edit_all_invoices',$username)) { ?>

									<a href="<?=base_url()?>invoices/items/insert/<?=$inv->inv_id?>" title="<?=lang('item_quick_add')?>" class="btn btn-sm btn-<?=config_item('button_color')?>" data-toggle="ajaxModal">
									<i class="fa fa-list-alt text-white"></i> <?=lang('items')?></a>
									<?php } ?>
		<?php if($role == '1' OR $this -> applib -> allowed_module('pay_invoice_offline',$username)) { ?>	
									<?php
									if ($this->user_profile->invoice_payable($inv->inv_id) > 0) { ?>
									<a class="btn btn-sm btn-<?=config_item('button_color')?>" href="<?=base_url()?>invoices/pay/<?=$inv->inv_id?>" 
										title="<?=lang('add_payment')?>"><i class="fa fa-credit-card"></i> <?=lang('pay_invoice')?>
									</a>
		<?php } }else{ ?>
		<div class="btn-group hidden-nav-xs">
          <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown"><i class="fa fa-credit-card"></i> <?=lang('pay_invoice')?>
          <span class="caret">
          </span> </button>
          <ul class="dropdown-menu text-left">
            <li><a href="<?=base_url()?>paypal/pay/<?=$inv->inv_id?>" data-toggle="ajaxModal"
				title="<?=lang('via_paypal')?>"><?=lang('via_paypal')?></a></li>
            <li><a href="<?=base_url()?>stripepay/pay/<?=$inv->inv_id?>" data-toggle="ajaxModal" title="<?=lang('via_stripe')?>"><?=lang('via_stripe')?></a></li>
            <li><a href="<?=base_url()?>bitcoin/pay/<?=$inv->inv_id?>" data-toggle="ajaxModal" title="<?=lang('via_bitcoin')?>"><?=lang('via_bitcoin')?></a></li>
            
          </ul>
        </div>
        <?php } ?>
									
									
									<div class="btn-group">
										<button class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown">
										<?=lang('more_actions')?>
										<span class="caret"></span></button>
										<ul class="dropdown-menu">
		<?php
			if ($this -> applib -> invoice_payable($inv->inv_id) > 0) { ?>
							<?php if($role == '1' OR $this -> applib -> allowed_module('email_invoices',$username)) { ?>
											<li>
												<a href="<?=base_url()?>invoices/email/<?=$inv->inv_id?>" data-toggle="ajaxModal" title="<?=lang('email_invoice')?>"><?=lang('email_invoice')?></a>
											</li>
							<?php } if($role == '1' OR $this -> applib -> allowed_module('send_email_reminders',$username)) { ?>
											<li>
												<a href="<?=base_url()?>invoices/remind/<?=$inv->inv_id?>" data-toggle="ajaxModal" title="<?=lang('send_reminder')?>"><?=lang('send_reminder')?></a>
											</li>
											<?php } ?>
											<li><a href="<?=base_url()?>invoices/timeline/<?=$inv->inv_id?>"><?=lang('invoice_history')?></a></li>
		<?php } ?>
											<li class="divider"></li>
							<?php if($role == '1' OR $this -> applib -> allowed_module('edit_all_invoices',$username)) { ?>
											<li><a href="<?=base_url()?>invoices/edit/<?=$inv->inv_id?>"><?=lang('edit_invoice')?></a></li>
							<?php } ?>
							<?php if($role == '1' OR $this -> applib -> allowed_module('delete_invoices',$username)) { ?>
											<li><a href="<?=base_url()?>invoices/delete/<?=$inv->inv_id?>" data-toggle="ajaxModal"><?=lang('delete_invoice')?></a></li>
							<?php } ?>
										</ul>
									</div>

		<?php if($role == '1' AND $inv->recurring == 'Yes' OR $this -> applib -> allowed_module('edit_all_invoices',$username)) { ?>
<a class="btn btn-sm btn-danger" href="<?=base_url()?>invoices/stop_recur/<?=$inv->inv_id?>" 
										title="<?=lang('stop_recurring')?>" data-toggle="ajaxModal"><i class="fa fa-retweet"></i> <?=lang('stop_recurring')?>
									</a>
		<?php } ?>



									
								</div>
								<div class="col-sm-4 m-b-xs pull-right">
									<a href="<?=base_url()?>fopdf/invoice/<?=$inv->inv_id?>" class="btn btn-sm btn-dark pull-right">
									<i class="fa fa-file-pdf-o"></i> <?=lang('pdf')?></a>
								</div>
							</div> </header>
							
							<section class="scrollable wrapper">
								<!-- Start Display Details -->
								<?php
								if(!$this->session->flashdata('message')){
								if(strtotime($inv->due_date) < time() AND $payment_status != lang('fully_paid')){ ?>
								<div class="alert alert-info hidden-print">
									<button type="button" class="close" data-dismiss="alert">×</button> <i class="fa fa-warning"></i>
									<?=lang('invoice_overdue')?>
								</div>
								<?php } } ?>
								
								<section class="scrollable wrapper">
									<div class="row">
										<div class="col-xs-6">

										<img height="40" src="<?=base_url()?>resource/images/logos/<?=config_item('invoice_logo')?>" >
										</div>
										<div class="col-xs-6 text-right">
											<p class="h4"><?=$inv->reference_no?> 
											<?php
											if ($inv->recurring == 'Yes') { ?>
												<span class="label bg-danger"><i class="fa fa-retweet"></i> <?=$inv->recur_frequency?> </span>
											<?php } ?>
											</p>
											<p class="m-t m-b">
											<?=lang('invoice_date')?>: <strong><?=strftime(config_item('date_format'), strtotime($inv->date_saved));?></strong><br>
											<?=lang('due_date')?>: <strong><?=strftime(config_item('date_format'), strtotime($inv->due_date));?></strong><br>
										
											<?=lang('payment_status')?>: <span class="label bg-dark"><?=$payment_status?> </span><br>
											</p>
										</div>
									</div>


									<div class="well m-t">
                <div class="row">
                  <div class="col-xs-6">
                    <strong><?=lang('received_from')?>:</strong>
                    <h4><?=$this->config->item('company_name')?></h4>
                    <p><?=$this->config->item('company_address')?><br>
                    <?=$this->config->item('company_city')?><br>
                      <?=$this->config->item('company_country')?><br>
                      <?=lang('phone')?>: <?=$this->config->item('company_phone')?> <br>
                      <?=$this->config->item('company_vat')?><br>
                    </p>
                  </div>
                  <div class="col-xs-6">
                    <strong><?=lang('bill_to')?>:</strong>
                    <h4><?=ucfirst($this->applib->company_details($inv->client,'company_name'))?> <br></h4>
                    <p>
                      <?=ucfirst($this->applib->company_details($inv->client,'company_address'))?><br>
                       <?=ucfirst($this->applib->company_details($inv->client,'city'))?><br>
                      <?=ucfirst($this->applib->company_details($inv->client,'country'))?> <br>
                      <?=lang('phone')?> : <?=$this->applib->company_details($inv->client,'company_phone')?> <br>
                      <?=$this->applib->company_details($inv->client,'VAT')?> <br>                      
                    </p>
                  </div>
                </div>
              </div>
									
									<div class="line"></div>
									<table class="table"><thead>
										<tr>
											<th width="25%"><?=lang('item_name')?> </th>
											<th width="35%"><?=lang('description')?> </th>
											<th width="10%"><?=lang('qty')?> </th>
											<th width="15%"  class="text-right"><?=lang('unit_price')?> </th>
											<th width="15%" class="text-right"><?=lang('total')?> </th>
										</tr> </thead> <tbody>
										<?php
										if (!empty($invoice_items)) {
										foreach ($invoice_items as $key => $item) { ?>
										<tr>
											<td>
<?php
$item_name = $item->item_name ? $item->item_name : $item->item_desc;
 if($role == '1' OR $this -> applib -> allowed_module('edit_all_invoices',$username)) { ?>
<a href="<?=base_url()?>invoices/items/edit/<?=$item->item_id?>" data-toggle="ajaxModal"><?=$item_name?></a>
											<?php }else{ ?>
											<?=$item_name?>
											<?php } ?>
											</td>
											<td><small class="small text-muted"><?=$item->item_desc?></small> </td>
											<td><?=$item->quantity?></td>
											<td class="text-right"><?=number_format($item->unit_cost,2,$this->config->item('decimal_separator'),$this->config->item('thousand_separator'))?></td>
											<td class="text-right"><?=number_format($item->total_cost,2,$this->config->item('decimal_separator'),$this->config->item('thousand_separator'))?>

<?php if($role == '1' OR $this -> applib -> allowed_module('edit_all_invoices',$username)) { ?>
<a class="hidden-print" href="<?=base_url()?>invoices/items/delete/<?=$item->item_id?>/<?=$item->invoice_id?>" data-toggle="ajaxModal"><i class="fa fa-trash-o text-danger"></i></a>
<?php } ?>
</td>
											</tr>
											<?php } } ?>
	<?php if($role == '1' OR $this -> applib -> allowed_module('edit_all_invoices',$username)) { ?>
											<tr class="hidden-print">
												<?php
												$attributes = array('class' => 'bs-example form-horizontal');
												echo form_open(base_url().'invoices/items/add', $attributes); ?>
												<input type="hidden" name="invoice_id" value="<?=$inv->inv_id?>">
												<td> <input type="text" name="item_name"  placeholder="Item Name" class="form-control"></td>
												<td> <input type="text" name="item_desc" placeholder="Item Description" class="form-control"></td>
												<td><input type="text" name="quantity" placeholder="1" class="form-control"></td>
												<td><input type="text" name="unit_cost" required placeholder="50.56" class="form-control"></td>
												<td><button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check"></i> <?=lang('save')?></button></td>
											</form>
										</tr>
	<?php } ?>
										<tr>
											<td colspan="4" class="text-right no-border"><strong><?=lang('sub_total')?></strong></td>
											<td> <?=number_format($this -> applib -> calculate('invoice_cost',$inv->inv_id),2,config_item('decimal_separator'),config_item('thousand_separator'))?></td>
										</tr>
										<tr>
											<td colspan="4" class="text-right no-border">
											<strong><?=lang('tax')?> - <?php echo $inv->tax;?>%</strong></td>
											<td><?=number_format($this -> applib -> calculate('tax',$inv->inv_id),2,config_item('decimal_separator'),config_item('thousand_separator'))?> </td>
										</tr>
									<?php if($inv->discount > 0){ ?>
										<tr>
											<td colspan="4" class="text-right no-border">
											<strong><?=lang('discount')?> - <?php echo $inv->discount;?>%</strong></td>
											<td><?=number_format($this -> applib -> calculate('discount',$inv->inv_id),2,config_item('decimal_separator'),config_item('thousand_separator'))?> </td>
										</tr>
									<?php } ?>
										<tr>
											<td colspan="4" class="text-right no-border"><strong><?=lang('payment_made')?></strong></td>
											<td><?=number_format($this -> applib -> calculate('paid_amount',$inv->inv_id),2,config_item('decimal_separator'),config_item('thousand_separator'))?> </td>
										</tr>
										<tr>
											<td colspan="4" class="text-right no-border"><strong><?=lang('total')?></strong></td>
											<td><?=$inv->currency?> <?=number_format($this -> applib -> calculate('invoice_due',$inv->inv_id),2,config_item('decimal_separator'),config_item('thousand_separator'))?></td>
										</tr>
									</tbody>
								</table>
							</section>
							<p><blockquote><?=$inv->notes?></blockquote></p>
							<?php } } ?>
							<!-- End display details -->
						</section>
						</section> </aside> </section> <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen" data-target="#nav"></a> </section>
						<!-- end
