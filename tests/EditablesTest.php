<?php

namespace Tests;

use Giftd\Editor\Editable;
use Illuminate\Support\Arr;

class EditablesTest extends TestCase
{
    public function testName()
    {
        $editable = new Editable('test');
        $this->assertSame('test', $editable->attributes()['name']);
    }

    /**
     * @param Editable $editable
     * @param string   $attribute
     * @param $expected
     * @dataProvider attributesDataProvider
     */
    public function testAttributes(Editable $editable, string $attribute, $expected)
    {
        $this->assertSame($expected, Arr::get($editable->attributes(), $attribute));
    }

    public function testPlaceholders()
    {
        $editable = (new Editable('test'))->placeholder('balance', 'Balance Value', 1337);
        $placeholder = $editable->attributes()['placeholders']['balance'];
        $expected = ['title' => 'Balance Value', 'value' => 1337];

        $this->assertSame($placeholder, $expected);
    }

    public function testMultiplePlaceholders()
    {
        $expected = (new Editable('test'))
            ->placeholder('first', 'First caption', 'first-value')
            ->placeholder('second', 'Second caption', 'second-value');

        $actual = (new Editable('test'))->placeholders([
            [
                'name' => 'first',
                'title' => 'First caption',
                'value' => 'first-value',
            ],
            [
                'name' => 'second',
                'title' => 'Second caption',
                'value' => 'second-value',
            ],
        ]);

        $this->assertSame($expected->attributes()['placeholders'], $actual->attributes()['placeholders']);
    }

    public function testInvalidPlaceholderFromArrayShouldBeIgnored()
    {
        $expected = (new Editable('test'))
            ->placeholder('first', 'First caption', 'first-value');

        $actual = (new Editable('test'))->placeholders([
            [
                'name' => 'first',
                'title' => 'First caption',
                'value' => 'first-value',
            ],
            [
                // Should be ignored when 'name' is missing.
                'title' => 'Second caption',
                'value' => 'second-value',
            ],
            [
                // Should be ignored when 'title' is missing.
                'name' => 'second',
                'value' => 'second-value',
            ],
            [
                // Should be ignored when 'value' is missing.
                'name' => 'second',
                'title' => 'Second caption',
            ],
        ]);

        $this->assertSame($expected->attributes()['placeholders'], $actual->attributes()['placeholders']);
    }

    public function testStructure()
    {
        $editable = (new Editable('test'))
            ->titledAs('email_heading')
            ->usingType('text')
            ->asInline()
            ->withValue('Hello, {user}!')
            ->placeholder('user', 'User name', 'John');

        $expected = [
            'name' => 'test',
            'title' => 'email_heading',
            'type' => 'text',
            'inline' => true,
            'value' => 'Hello, {user}!',
            'placeholders' => [
                'user' => [
                    'title' => 'User name',
                    'value' => 'John',
                ],
            ],
        ];

        $this->assertSame($expected, $editable->attributes());
    }

    public function testJsonSerialize()
    {
        $editable = (new Editable('test'))
            ->titledAs('email_heading')
            ->usingType('text')
            ->asInline()
            ->withValue('Hello, {user}!')
            ->placeholder('user', 'User name', 'John');

        $expected = [
            'name' => 'test',
            'title' => 'email_heading',
            'type' => 'text',
            'inline' => true,
            'value' => 'Hello, {user}!',
            'placeholders' => [
                'user' => [
                    'title' => 'User name',
                    'value' => 'John',
                ],
            ],
        ];

        $this->assertSame(json_encode($expected), json_encode($editable));
    }

    public function attributesDataProvider(): array
    {
        return [
            [
                'editable' => (new Editable('test'))->titledAs('name'),
                'attribute' => 'title',
                'expected' => 'name',
            ],
            [
                'editable' => (new Editable('test'))->withValue('hello'),
                'attribute' => 'value',
                'expected' => 'hello',
            ],
            [
                'editable' => (new Editable('test'))->withOptions(['first' => 'first value', 'second' => 'second value']),
                'attribute' => 'options',
                'expected' => [
                    ['value' => 'first', 'label' => 'first value'],
                    ['value' => 'second', 'label' => 'second value'],
                ]
            ],
            [
                'editable' => (new Editable('test'))->withAttributes(['custom-attribute' => 'custom-value']),
                'attribute' => 'custom-attribute',
                'expected' => 'custom-value',
            ],
            [
                'editable' => (new Editable('test'))->uploadTo('/upload'),
                'attribute' => 'settings.upload_url',
                'expected' => '/upload',
            ],
            [
                'editable' => (new Editable('test'))->withHeaders(['X-CSRF-Token' => 'secret']),
                'attribute' => 'settings.headers',
                'expected' => ['X-CSRF-Token' => 'secret'],
            ],
        ];
    }
}
