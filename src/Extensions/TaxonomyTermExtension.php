<?php

namespace CWP\CWP\Extensions;

use CWP\CWP\PageTypes\BasePage;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Taxonomy\TaxonomyTerm;

/**
 * @method SilverStripe\ORM\ManyManyList<BasePage> Pages()
 * @extends Extension<TaxonomyTerm>
 */
class TaxonomyTermExtension extends Extension
{
    private static $api_access = true;

    private static $belongs_many_many = array(
        'Pages' => BasePage::class
    );

    public function updateCMSFields(FieldList $fields)
    {
        $pagesGridField = $fields->dataFieldByName('Pages');
        if ($pagesGridField) {
            $pagesGridField->getConfig()->removeComponentsByType(GridFieldAddNewButton::class);
        }
    }
}
