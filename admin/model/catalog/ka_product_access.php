<?php
/*
	Project : Restricted Product Access
	Author  : karapuz <support@ka-station.com>

	Version : 2 ($Revision: 12 $)

*/
class ModelCatalogKaProductAccess extends Model {

	public function isInstalled() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE code = 'ka_product_access'");
		if (empty($query->num_rows)) {
			return false;
		}
		return true;
	}
}
?>