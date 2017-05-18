<?php

namespace Tests\Stubs;

use Giftd\Editor\Contracts\HasSettings;
use Giftd\Editor\Traits\PropertyBagTrait;

class PropertyBagResource implements HasSettings
{
    use PropertyBagTrait;

    /**
     * @var PropertyBagSettingsStub
     */
    protected $propertyBagSettings;

    /**
     * PropertyBagResource constructor.
     *
     * @param array $defaults
     * @param array $settings
     */
    public function __construct(array $defaults, array $settings = [])
    {
        $this->propertyBagSettings = new PropertyBagSettingsStub($defaults, $settings);
    }

    /**
     * @param mixed $passed = null
     *
     * @return PropertyBagSettingsStub
     */
    public function settings($passed = null)
    {
        if (is_array($passed)) {
            $this->propertyBagSettings->update($passed);
        }

        return $this->propertyBagSettings;
    }
}
