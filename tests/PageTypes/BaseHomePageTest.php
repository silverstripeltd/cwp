<?php

namespace CWP\CWP\Tests\PageTypes;

use CWP\CWP\PageTypes\BaseHomePage;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\FieldList;
use SilverStripe\Versioned\Versioned;

class BaseHomePageTest extends SapphireTest
{
    protected static $fixture_file = 'BaseHomePageTest.yml';

    /**
     * Check each feature field appears once, inside its feature toggle
     */
    public function testFeatureFieldsAreNotDuplicated(): void
    {
        $fields = $this->objFromFixture(BaseHomePage::class, 'home')->getCMSFields();

        $this->assertFeatureFieldsInToggles($fields);
    }

    /**
     * Check an archived home page builds its CMS fields
     */
    public function testArchivedPageBuildsCMSFields(): void
    {
        $page = $this->objFromFixture(BaseHomePage::class, 'home');
        $page->publishRecursive();
        $id = $page->ID;
        $page->doArchive();

        $archived = Versioned::get_latest_version(BaseHomePage::class, $id);
        $this->assertTrue($archived->isArchived());

        $this->assertFeatureFieldsInToggles($archived->getCMSFields());
    }

    /**
     * Assert every feature field sits in its feature toggle and nowhere else
     */
    private function assertFeatureFieldsInToggles(FieldList $fields): void
    {
        $toggles = [];
        foreach (['FeatureOne', 'FeatureTwo'] as $toggleName) {
            foreach (['Title', 'Category', 'Content', 'ButtonText', 'LinkID'] as $suffix) {
                $toggles[$toggleName][] = $toggleName . $suffix;
            }
        }

        // getDataFields() throws when a field name appears more than once
        $dataFields = $fields->getDataFields();

        foreach ($toggles as $toggleName => $fieldNames) {
            $toggle = $fields->fieldByName('Root.Features.' . $toggleName);
            $this->assertNotNull($toggle, "$toggleName toggle is missing");

            foreach ($fieldNames as $fieldName) {
                $this->assertArrayHasKey($fieldName, $dataFields);
                $this->assertSame($dataFields[$fieldName], $toggle->fieldByName($fieldName));
            }
        }
    }
}
