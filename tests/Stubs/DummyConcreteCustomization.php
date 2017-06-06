<?php

namespace Tests\Stubs;

class DummyConcreteCustomization extends DummyCommonCustomization
{
    protected $disabledEditables = ['first-editable'];

    public function concreteEditables()
    {
        return [
            $this->group('concrete-group', 'Concrete Group', function () {
                return [
                    $this->editable('concrete-editable', 'text', 'Concrete Editable')
                        ->withValue('baz')
                        ->asInline(false),
                ];
            }),
        ];
    }

    public function buildConcreteData()
    {
        //
    }

    public function expectedConcrete()
    {
        return [
            [
                'id' => 'first-group',
                'title' => 'First Group',
                'settings' => [
                    'second-editable' => [
                        'title' => 'Second Editable',
                        'type' => 'text',
                        'inline' => false,
                        'value' => 'bar',
                    ],
                ],
            ],
            [
                'id' => 'concrete-group',
                'title' => 'Concrete Group',
                'settings' => [
                    'concrete-editable' => [
                        'title' => 'Concrete Editable',
                        'type' => 'text',
                        'inline' => false,
                        'value' => 'baz',
                    ],
                ],
            ],
        ];
    }
}
