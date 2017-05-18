<?php

namespace Tests;

use Giftd\Editor\SettingsManager;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\Stubs\DummyResource;

class TestCase extends BaseTestCase
{
    /**
     * @var array
     */
    protected $settings = [];
    /**
     * @var DummyResource
     */
    protected $resource;

    /**
     * @var SettingsManager
     */
    protected $manager;

    public function setUp()
    {
        parent::setUp();

        $this->settings = $this->exampleSettings();
        $this->resource = new DummyResource($this->settings);
        $this->manager = new SettingsManager($this->resource);
    }

    /**
     * @return array
     */
    protected function exampleSettings(): array
    {
        return [
            'email_heading' => 'Hello, {name}!',
            'email_subheading' => 'This is automated notification, please do not reply.',
            'balance_decreased_line' => 'Your balance decreased down to {balance} points.',
            'balance_increased_line' => 'Your balance increased up to {balance} points.',
            'show_referral_block' => 1,
            'referral_block_heading' => 'Invite your friends!',
        ];
    }
}
