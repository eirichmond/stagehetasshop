<div id="payment">
	<?php if (!empty($cards)) { ?>
		<div class="radio">
			<label>
				<input type="radio" name="CreateToken" value="0" checked="checked"/>
				<?php echo $entry_card_existing; ?></label>
		</div>
		<div class="radio">
			<label>
				<input type="radio" name="CreateToken" value=""/>
				<?php echo $entry_card_new; ?></label>
		</div>
		<div id="card-existing">
			<select name="Token" class="form-control" >
				<?php foreach ($cards as $card) { ?>
					<option value="<?php echo $card['token']; ?>"><?php echo $text_card_type . ' ' . $card['type']; ?>, <?php echo $text_card_digits . ' ' . $card['digits']; ?>, <?php echo $text_card_expiry . ' ' . $card['expiry']; ?></option>    
				<?php } ?>
			</select>
		</div>
		<label style="display: none" id="card-save" >		
			<input type="checkbox" name="CreateToken" value="1" disabled/>
			<?php echo $entry_card_save; ?>
		</label>
	<?php } elseif($sagepay_server_v3_card) { ?>
		<div class="radio">
			<label>
				<input type="radio" name="CreateToken" value="" checked="checked"/>
				<?php echo $entry_card_new; ?></label>
		</div>
		<label id="card-save">		
			<input type="checkbox" name="CreateToken" value="1"/>
			<?php echo $entry_card_save; ?>
		</label>
	<?php }  ?>
    <div class="buttons">
        <div class="right">
            <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="button" />
        </div>
    </div>
</div>  
<script type="text/javascript">
	$('input[name=\'CreateToken\']').bind('change', function() {
		if (this.value === '0') {
			$('#card-existing').show();
			$('#card-save').hide();
			$('.form-control').prop('disabled', false);
			$('#card-save :input').prop('disabled', true);
		} else {
			$('#card-existing').hide();
			$('#card-save').show();
			$('.form-control').prop('disabled', true);
			$('#card-save :input').prop('disabled', false);
		}
	});
//</script>
<script type="text/javascript"><!--
	$('#button-confirm').bind('click', function() {
		$.ajax({
			url: 'index.php?route=payment/sagepay_server_v3/send',
			type: 'post',
			data: $('#card-existing :input:checked, #card-save :input:enabled, #payment select:enabled'),
			dataType: 'json',
			cache: false,
			beforeSend: function() {
				$('#button-confirm').button('loading');
			},
			complete: function() {
				$('#button-confirm').button('reset');
			},
			success: function(json) {
				// if success
				if (json['redirect']) {
					html = '<form action="' + json['redirect'] + '" method="post" id="redirect">';
					html += '  <input type="hidden" name="Status" value="' + json['Status'] + '" />';
					html += '  <input type="hidden" name="StatusDetail" value="' + json['StatusDetail'] + '" />';

					html += '</form>';

					$('#payment').after(html);

					$('#redirect').submit();
				}

				// if error
				if (json['error']) {
					$('#payment').before('<div id="sagepay_message_error" class="alert alert-warning"><i class="fa fa-info-circle"></i> ' + json['error'] + '</div>');
				}
			}
		});
	});
//--></script>