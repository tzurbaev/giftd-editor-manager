<?php

namespace Tests;

use Giftd\Editor\CustomizationsManager;
use Tests\Stubs\DummyCustomization;

class CustomizationsManagerTest extends TestCase
{
    public function testEditablesGrouppingByName()
    {
        $customization = new DummyCustomization();
        $manager = new CustomizationsManager($customization, $this->manager);

        $editables = $manager->parseEditables();

        $this->assertSame($customization->expected(), $editables);
    }

    public function testBuildEditable()
    {
        $customization = new DummyCustomization();
        $manager = new CustomizationsManager($customization, $this->manager);

        $manager->buildData();

        $this->assertSame($customization->expectedData(), $customization->all());
    }

    public function testBuildEditableWithPreviewData()
    {
        $customization = new DummyCustomization();
        $previewData = ['email_heading' => 'Hello, {user}! This is simple notification.'];
        $manager = new CustomizationsManager($customization, $this->manager, $previewData);

        $manager->buildData();

        $data = $customization->all();
        $this->assertSame('Hello, John! This is simple notification.', $data['email_heading']);
    }

    public function testBuildEditableWithPreviewDataAndNonSettingsKey()
    {
        $customization = new DummyCustomization();
        $previewData = ['hello_world' => 'New Value'];
        $manager = new CustomizationsManager($customization, $this->manager, $previewData);

        $manager->buildData();

        $data = $customization->all();
        $this->assertSame('New Value', $data['some_other']);
    }

    public function testSettingsMap()
    {
        $customization = new DummyCustomization();
        $previewData = ['has_referral' => 'yes'];
        $manager = new CustomizationsManager($customization, $this->manager, $previewData);

        $manager->buildData();

        $data = $customization->all();
        $this->assertSame('yes', $data['has_referral']);
        $this->assertTrue($this->manager->equals('show_referral_block', 'yes'));
    }

    public function testSaveSettings()
    {
        $customization = new DummyCustomization();
        $requestData = ['email_heading' => 'Welcome, {user}!'];
        $manager = new CustomizationsManager($customization, $this->manager, $requestData);
        $manager->saveSettings();

        $this->assertTrue($this->manager->equals('email_heading', 'Welcome, {user}!'));
    }
}
