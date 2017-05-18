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
     * Preview data.
     *
     * @var array
     */
    protected $previewData = [];

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
        $this->previewData = $previewData;

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
     * Returns value from preview data or default value.
     *
     * @param string $key
     * @param null   $defaultValue
     *
     * @return mixed|null
     */
    protected function previewData(string $key, $defaultValue = null)
    {
        $defaultValue = $defaultValue ?? $this->settings->get($key);

        return $this->previewData[$key] ?? $defaultValue;
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
    protected function previewDataWithPlaceholders(string $key, array $placeholders = [], $defaultValue = null)
    {
        $text = $this->previewData($key, $defaultValue);

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
