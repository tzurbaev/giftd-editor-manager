<?php

namespace Giftd\Editor;

class Editable implements \JsonSerializable
{
    /**
     * Editable attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Editable constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->attributes['name'] = $name;
    }

    /**
     * Returns Editable's attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->attributes;
    }

    /**
     * Data for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->attributes();
    }

    /**
     * Sets editable title.
     *
     * @param string|null $title
     *
     * @return $this
     */
    public function titledAs(string $title = null)
    {
        $this->attributes['title'] = $title;

        return $this;
    }

    /**
     * Sets editable type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function usingType(string $type)
    {
        $this->attributes['type'] = $type;

        if ($type === 'text') {
            $this->asInline(false);
        }

        return $this;
    }

    /**
     * Sets editable value.
     *
     * @param null $value
     *
     * @return $this
     */
    public function withValue($value = null)
    {
        $this->attributes['value'] = $value;

        return $this;
    }

    /**
     * Sets editable options list.
     *
     * @param array $options
     *
     * @return $this
     */
    public function withOptions(array $options = [])
    {
        $data = [];

        foreach ($options as $value => $label) {
            $data[] = ['value' => $value, 'label' => $label];
        }

        $this->attributes['options'] = $data;

        return $this;
    }

    /**
     * Sets custom editable attributes.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function withAttributes(array $attributes = [])
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    /**
     * Set upload URL.
     *
     * @param string $url
     *
     * @return Editable
     */
    public function uploadTo(string $url)
    {
        return $this->withSetting('upload_url', $url);
    }

    /**
     * Set request headers.
     *
     * @param array $headers
     *
     * @return Editable
     */
    public function withHeaders(array $headers)
    {
        return $this->withSetting('headers', $headers);
    }

    /**
     * Set custom setting value.
     *
     * @param string $setting
     * @param $value
     *
     * @return $this
     */
    public function withSetting(string $setting, $value)
    {
        if (!isset($this->attributes['settings'])) {
            $this->attributes['settings'] = [];
        }

        $this->attributes['settings'][$setting] = $value;

        return $this;
    }

    /**
     * Creates new placeholder and pushes it to editable's placeholders list.
     *
     * @param string $name
     * @param string $title
     * @param $value
     * @param array $attributes
     *
     * @return $this
     */
    public function placeholder(string $name, string $title, $value, array $attributes = [])
    {
        if (!isset($this->attributes['placeholders'])) {
            $this->attributes['placeholders'] = [];
        }

        $this->attributes['placeholders'][$name] = array_merge([
            'title' => $title,
            'value' => $value,
        ], $attributes);

        return $this;
    }

    /**
     * Sets inline mode.
     *
     * @param bool $inline = true
     *
     * @return $this
     */
    public function asInline(bool $inline = true)
    {
        $this->attributes['inline'] = $inline;

        return $this;
    }
}
