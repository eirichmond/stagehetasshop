<?php
/*
  Project: Ka Extensions
  Author : karapuz <support@ka-station.com>

  Version: 1.0 ($Revision: 14 $)

*/

/*
PARAMETERS:
  $options    - array of options like this one:
                  $options = arrray(
                    '<key>' => '<value>'
                  )
  $value      - selected option
  $name       - name of the select element;
  $first_line - insert y/n the first empty line to the select 
  
  Example of use:
  
  <?php $this->showTemplate('ka_product_warranty/select.tpl', 
    array('name' => 'warranty_period', 
          'value' => $product_warranty['warranty_period'], 
          'options' => $warranty_periods)); 
  ?> 
*/

?>
<?php if (!empty($name)) { ?>

<select name="<?php echo $name; ?>" <?php if (!empty($extra)) { echo $extra; } ?>>
<?php if (isset($first_line)) { ?>
  <option value="">&nbsp;</option>
<?php } ?>   
<?php foreach ($options as $k => $v) { ?>
  <option value="<?php echo $k;?>" <?php if ($value == $k) { ?> selected="selected" <?php } ?>><?php echo $v;?></option>
<?php } ?>
</select>

<?php } else { 

  if (isset($value) && isset($options[$value])) {
    echo $options[$value];
  } else {
    echo "Undefined";
  }
}

?>