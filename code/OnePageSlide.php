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

	private static $use_only_on_onepage_slides = false;


	/**
	 * @inheritdoc
	 */
	public function updateFieldLabels(&$labels)
	{
		$labels = parent::updateFieldLabels($labels);

		$labels['Title'] = _t('OnePageSlide.db_Title', 'Title');
		$labels['BackgroundColor'] = _t('OnePageSlide.db_BackgroundColor', 'Background Color');
		$labels['HeadingColor'] = _t('OnePageSlide.db_HeadingColor', 'Heading Color');
		$labels['TextColor'] = _t('OnePageSlide.db_TextColor', 'Text Color');
		$labels['AdditionalCSSClass'] = _t('OnePageSlide.db_AdditionalCSSClass', 'Additional CSS class');

		$labels['BackgroundImage'] = _t('OnePageSlide.has_many_BackgroundImage', 'Background Image');
	}


	/**
	 * @inheritdoc
	 */
	public function updateCMSFields(FieldList $fields) {

		if (Config::inst()->get($this->class, 'use_only_on_onepage_slides')
			&& !$this->owner->isOnePageSlide()) {
			return;
		}

		$image = UploadField::create('BackgroundImage',$this->owner->fieldLabel('BackgroundImage'))
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
			$this->owner->fieldLabel('BackgroundColor'),
			$backgroundPalette
		);

		$headingPalette = $this->owner->config()->get('heading_color_palette')
			? $this->owner->config()->get('heading_color_palette')
			: Config::inst()->get($this->class, 'heading_color_palette');
		$headingColor = ColorPaletteField::create(
			'HeadingColor',
			$this->owner->fieldLabel('HeadingColor'),
			$headingPalette
		);

		$textPalette = $this->owner->config()->get('text_color_palette')
			? $this->owner->config()->get('text_color_palette')
			: Config::inst()->get($this->class, 'text_color_palette');
		$textColor = ColorPaletteField::create(
			'TextColor',
			$this->owner->fieldLabel('TextColor'),
			$textPalette
		);

		$layout = $fields->findOrMakeTab('Root.Layout',_t('OnePageSlide.TABLAYOUT', 'Layout'));
		$layout->push($image);
		$layout->push($backgroundColor);
		$layout->push($headingColor);
		$layout->push($textColor);
		$layout->push(TextField::create('AdditionalCSSClass', $this->owner->fieldLabel('AdditionalCSSClass')));
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
	 * check if this is a OnePageSlide and redirect to parent if
	 *  - controller has no action
	 *  - request isn't an ajax request
	 */
	public function contentcontrollerInit(&$controller){
		if ($this->owner->isOnePageSlide()
				&& !$controller->urlParams['Action']
				&& !Director::is_ajax()) {
			$controller->redirect($this->owner->Parent()->Link('#'.$this->owner->URLSegment), 301);
		}
	}

	/**
	 * Udates RelativeLink()
	 *
	 * If no $action is given it changes /path/to/URLSegment into /path/to#URLSegment
	 *
	 * @param $base
	 * @param $action
	 */
	public function updateRelativeLink(&$base, &$action){
		if (!$action && $this->owner->isOnePageSlide()) {
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

		$controller = ModelAsController::controller_for($this->owner);

	    return $controller->renderWith($templateName);
	}
}

