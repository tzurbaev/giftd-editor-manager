<?php

namespace Tests;

use Giftd\Editor\SettingsManager;
use Tests\Stubs\PropertyBagResource;

class PropertyBagTraitTest extends TestCase
{
    /**
     * @var PropertyBagResource
     */
    protected $resource;

    /**
     * @var SettingsManager
     */
    protected $manager;

    public function setUp()
    {
        parent::setUp();

        $this->resource = new PropertyBagResource($this->settings, []);
        $this->manager = new SettingsManager($this->resource);
    }

    public function testGetSettings()
    {
        $this->assertSame($this->settings['email_heading'], $this->manager->get('email_heading'));
    }

    public function testGetMultipleSettings()
    {
        $expected = array_intersect_key($this->settings, array_flip(['email_heading', 'email_subheading']));
        $actual = $this->manager->getMany(['email_heading', 'email_subheading']);

        $this->assertSame($expected, $actual);
    }

    public function testUpdate()
    {
        $expected = 'Updated Value';
        $this->manager->update('email_heading', $expected);

        $this->assertSame($expected, $this->manager->get('email_heading'));
    }

    public function testReplace()
    {
        $this->manager->replace(['email_heading' => 'Replaced Value']);

        $this->assertSame('Replaced Value', $this->manager->get('email_heading'));
    }
}
