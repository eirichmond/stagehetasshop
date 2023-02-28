<?php echo $header; ?>
<div id="content">
    <div class="breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
      <?php } ?>
    </div>
    <?php if ($error_warning) { ?>
        <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <div class="box">
        <div class="heading">
          <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
          <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
        </div>
        <div class="content">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                <table class="form">
                    <tr>
                        <td><span class="required">*</span> <?php echo $entry_vendor; ?></td>
                        <td>
                            <input type="text" name="sagepay_direct_v3_vendor" value="<?php echo $sagepay_direct_v3_vendor; ?>" placeholder="<?php echo $entry_vendor; ?>" id="input-vendor" class="form-control" />
                            <?php if ($error_vendor) { ?>
                                <span class="error"><?php echo $error_vendor; ?></span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_test; ?></td>
                        <td>
                            <select name="sagepay_direct_v3_test">
                                <?php if ($sagepay_direct_v3_test == 'sim') { ?>
                                    <option value="sim" selected="selected"><?php echo $text_sim; ?></option>
                                <?php } else { ?>
                                    <option value="sim"><?php echo $text_sim; ?></option>
                                <?php } ?>
                                <?php if ($sagepay_direct_v3_test == 'test') { ?>
                                    <option value="test" selected="selected"><?php echo $text_test; ?></option>
                                <?php } else { ?>
                                    <option value="test"><?php echo $text_test; ?></option>
                                <?php } ?>
                                <?php if ($sagepay_direct_v3_test == 'live') { ?>
                                    <option value="live" selected="selected"><?php echo $text_live; ?></option>
                                <?php } else { ?>
                                    <option value="live"><?php echo $text_live; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_transaction; ?><br /><span class="help"><?php echo $help_transaction ?></span></td>
                        <td>
                            <select name="sagepay_direct_v3_transaction" id="input-transaction" class="form-control">
                                <?php if ($sagepay_direct_v3_transaction == 'PAYMENT') { ?>
                                    <option value="PAYMENT" selected="selected"><?php echo $text_payment; ?></option>
                                <?php } else { ?>
                                    <option value="PAYMENT"><?php echo $text_payment; ?></option>
                                <?php } ?>
                                <?php if ($sagepay_direct_v3_transaction == 'DEFERRED') { ?>
                                    <option value="DEFERRED" selected="selected"><?php echo $text_defered; ?></option>
                                <?php } else { ?>
                                    <option value="DEFERRED"><?php echo $text_defered; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_total; ?><br /><span class="help"><?php echo $help_total; ?></span></td>
                        <td>
                            <input type="text" name="sagepay_direct_v3_total" value="<?php echo $sagepay_direct_v3_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_card; ?></td>
                        <td>
                            <select name="sagepay_direct_v3_card" id="input-card" class="form-control">
                                <?php if ($sagepay_direct_v3_card) { ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_order_status; ?></td>
                        <td>
                            <select name="sagepay_direct_v3_order_status_id" id="input-order-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                    <?php if ($order_status['order_status_id'] == $sagepay_direct_v3_order_status_id) { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_geo_zone; ?></td>
                        <td>
                            <select name="sagepay_direct_v3_geo_zone_id" id="input-geo-zone" class="form-control">
                                <option value="0"><?php echo $text_all_zones; ?></option>
                                <?php foreach ($geo_zones as $geo_zone) { ?>
                                    <?php if ($geo_zone['geo_zone_id'] == $sagepay_direct_v3_geo_zone_id) { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_debug; ?><br /><span class="help"><?php echo $help_debug; ?></span></td>
                        <td>
                            <select name="sagepay_direct_v3_debug" id="input-debug" class="form-control">
                                <?php if ($sagepay_direct_v3_debug) { ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_status; ?></td>
                        <td>
                            <select name="sagepay_direct_v3_status" id="input-status" class="form-control">
                                <?php if ($sagepay_direct_v3_status) { ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo $entry_sort_order; ?></td>
                        <td>
                            <input type="text" name="sagepay_direct_v3_sort_order" value="<?php echo $sagepay_direct_v3_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</div>
<?php echo $footer; ?> 