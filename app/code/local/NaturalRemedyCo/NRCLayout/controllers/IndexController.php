<?php
class NaturalRemedyCo_NRCLayout_IndexController extends Mage_Core_Controller_Front_Action {
	public function indexAction() {
		$date = date('Y-m-d');
		echo "Hello todays date is $date";
	}
}
?>