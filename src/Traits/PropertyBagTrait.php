<?php

namespace Giftd\Editor\Traits;

trait PropertyBagTrait
{
    /**
     * If passed is string, get settings class for the resource or return value
     * for given key. If passed is array, set the key value pair.
     *
     * @param string|array $passed = null
     *
     * @return \LaravelPropertyBag\Settings\Settings|mixed
     */
    abstract public function settings($passed = null);

    /**
     * Returns values by settings names.
     *
     * @param array $settings
     *
     * @return array
     */
    public function getResourceSettingsValues(array $settings)
    {
        $values = [];
        $pbSettings = $this->settings();
        $saved = $pbSettings->allSaved();

        foreach ($settings as $setting) {
            $values[$setting] = $saved->get($setting, $pbSettings->getDefault($setting));
        }

        return $values;
    }

    /**
     * Sets settings values.
     *
     * @param array $settings
     *
     * @return static
     */
    public function setResourceSettingsValues(array $settings)
    {
        $this->settings($settings);

        return $this;
    }
}
