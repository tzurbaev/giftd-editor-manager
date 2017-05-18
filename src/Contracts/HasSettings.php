<?php

namespace Giftd\Editor\Contracts;

interface HasSettings
{
    /**
     * Returns values by settings names.
     *
     * @param array $settings
     *
     * @return array
     */
    public function getResourceSettingsValues(array $settings);

    /**
     * Sets settings values.
     *
     * @param array $settings
     *
     * @return static
     */
    public function setResourceSettingsValues(array $settings);
}
