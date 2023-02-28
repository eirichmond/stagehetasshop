<?php
/*
  Project : Restricted Product Access
  Author  : karapuz <support@ka-station.com>

  Version : 2 ($Revision: 18 $)

*/
?>
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
    <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a></div>
  </div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table id="module" class="list">
        <thead> 
          <tr>
            <td class="left">Setting</td>
            <td>Value</td>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="left" width="70%">Version          
            </td>
            <td class="left" width="30%">
              <?php echo $extension_version; ?>
            </td>
          </tr>
          
          <tr>
            <td class="left">Display the login page instead of 'Access denied' for non-logged in users</span></td>
            <td class="left">
              <input type="checkbox" name="ka_pa_show_login_page" value="Y" <?php if ($ka_pa_show_login_page == 'Y') { ?> checked="checked" <?php } ?> />
            </td>
          </tr>
          
          <tr>
            <td class="left">Do not store a state of the propagation checkbox for categories<span class="help">increases saving the category data</span></td>
            <td class="left">
              <input type="checkbox" name="ka_pa_no_propagation" value="Y" <?php if ($ka_pa_no_propagation == 'Y') { ?> checked="checked" <?php } ?> />
            </td>
          </tr>
          
          <tr>
            <td class="left">Visibility of Restricted Products
            </td>
            <td class="left">
              <?php $this->showTemplate('ka_extensions/select.tpl', 
                array('name' => 'ka_pa_visibility',
                      'value' => $ka_pa_visibility, 
                      'options' => $product_visibilities)); 
              ?>
            </td>
          </tr>
          
          <tr>
            <td class="left">Page URL for purchasing the customer group
            <span class="help">The url will be visible to customers.<br />
            The <a target="_blank" href="http://www.ka-station.com/ka-extensions/paid_customer_groups">'Paid Customer Groups'</a> 
            extension can be used for selling customer groups online</span>
            </td>            
            <td class="left">
              <input type="text" name="ka_purchase_url" value="<?php echo $ka_purchase_url; ?>" />
              <?php if (!empty($error['ka_purchase_url'])) { ?>
                <span class="error"><?php echo $error['ka_purchase_url'];?></span>
              <?php } ?>
            </td>
          </tr>
          
        </tbody>
      </table>
    </form>
  </div>
</div>

<script type="text/javascript"><!--

//--></script>

<?php echo $footer; ?>