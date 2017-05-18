<?php

namespace Tests\Stubs;

use Giftd\Editor\Customization;

class DummyCustomization extends Customization
{
    public function editables()
    {
        return [
            'texts' => $this->group('texts-settings', 'Texts Settings', function () {
                return [
                    $this->editable('email_heading', 'text', 'Email Heading')
                        ->withValue($this->previewDataWithPlaceholders('email_heading'))
                        ->placeholder('user', 'User name', 'John'),

                    $this->editable('email_subheading', 'text', 'Email Subheading')
                        ->withValue($this->previewData('email_subheading'))
                        ->asInline()
                ];
            })
        ];
    }

    public function build()
    {
        $userName = 'John';

        $this->data['email_heading'] = $this->previewDataWithPlaceholders('email_heading', ['user' => $userName]);
        $this->data['email_subheading'] = $this->previewData('email_subheading');
    }

    public function expected()
    {
        return [
            'texts' => [
                'id' => 'texts-settings',
                'title' => 'Texts Settings',
                'settings' => [
                    'email_heading' => [
                        'title' => 'Email Heading',
                        'type' => 'text',
                        'inline' => false,
                        'value' => $this->previewData('email_heading'),
                        'placeholders' => [
                            'user' => [
                                'title' => 'User name',
                                'value' => 'John',
                            ],
                        ],
                    ],
                    'email_subheading' => [
                        'title' => 'Email Subheading',
                        'type' => 'text',
                        'inline' => true,
                        'value' => $this->previewData('email_subheading'),
                    ],
                ],
            ],
        ];
    }

    public function expectedData()
    {
        return [
            'email_heading' => $this->previewDataWithPlaceholders('email_heading', ['user' => 'John']),
            'email_subheading' => $this->previewData('email_subheading'),
        ];
    }
}
