<?php

class OnePageSlide extends DataExtension {

	private static $db = array(
		'BackgroundColor' => 'Varchar',
		'TextColor' => 'Varchar',
		'AdditionalCSSClass' => 'Varchar'
	);

    private static $has_one = array(
		'BackgroundImage' => 'Image'
	);

	/**
	 * @inheritdoc
	 */
	public function updateCMSFields(FieldList $fields) {
		//@todo: use https://github.com/heyday/silverstripe-colorpalette for color fields w/ predefined colors

		$image = UploadField::create('BackgroundImage','Background Image')
			->setAllowedFileCategories('image')
			->setAllowedMaxFileNumber(1);
		if ($this->owner->hasMethod('getRootFolderName')) {
			$image->setFolderName($this->owner->getRootFolderName());
		}

		$fields->addFieldToTab('Root.Layout', $image);
		$fields->addFieldToTab('Root.Layout', TextField::create('BackgroundColor', 'Background Color'));
		$fields->addFieldToTab('Root.Layout', TextField::create('TextColor', 'Text Color'));
		$fields->addFieldToTab('Root.Layout', TextField::create('AdditionalCSSClass', 'CSS Class'));

	}

	//@todo: if Parent is a OnePageHolder modify $Link to show to $Parent->Link() / #$URLSegment
	//@todo: if Parent is a OnePageHolder disable ShowInMenus
	//@todo: don't show slide in google sitempap

	/**
	 * @todo: use customCSS?
	 * @return string
	 */
	public function getOnePageSlideStyle(){
		$style = '';

		$style .= $this->owner->BackgroundColor
			? 'background-color: ' . $this->owner->BackgroundColor . '; '
			: '';

		$style .= $this->owner->TextColor
			? ' color: ' . $this->owner->TextColor. ' !important; '
			: '';

		$this->owner->extend('updateOnePageSlideStyle', $style);

		return $style;

	}
}

