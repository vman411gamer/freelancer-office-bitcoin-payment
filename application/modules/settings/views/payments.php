
	 <div class="row">
	<!-- Start Form -->
	<div class="col-lg-12">

	<section class="panel panel-default">
	<header class="panel-heading font-bold"><i class="fa fa-cogs"></i> <?=lang('payment_settings')?></header>
	<div class="panel-body">
	  <?php     
$attributes = array('class' => 'bs-example form-horizontal');
echo form_open('settings/update', $attributes); ?>
<input type="hidden" name="settings" value="<?=$load_setting?>">
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('paypal_email')?> <span class="text-danger">*</span></label> 
				<div class="col-lg-7">
					<input type="email" name="paypal_email" class="form-control" value="<?=$this->config->item('paypal_email')?>" required>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('paypal_ipn_url')?> </label>
				<div class="col-lg-7">
					<input type="text" class="form-control" data-toggle="tooltip" data-placement="top" data-original-title="<?=lang('change_if_necessary')?>" value="<?=$this->config->item('paypal_ipn_url')?>" name="paypal_ipn_url">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('paypal_cancel_url')?> <span class="text-danger">*</span></label>
				<div class="col-lg-7">
					<input type="text" class="form-control" data-toggle="tooltip" data-placement="top" data-original-title="<?=lang('change_if_necessary')?>"  value="<?=$this->config->item('paypal_cancel_url')?>" name="paypal_cancel_url">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('paypal_success_url')?></label>
				<div class="col-lg-7">
					<input type="text" class="form-control" data-toggle="tooltip" data-placement="top" data-original-title="<?=lang('change_if_necessary')?>"  value="<?=$this->config->item('paypal_success_url')?>" name="paypal_success_url">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('bitcoin_address')?></label>
				<div class="col-lg-7">
					<input type="text" class="form-control" value="<?=$this->config->item('bitcoin_address')?>" name="bitcoin_address">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('stripe_private_key')?></label>
				<div class="col-lg-7">
					<input type="text" class="form-control" value="<?=$this->config->item('stripe_private_key')?>" name="stripe_private_key">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('stripe_public_key')?></label>
				<div class="col-lg-7">
					<input type="text" class="form-control" value="<?=$this->config->item('stripe_public_key')?>" name="stripe_public_key">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('default_currency')?></label>
				<div class="col-lg-7">
					<input type="text" class="form-control" value="<?=$this->config->item('default_currency')?>" name="default_currency">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('default_currency_symbol')?></label>
				<div class="col-lg-7">
					<input type="text" class="form-control" value="<?=$this->config->item('default_currency_symbol')?>" name="default_currency_symbol">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('default_tax')?></label>
				<div class="col-lg-7">
					<input type="text" class="form-control" value="<?=$this->config->item('default_tax')?>" name="default_tax">
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-3 control-label"><?=lang('paypal_live')?></label>
				<div class="col-lg-7">
				<select name="paypal_live">
				<option value="<?=config_item('paypal_live')?>"><?=lang('use_current')?></option>
						<option value="FALSE">FALSE</option>
						<option value="TRUE">TRUE</option>
					</select> 
				</div>
			</div>

			
			
			
			<div class="form-group">
				<div class="col-lg-offset-2 col-lg-10">
				<button type="submit" class="btn btn-sm btn-primary"><?=lang('save_changes')?></button>
				</div>
			</div>
		</form>

		


	</div> </section>
</div>
<!-- End Form -->




</div>

