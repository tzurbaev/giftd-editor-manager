<?php

namespace Tests\Stubs;

use Giftd\Editor\Customization;

class DummyCustomization extends Customization
{
    public function settingsMap()
    {
        return ['has_referral' => 'show_referral_block'];
    }

    public function editables()
    {
        return [
            'texts' => $this->group('texts-settings', 'Texts Settings', function () {
                return [
                    $this->editable('email_heading', 'text', 'Email Heading')
                        ->withValue($this->settingWithPlaceholders('email_heading'))
                        ->placeholder('user', 'User name', 'John'),

                    $this->editable('email_subheading', 'text', 'Email Subheading')
                        ->withValue($this->setting('email_subheading'))
                        ->asInline()
                ];
            })
        ];
    }

    public function build()
    {
        $userName = 'John';

        $this->data['email_heading'] = $this->settingWithPlaceholders('email_heading', ['user' => $userName]);
        $this->data['email_subheading'] = $this->setting('email_subheading');
        $this->data['some_other'] = $this->setting('hello_world', 'Other value');
        $this->data['has_referral'] = $this->setting('show_referral_block');
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
                        'value' => $this->setting('email_heading'),
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
                        'value' => $this->setting('email_subheading'),
                    ],
                ],
            ],
        ];
    }

    public function expectedData()
    {
        return [
            'email_heading' => $this->settingWithPlaceholders('email_heading', ['user' => 'John']),
            'email_subheading' => $this->setting('email_subheading'),
            'some_other' => $this->setting('hello_world', 'Other value'),
            'has_referral' => $this->setting('show_referral_block'),
        ];
    }
}
