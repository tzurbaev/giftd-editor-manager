<?php

namespace Giftd\Editor;

use Giftd\Editor\Contracts\HasSettings;

abstract class SettingsManager
{
    /**
     * Resource with settings.
     *
     * @var HasSettings
     */
    protected $resource;

    /**
     * Replaced settings.
     *
     * @var array
     */
    protected $replaced = [];

    /**
     * SettingsManager constructor.
     *
     * @param HasSettings $resource
     */
    public function __construct(HasSettings $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get the resource.
     *
     * @return HasSettings
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Returns single setting value (or default value if setting was not found or is NULL).
     *
     * @param string $setting
     * @param null   $defaultValue
     *
     * @return mixed
     */
    public function get(string $setting, $defaultValue = null)
    {
        $values = $this->getMany([$setting], [$setting => $defaultValue]);

        return $values[$setting] ?? $defaultValue;
    }

    /**
     * Returns multiple settings values (or replaces missing settings with default values).
     *
     * @param array $settings
     * @param array $defaultValues
     *
     * @return array
     */
    public function getMany(array $settings, array $defaultValues = [])
    {
        $values = $this->resource->getResourceSettingsValues($settings);
        $replaced = $this->getReplaced($settings);

        $result = array_merge($values, $replaced);

        if (count($result) === count($settings)) {
            return $result;
        }

        return $result + $defaultValues;
    }

    /**
     * Returns string value and performs placeholders replacement.
     *
     * @param string $setting
     * @param array  $placeholders
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getWithPlaceholders(string $setting, array $placeholders = [])
    {
        $value = $this->get($setting);

        if (!is_string($value)) {
            throw new \InvalidArgumentException('"'.$setting.'" setting value is not a string.');
        }

        return $this->replacePlaceholders($value, $placeholders);
    }

    /**
     * Performs placeholders replacement in given text.
     *
     * @param string $value
     * @param array  $placeholders
     *
     * @return string
     */
    public function replacePlaceholders(string $value, array $placeholders = [])
    {
        if (!count($placeholders)) {
            return $value;
        }

        foreach ($placeholders as $find => $replace) {
            $value = str_replace('{'.$find.'}', $replace, $value);
        }

        return $value;
    }

    /**
     * Returns in-memory replaced settings.
     *
     * @param array $settings
     *
     * @return array
     */
    public function getReplaced(array $settings)
    {
        return array_intersect_key($this->replaced, array_flip($settings));
    }

    /**
     * Determines if setting value equals to given value.
     *
     * @param string $setting
     * @param $value
     *
     * @return bool
     */
    public function equals(string $setting, $value): bool
    {
        return $this->get($setting) === $value;
    }

    /**
     * Updates single setting value.
     *
     * @param string $setting
     * @param $value
     *
     * @return SettingsManager
     */
    public function update(string $setting, $value)
    {
        return $this->updateMany([$setting => $value]);
    }

    /**
     * Updates settings values.
     *
     * @param array $settings
     *
     * @return SettingsManager
     */
    public function updateMany(array $settings)
    {
        $this->resource->setResourceSettingsValues($settings);

        return $this;
    }

    /**
     * Replaces given settings values in memory storage.
     *
     * @param array $settings
     * @param bool  $fullReplacement
     *
     * @return SettingsManager
     */
    public function replace(array $settings, bool $fullReplacement = false)
    {
        if ($fullReplacement === true) {
            $this->replaced = $settings;
        } else {
            $this->replaced = array_merge($this->replaced, $settings);
        }

        return $this;
    }
}
