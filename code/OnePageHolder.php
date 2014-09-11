<?php

class OnePageHolder extends Page {

    private static $singular_name = 'One Page Holder';
    
    private static $plural_name = 'One Page Holders';

    
    public function getCMSFields() {
		$fields = parent::getCMSFields();

		return $fields;
	}
}

class OnePageHolder_Controller extends Page_Controller {


}