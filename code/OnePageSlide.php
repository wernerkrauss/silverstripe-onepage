<?php

class OnePageSlide extends DataExtension {

	private static $db = array(
		'BackgroundColor' => 'Varchar',
		'HeadingColor' => 'Varchar',
		'TextColor' => 'Varchar',
		'AdditionalCSSClass' => 'Varchar'
	);

    private static $has_one = array(
		'BackgroundImage' => 'Image'
	);

	private static $background_color_palette = array(
		'#fff' => '#fff',
		'#444' => '#444',
		'#000' => '#000'
	);
	private static $heading_color_palette = array(
		'#000' => '#000',
		'#fff' => '#fff'
	);
	private static $text_color_palette = array(
		'#000' => '#000',
		'#fff' => '#fff'
	);
	/**
	 * @todo: use fieldLabels() for field labels
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

		$backgroundPalette = $this->owner->config()->get('background_color_palette')
			? $this->owner->config()->get('background_color_palette')
			: Config::inst()->get($this->class, 'background_color_palette');
		$backgroundColor = ColorPaletteField::create(
			'BackgroundColor',
			'Background Color',
			$backgroundPalette
		);

		$headingPalette = $this->owner->config()->get('heading_color_palette')
			? $this->owner->config()->get('heading_color_palette')
			: Config::inst()->get($this->class, 'heading_color_palette');
		$headingColor = ColorPaletteField::create(
			'HeadingColor',
			'Heading Color',
			$headingPalette
		);

		$textPalette = $this->owner->config()->get('text_color_palette')
			? $this->owner->config()->get('text_color_palette')
			: Config::inst()->get($this->class, 'text_color_palette');
		$textColor = ColorPaletteField::create(
			'TextColor',
			'Text Color',
			$textPalette
		);

		$fields->addFieldToTab('Root.Layout', $image);
		$fields->addFieldToTab('Root.Layout', $backgroundColor);
		$fields->addFieldToTab('Root.Layout', $headingColor);
		$fields->addFieldToTab('Root.Layout', $textColor);
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

	/**
	 * get's fired on ContentController::init()
	 *
	 * check if this is a OnePageSlide and redirect to parent...
	 */
	public function contentcontrollerInit(&$controller){
		if ($this->owner->isOnePageSlide()) {
			$controller->redirect($this->owner->Parent()->Link('#'.$this->owner->URLSegment), 301);
		}
	}


	public function updateRelativeLink(&$base, &$action){
		if ($this->owner->isOnePageSlide()) {
//			$base = $this->owner->Parent()->RelativeLink('#' . $this->owner->URLSegment); //e.g. /home/#urlsegment :(
			$base = $this->owner->Parent()->RelativeLink($action) . '#' . $this->owner->URLSegment; // just /#urlsegment
		}
	}


	/**
	 * Checks, if the current page is a slide of a one-page by checking if the parent page is a OnePageHolder
	 * @return bool
	 */
	public function isOnePageSlide(){
		return ($this->owner->Parent() instanceof OnePageHolder);
	}

	/**
	 * renders the current page using the ClassName_onepage template,
	 * e.g. Page_onepage
	 *
	 * @return HTMLText
	 */
	public function getOnePageContent(){
		$templateName = SSViewer::get_templates_by_class($this->owner->Classname, '_onepage', 'SiteTree')
			?: 'Page_onepage';

	    return $this->owner->renderWith($templateName);
	}
}

