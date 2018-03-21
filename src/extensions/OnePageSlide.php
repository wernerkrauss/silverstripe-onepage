<?php

namespace Netwerkstatt\Onepage\Extensions;

use Heyday\ColorPalette\Fields\ColorPaletteField;
use Netwerkstatt\Onepage\Pages\OnePageHolder;
use Page;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\CMS\Controllers\ModelAsController;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\SSViewer;

class OnePageSlide extends DataExtension
{
    private static $db = [
        'BackgroundColor' => 'Varchar',
        'HeadingColor' => 'Varchar',
        'TextColor' => 'Varchar',
        'AdditionalCSSClass' => 'Varchar'
    ];

    private static $has_one = [
        'BackgroundImage' => Image::class
    ];

    private static $owns = [
        'BackgroundImage'
    ];

    private static $background_color_palette = [
        '#fff',
        '#444',
        '#000'
    ];
    private static $heading_color_palette = [
        '#000',
        '#fff'
    ];
    private static $text_color_palette = [
        '#000',
        '#fff'
    ];

    /**
     * Should we modify the link to represent anchors?
     *
     * @var bool
     */
    private static $do_modify_link = true;

    /**
     * limit the generated form fields to slides (direct children of a OnePageHolder)
     * @var bool
     */
    private static $use_only_on_onepage_slides = false;

    /**
     * do not require colors to be set
     * @var bool
     */
    private static $colors_can_be_empty = false;

    /**
     * @inheritdoc
     */
    public function updateFieldLabels(&$labels)
    {
        $labels = parent::updateFieldLabels($labels);

        $labels['Title'] = _t('Netwerkstatt\\Onepage\\Extensions\\OnePageSlide.db_Title', 'Title');
        $labels['BackgroundColor'] = _t('Netwerkstatt\\Onepage\\Extensions\\OnePageSlide.db_BackgroundColor', 'Background Color');
        $labels['HeadingColor'] = _t('Netwerkstatt\\Onepage\\Extensions\\OnePageSlide.db_HeadingColor', 'Heading Color');
        $labels['TextColor'] = _t('Netwerkstatt\\Onepage\\Extensions\\OnePageSlide.db_TextColor', 'Text Color');
        $labels['AdditionalCSSClass'] = _t('Netwerkstatt\\Onepage\\Extensions\\OnePageSlide.db_AdditionalCSSClass', 'Additional CSS class');

        $labels['BackgroundImage'] = _t('Netwerkstatt\\Onepage\\Extensions\\OnePageSlide.has_many_BackgroundImage', 'Background Image');
    }


    /**
     * @inheritdoc
     */
    public function updateCMSFields(FieldList $fields)
    {
        if (Config::inst()->get(OnePageSlide::class, 'use_only_on_onepage_slides')
            && !$this->owner->isOnePageSlide()
        ) {
            return;
        }

        $image = UploadField::create('BackgroundImage', $this->owner->fieldLabel('BackgroundImage'))
            ->setAllowedFileCategories('image');
        if ($this->owner->hasMethod('getRootFolderName')) {
            $image->setFolderName($this->owner->getRootFolderName());
        }

        $colorFields = [
            'BackgroundColor' => 'background_color_palette',
            'HeadingColor' => 'heading_color_palette',
            'TextColor' => 'text_color_palette'
        ];

        $layout = $fields->findOrMakeTab('Root.Layout', _t('Netwerkstatt\\Onepage\\Extensions\\OnePageSlide.TABLAYOUT', 'Layout'));
        $layout->push($image);

        foreach ($colorFields as $fieldName => $palette) {
            $layout->push($this->generateColorPalette($fieldName, $palette));
        }
        $layout->push(TextField::create('AdditionalCSSClass', $this->owner->fieldLabel('AdditionalCSSClass')));
    }

    protected function generateColorPalette($fieldName, $paletteSetting)
    {
        $palette = $this->owner->config()->get($paletteSetting)
            ? $this->owner->config()->get($paletteSetting)
            : Config::inst()->get(OnePageSlide::class, $paletteSetting);

        $field = ColorPaletteField::create(
            $fieldName,
            $this->owner->fieldLabel($fieldName),
            ArrayLib::valuekey($palette)
        );

        if (Config::inst()->get(OnePageSlide::class, 'colors_can_be_empty')) {
            $field = $field->setEmptyString('none');
        }

        return $field;
    }

    //@todo: if Parent is a OnePageHolder modify $Link to show to $Parent->Link() / #$URLSegment
    //@todo: if Parent is a OnePageHolder disable ShowInMenus
    //@todo: don't show slide in google sitempap

