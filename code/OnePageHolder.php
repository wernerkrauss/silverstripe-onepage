<?php

class OnePageHolder extends Page {

	private static $db = array();

    private static $has_one = array();
    
    private static $has_many = array();
    
    private static $many_many = array();
    
    private static $belongs_many_many = array();
    
    private static $singular_name = '';
    
    private static $plural_name = 's';
    
    private static $summary_fields = array();
    
    private static $searchable_fields = array();
    
    public function getCMSFields() {
		$fields = parent::getCMSFields();

		return $fields;
	}
}

class OnePageHolder_Controller extends Page_Controller {

	private static $block_thirdpary_jquery = true;

	public function init(){
		parent::init();
		if ($this->config()->get('block_thirdparty_jquery')) {
//			Requirements::block(THIRDPARTY_DIR . '/jquery/jquery.js');
//			Requirements::javascript('onepage/javascript/jquery-1.11.1.min.js');
		}
	}
}