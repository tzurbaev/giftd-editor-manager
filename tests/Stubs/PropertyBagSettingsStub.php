<?php

namespace Tests\Stubs;

use Illuminate\Support\Collection;

class PropertyBagSettingsStub
{
    /**
     * @var Collection
     */
    protected $defaults;

    /**
     * @var Collection
     */
    protected $settings;

    /**
     * PropertyBagSettingsStub constructor.
     *
     * @param array $defaults
     * @param array $settings
     */
    public function __construct(array $defaults, array $settings = [])
    {
        $this->settings = new Collection($settings);
        $this->defaults = new Collection($defaults);
    }

    /**
     * @return Collection
     */
    public function allSaved()
    {
        return $this->settings;
    }

    /**
     * @param string $setting
     *
     * @return mixed
     */
    public function getDefault(string $setting)
    {
        return $this->defaults->get($setting);
    }

    /**
     * @param array $settings
     *
     * @return $this
     */
    public function update(array $settings)
    {
        $this->settings = $this->settings->merge($settings);

        return $this;
    }
}