    /**
     * @todo: use customCSS?
     * @return string
     */
    public function getOnePageSlideStyle()
    {
        $style = '';

        $style .= $this->owner->BackgroundColor
            ? 'background-color: ' . $this->owner->BackgroundColor . '; '
            : '';

        $style .= $this->owner->TextColor
            ? ' color: ' . $this->owner->TextColor . ' !important; '
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
    public function contentcontrollerInit(&$controller)
    {
        if ($this->owner->isOnePageSlide() && $this->isCMSPreview()) {
            //redirect and pass current ID by param, as anchor tags re not sent to the server
            $url = Controller::join_links(
                $this->owner->RelativeLink(),
                '?EditPageID=' . $this->owner->ID,
                '?Stage=' . Versioned::current_stage(),
                '?CMSPreview=1'
            );
            $controller->redirect($url);
        }

        if ($this->owner->isOnePageSlide()
            && !$controller->urlParams['Action']
            && !Director::is_ajax()
            && !$this->isCMSPreview()
        ) {
            $controller->redirect($this->owner->RelativeLink(), 301);
        }
    }


    /**
     * Updates RelativeLink()
     *
     * If no $action is given it changes /path/to/URLSegment into /path/to#URLSegment
     *
     * @param $base
     * @param $action
     */
    public function updateRelativeLink(&$base, &$action)
    {
        //we need to call the redirection for cms preview
        if (Controller::curr() instanceof LeftAndMain) {
            return;
        }

        if (Config::inst()->get(OnePageSlide::class, 'do_modify_link') == false) {
            return;
        }

        if ($this->owner->isNestedOnePageSlide()) {
            $base = $this->owner->Parent()->RelativeLink($action) . '-' . $this->owner->URLSegment;
            return;
        }

        if ($this->owner->isOnePageSlide()) {
            //			$base = $this->owner->Parent()->RelativeLink('#' . $this->owner->URLSegment); //e.g. /home/#urlsegment :(
            $base = Controller::join_links(
                $this->owner->Parent()->RelativeLink($action),
                '#' . $this->owner->URLSegment
            ); // just /#urlsegment
        }
    }

    /**
     * Helper to get a unmofified link if a slide should represent a classical page, not a "block" inside a OnePageHolder
     *
     * @param null $action
     * @return mixed
     */
    public function UnmodifiedRelativeLink($action = null)
    {
        Config::inst()->update(OnePageSlide::class, 'do_modify_link', false);
        $link = $this->owner->RelativeLink($action);
        Config::inst()->update(OnePageSlide::class, 'do_modify_link', true);

        return $link;
    }

    /**
     * Checks, if the current page is a slide of a one-page by checking if the parent page is a OnePageHolder
     *
     * @return bool
     */
    public function isOnePageSlide()
    {
        return ($this->owner->Parent() instanceof OnePageHolder);
    }

    /**
     * Checks if the current page is a nested one-page slide
     *
     * @return bool
     */
    public function isNestedOnePageSlide()
    {
        return $this->owner->ParentID && $this->owner->Parent instanceof Page
            ? $this->owner->Parent()->isOnePageSlide()
            : false;
    }

    /**
     * Helper to check if we're previewing the current page in CMS
     *
     * @return bool
     */
    public function isCMSPreview()
    {
        $isCMSPreview = Controller::curr()->getRequest()->getVar('CMSPreview');

        return (bool)$isCMSPreview;
    }

    /**
     * renders the current page using the ClassName_onepage template,
     * e.g. Page_onepage
     *
     * The suffix is generated by @link getOnePageTemplateSuffix
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getOnePageContent()
    {
        $templateNames = SSViewer::get_templates_by_class(
            $this->owner->Classname,
            $this->getOnePageTemplateSuffix(),
            SiteTree::class
        )
            ?: ['Page_onepage', 'type' => 'Layout'];

        foreach ($templateNames as $templateName) {
            if (!is_array($templateName)) {
                $templateNames[] = [$templateName, 'type' => 'Layout'];
            }
        }

        $controller = ModelAsController::controller_for($this->owner);

        return $controller->renderWith($templateNames);
    }


    /**
     * Helper function to generate the template suffix for the current page.
     * Calls page's "generateOnePageTemplateSuffix" method if it exists.
     * This way your page can define the template suffix to be e.g. '_layout1_onepage' instead of just '_onepage'
     *
     * @return string
     */
    public function getOnePageTemplateSuffix()
    {
        return $this->owner->hasMethod('generateOnePageTemplateSuffix')
            ? $this->owner->generateOnePageTemplateSuffix()
            : '_onepage';
    }
}
