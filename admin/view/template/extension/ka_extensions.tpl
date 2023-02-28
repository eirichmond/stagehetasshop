<?php
/*
  Project : Ka Extensions
  Author  : karapuz <support@ka-station.com>

  Version : 2 ($Revision: 36 $)
  
*/
?>

<?php echo $header; ?>

<style>
#service_line {
  width: 100%;
  background-color: #EFEFEF;
}

.adv {
  background-color: white;
  color: black;
  font-weight: 12px;
}

</style>

<div id="content">

  <?php echo $ka_top; ?>

  <div class="box">
    <div class="heading">
      <h1><img src="view/image/module.png" alt="" /> <?php echo $heading_title; ?></h1>
    </div>

    <div id="service_line">
    <table>
      <tr>
        <td><b>Ka-Extensions Version</b>: <?php echo $extension_version; ?>&nbsp;&nbsp;&nbsp;</td>
        <td><b>Contact Us</b>: <a href="mailto:support@ka-station.com">via email</a>&nbsp;&nbsp;&nbsp;</td>
        <td><a href="https://www.ka-station.com/index.php?route=information/contact" target="_blank">via secure form at www.ka-station.com</a>&nbsp;&nbsp;&nbsp;</td>
      </tr>
    </table>
    </div>
    
    <div class="content">
      <table class="list">
        <thead>
          <tr>
            <td class="left"><?php echo $column_name; ?></td>
            <td class="right"><?php echo $column_action; ?></td>
          </tr>
        </thead>
        <tbody>
          <?php if ($extensions) { ?>
          <?php foreach ($extensions as $extension) { ?>
          <tr>
            <td class="left"><?php echo $extension['name']; ?></td>
            <td class="right"><?php foreach ($extension['action'] as $action) { ?>
              [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
              <?php } ?></td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="center" colspan="8"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <span class="adv">Find more useful extensions at our official <a target="_blank" href="http://www.ka-station.com/ka-extensions?ref=215">www.ka-station.com</a> site</span>
    </div>
  </div>
</div>
<?php echo $footer; ?>