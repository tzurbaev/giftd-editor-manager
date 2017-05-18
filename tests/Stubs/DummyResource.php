<?php

namespace Tests\Stubs;

use Giftd\Editor\Contracts\HasSettings;

class DummyResource implements HasSettings
{
    /**
     * @var array
     */
    protected $settings = [];

    /**
     * DummyResource constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceSettingsValues(array $settings)
    {
        return array_intersect_key($this->settings, array_flip($settings));
    }

    /**
     * {@inheritdoc}
     */
    public function setResourceSettingsValues(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);

        return $this;
    }
}
