<?php

namespace Tests;

use Giftd\Editor\Editable;

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
        $this->assertSame($expected, $editable->attributes()[$attribute]);
    }

    public function testPlaceholders()
    {
        $editable = (new Editable('test'))->placeholder('balance', 'Balance Value', 1337);
        $placeholder = $editable->attributes()['placeholders']['balance'];
        $expected = ['title' => 'Balance Value', 'value' => 1337];

        $this->assertSame($placeholder, $expected);
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
                'editable' => (new Editable('test'))->withOptions(['first', 'second']),
                'attribute' => 'options',
                'expected' => ['first', 'second'],
            ],
            [
                'editable' => (new Editable('test'))->withAttributes(['custom-attribute' => 'custom-value']),
                'attribute' => 'custom-attribute',
                'expected' => 'custom-value',
            ],
        ];
    }
}
