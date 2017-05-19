<?php

namespace Giftd\Editor;

abstract class Customization
{
    /**
     * Actual customization data.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Customization-related settings.
     *
     * @var SettingsManager
     */
    protected $settings;

    /**
     * Creates groupped editables list.
     *
     * @return array
     */
    abstract public function editables();

    /**
     * Builds template data.
     *
     * @return mixed
     */
    abstract public function build();

    /**
     * Returns ['customization_name' => 'setting_key'] map.
     *
     * @return array
     */
    public function settingsMap()
    {
        return [
            //
        ];
    }

    /**
     * Triggers before the save settings operation.
     */
    public function beforeSaving()
    {
        //
    }

    /**
     * Saves customization settings.
     *
     * @return $this
     */
    public function save()
    {
        $this->beforeSaving();

        $this->settings->saveReplaced();

        return $this;
    }

    /**
     * Returns value for given customization data key or default value.
     *
     * @param string $key
     * @param mixed  $defaultValue = null
     *
     * @return mixed
     */
    public function get(string $key, $defaultValue = null)
    {
        return $this->data[$key] ?? $defaultValue;
    }

    /**
     * Returns customization data.
     *
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Sets preview data.
     *
     * @param array $previewData
     *
     * @return $this
     */
    public function setPreviewData(array $previewData = [])
    {
        if (!count($previewData)) {
            return $this;
        }

        $settingsMap = $this->settingsMap();

        if (count($settingsMap) > 0) {
            $previewData = $this->renamePreviewDataKeys($previewData, $settingsMap);
        }

        $this->settings->replace($previewData);

        return $this;
    }

    /**
     * Sets settings manager.
     *
     * @param SettingsManager $settings
     *
     * @return $this
     */
    public function setSettings(SettingsManager $settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Renames customization keys to settings keys.
     *
     * @param array $previewData
     * @param array $map
     *
     * @return array
     */
    protected function renamePreviewDataKeys(array $previewData, array $map)
    {
        foreach ($map as $customizationName => $settingsKey) {
            if (!array_key_exists($customizationName, $previewData)) {
                continue;
            }

            $previewData[$settingsKey] = $previewData[$customizationName];
            unset($previewData[$customizationName]);
        }

        return $previewData;
    }

    /**
     * Returns value from preview data or default value.
     *
     * @param string $key
     * @param null   $defaultValue
     *
     * @return mixed|null
     */
    protected function setting(string $key, $defaultValue = null)
    {
        return $this->settings->get($key, $defaultValue);
    }

    /**
     * Returns value from preview data or default value with replaced placeholders.
     *
     * @param string $key
     * @param array  $placeholders
     * @param null   $defaultValue
     *
     * @return string
     */
    protected function settingWithPlaceholders(string $key, array $placeholders = [], $defaultValue = null)
    {
        $text = $this->setting($key, $defaultValue);

        return $this->settings->replacePlaceholders($text, $placeholders);
    }

    /**
     * Creates new editables group.
     *
     * @param string   $id
     * @param string   $title
     * @param \Closure $settingsFactory
     *
     * @return array
     */
    protected function group(string $id, string $title, \Closure $settingsFactory)
    {
        return [
            'id' => $id,
            'title' => $title,
            'settings' => $settingsFactory(),
        ];
    }

    /**
     * Creates new Editable.
     *
     * @param string      $name
     * @param string      $type
     * @param string|null $title
     * @param null        $value
     * @param array       $attributes
     *
     * @return Editable
     */
    protected function editable(string $name, string $type, string $title = null, $value = null, array $attributes = [])
    {
        return (new Editable($name))
            ->titledAs($title)
            ->usingType($type)
            ->withValue($value)
            ->withAttributes($attributes);
    }
}
