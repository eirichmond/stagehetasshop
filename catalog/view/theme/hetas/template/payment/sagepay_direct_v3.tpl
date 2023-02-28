<form class="form-horizontal">
    <?php if (!empty($existing_cards)) { ?>
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
            <table class="form">
                <tr>
                    <td><?php echo $entry_cc_choice; ?></td>
                    <td>
                        <select name="Token" class="form-control">
                            <?php foreach ($existing_cards as $existing_card) { ?>
                                <option value="<?php echo $existing_card['token']; ?>"><?php echo $text_card_type . ' ' . $existing_card['type']; ?>, <?php echo $text_card_digits . ' ' . $existing_card['digits']; ?>, <?php echo $text_card_expiry . ' ' . $existing_card['expiry']; ?></option>    
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo $entry_cc_cvv2; ?></td>
                    <td>
                        <input type="text" name="cc_cvv2" value="" placeholder="<?php echo $entry_cc_cvv2; ?>" id="input-cc-cvv2" class="form-control" />
                    </td>
                </tr>
            </table>
        </div>
        <div style="display: none;" id="card-new">
        <?php } else { ?>
            <div id="card-new">
        <?php } ?>
            <h2><?php echo $text_credit_card; ?></h2>
            <div class="content" id="payment">
                <table class="form">
                    <tr>
                        <td><?php echo $entry_cc_owner; ?></td>
                        <td>
                            <input type="text" name="cc_owner" value="" placeholder="<?php echo $entry_cc_owner; ?>" id="input-cc-owner" class="form-control" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_cc_type; ?></td>
                        <td>
                            <select name="cc_type" id="input-cc-type" class="form-control">
                                <?php foreach ($cards as $card) { ?>
                                    <option value="<?php echo $card['value']; ?>"><?php echo $card['text']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_cc_number; ?></td>
                        <td>
                            <input type="text" name="cc_number" value="" placeholder="<?php echo $entry_cc_number; ?>" id="input-cc-number" class="form-control" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_cc_start_date; ?></td>
                        <td>
                            <select name="cc_start_date_month" id="input-cc-start-date" class="form-control">
                                <?php foreach ($months as $month) { ?>
                                    <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
                                <?php } ?>
                            </select>
                            /
                            <select name="cc_start_date_year" class="form-control">
                                <?php foreach ($year_valid as $year) { ?>
                                    <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
                                <?php } ?>
                            </select>
                            <span class="help-block"><?php echo $help_start_date; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_cc_expire_date; ?></td>
                        <td>
                            <select name="cc_expire_date_month" id="input-cc-expire-date" class="form-control">
                                <?php foreach ($months as $month) { ?>
                                    <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
                                <?php } ?>
                            </select>
                            /
                            <select name="cc_expire_date_year" class="form-control">
                                <?php foreach ($year_expire as $year) { ?>
                                    <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_cc_cvv2; ?></td>
                        <td>
                            <input type="text" name="cc_cvv2" value="" placeholder="<?php echo $entry_cc_cvv2; ?>" id="input-cc-cvv2" class="form-control" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_cc_issue; ?></td>
                        <td>
                            <input type="text" name="cc_issue" value="" placeholder="<?php echo $entry_cc_issue; ?>" id="input-cc-issue" class="form-control" />
                            <span class="help-block"><?php echo $help_issue; ?></span>
                        </td>
                    </tr>
                    <?php if ($sagepay_direct_v3_card) { ?>
                        <tr>
                            <td><?php echo $entry_card_save; ?></td>
                            <td>
                                <input type="checkbox" name="CreateToken" value="1" id="input-cc-save"/>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
</form>
<div class="buttons">
  <div class="right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" />
  </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
<?php if (!empty($existing_cards)) { ?>
			$('#card-new input').prop('disabled', true);
			$('#card-new input').prop('disabled', true);
			$('#card-new select').prop('disabled', true);
<?php } ?>
	});
//</script>
<script type="text/javascript">
	$('input[name=\'CreateToken\']').bind('change', function() {
		if (this.value === '0') {
			$('#card-existing').show();
			$('#card-new').hide();
			$('#card-new input').prop('disabled', true);
			$('#card-new select').prop('disabled', true);
			$('#card-existing select').prop('disabled', false);
			$('#card-existing input').prop('disabled', false);
		} else {
			$('#card-existing').hide();
			$('#card-new').show();
			$('#card-new input').prop('disabled', false);
			$('#card-new select').prop('disabled', false);
			$('#card-existing select').prop('disabled', true);
			$('#card-existing input').prop('disabled', false);
		}
	});
//</script>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
		$.ajax({
			url: 'index.php?route=payment/sagepay_direct_v3/send',
			type: 'post',
			data: $('#card-new :input[type=\'text\']:enabled, #card-new select:enabled, #card-new :input[type=\'checkbox\']:checked:enabled, #payment select:enabled, #card-existing :input:enabled'),
			dataType: 'json',
			cache: false,
			beforeSend: function() {
				$('#button-confirm').button('loading');
			},
			complete: function() {
				$('#button-confirm').button('reset');
			},
			success: function(json) {
				if (json['ACSURL']) {
					$('#3dauth').remove();

					html = '<form action="' + json['ACSURL'] + '" method="post" id="3dauth">';
					html += '  <input type="hidden" name="MD" value="' + json['MD'] + '" />';
					html += '  <input type="hidden" name="PaReq" value="' + json['PaReq'] + '" />';
					html += '  <input type="hidden" name="TermUrl" value="' + json['TermUrl'] + '" />';
					html += '</form>';

					$('#payment').after(html);

					$('#3dauth').submit();
				}

				if (json['error']) {
					alert(json['error']);
				}

				if (json['redirect']) {
					location = json['redirect'];
				}
			}
		});
	});
//--></script>