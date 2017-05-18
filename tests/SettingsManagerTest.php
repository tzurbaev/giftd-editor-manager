<?php

namespace Tests;

class SettingsManagerTest extends TestCase
{
    public function testGetSetting()
    {
        $this->assertSame($this->settings['email_subheading'], $this->manager->get('email_subheading'));
    }

    public function testGetWithDefaultValue()
    {
        $this->assertNull($this->manager->get('non-existed-setting'));
        $this->assertSame('default-value', $this->manager->get('non-existed-setting', 'default-value'));
    }

    public function testGetMany()
    {
        $expected = array_intersect_key($this->settings, ['email_heading' => true, 'email_subheading' => true]);
        $this->assertSame($expected, $this->manager->getMany(['email_heading', 'email_subheading']));
    }

    public function testGetManyWithDefaultValues()
    {
        $expected = array_intersect_key($this->settings, ['email_heading' => true, 'email_subheading' => true]);
        $expected['non-existed'] = 'default-value';

        $this->assertSame(
            $expected,
            $this->manager->getMany(
                ['email_heading', 'email_subheading', 'non-existed'],
                ['non-existed' => 'default-value']
            )
        );
    }

    public function testGetWithPlaceholders()
    {
        $this->assertSame('Hello, John!', $this->manager->getWithPlaceholders('email_heading', ['name' => 'John']));
        $this->assertSame('Hello, {name}!', $this->manager->getWithPlaceholders('email_heading'));
        $this->assertSame(
            $this->settings['email_subheading'],
            $this->manager->getWithPlaceholders('email_subheading', ['non-existed' => 'hello'])
        );
    }

    public function testEquals()
    {
        $this->assertTrue($this->manager->equals('email_subheading', $this->settings['email_subheading']));
        $this->assertFalse($this->manager->equals('email_heading', $this->settings['email_subheading']));
    }

    public function testUpdate()
    {
        $this->assertTrue($this->manager->equals('email_subheading', $this->settings['email_subheading']));
        $this->manager->update('email_subheading', 'Please do not reply');

        $this->assertFalse($this->manager->equals('email_subheading', $this->settings['email_subheading']));
        $this->assertSame('Please do not reply', $this->resource->getResourceSettingsValues(['email_subheading'])['email_subheading']);
    }

    public function testReplace()
    {
        $this->assertTrue($this->manager->equals('email_subheading', $this->settings['email_subheading']));
        $this->manager->replace(['email_subheading' => 'replaced']);
        $this->assertTrue($this->manager->equals('email_subheading', 'replaced'));
    }
}
