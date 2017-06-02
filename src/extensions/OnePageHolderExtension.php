<?php

namespace Netwerkstatt\Onepage\Extensions;


use SilverStripe\Control\Controller;
use SilverStripe\Core\Extension;


/**
 * Created by IntelliJ IDEA.
 * User: Werner
 * Date: 14.11.2016
 * Time: 14:42
 */
class OnePageHolderExtension extends Extension
{
    public function MetaTags(& $tags)
    {
        $request = Controller::curr()->getRequest();

        if ($currentEditPageID = $request->getVar('EditPageID')) {
            $origIDTag = "<meta name=\"x-page-id\" content=\"{$this->owner->ID}\" />\n";
            $origEditLinkTag = "<meta name=\"x-cms-edit-link\" content=\"" . $this->owner->CMSEditLink() . "\" />\n";

            $modifiedCMSEditLink = str_replace($this->owner->ID, $currentEditPageID, $this->owner->CMSEditLink());

            $newIDTag = "<meta name=\"x-page-id\" content=\"{$currentEditPageID}\" />\n";
            $newEditLinkTag = "<meta name=\"x-cms-edit-link\" content=\"" . $modifiedCMSEditLink . "\" />\n";

            $tags = str_replace($origIDTag, $newIDTag, $tags);
            $tags = str_replace($origEditLinkTag, $newEditLinkTag, $tags);
        }
    }
}
