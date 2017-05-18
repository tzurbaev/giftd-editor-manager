<?php

namespace Giftd\Editor;

class CustomizationsManager
{
    /**
     * @var Customization
     */
    protected $customization;

    /**
     * CustomizationsManager constructor.
     *
     * @param Customization   $customization
     * @param SettingsManager $settings
     * @param array           $previewData   = []
     */
    public function __construct(Customization $customization, SettingsManager $settings, array $previewData = [])
    {
        $customization->setSettings($settings)->setPreviewData($previewData);

        $this->customization = $customization;
    }

    /**
     * Parses Customization's editables and generates final editables array.
     *
     * @return array
     */
    public function parseEditables()
    {
        $editables = $this->customization->editables();

        if (!is_array($editables)) {
            return [];
        }

        return $this->groupEditableSettings($editables);
    }

    /**
     * Builds customization data.
     *
     * @return Customization
     */
    public function buildData()
    {
        $this->customization->build();

        return $this->customization;
    }

    /**
     * Saves customization settings.
     *
     * @return $this
     */
    public function saveSettings()
    {
        $this->customization->save();

        return $this;
    }

    /**
     * Performs editable settings groupping.
     *
     * @param array $editables
     *
     * @return array
     */
    protected function groupEditableSettings(array $editables)
    {
        foreach ($editables as &$group) {
            if (!count($group['settings'])) {
                continue;
            }

            $settings = [];

            foreach ($group['settings'] as $setting) {
                /**
                 * @var Editable $setting
                 */
                $attributes = $setting->attributes();

                if (empty($attributes['name'])) {
                    continue;
                }

                $settings[$attributes['name']] = $attributes;
                unset($settings[$attributes['name']]['name']);
            }

            $group['settings'] = $settings;
        }

        return $editables;
    }
}
