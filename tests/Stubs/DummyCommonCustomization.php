<?php

namespace Tests\Stubs;

use Giftd\Editor\Customization;

abstract class DummyCommonCustomization extends Customization
{
    public function editables()
    {
        return array_merge($this->commonEditables(), $this->concreteEditables());
    }

    public function commonEditables()
    {
        return [
            $this->group('first-group', 'First Group', function () {
                return [
                    $this->editable('first-editable', 'text', 'First Editable')
                        ->withValue('foo')
                        ->asInline(false),

                    $this->editable('second-editable', 'text', 'Second Editable')
                        ->withValue('bar')
                        ->asInline(false),
                ];
            }),
        ];
    }

    public function build()
    {
        $this->buildCommonData();
        $this->buildConcreteData();
    }

    public function buildCommonData()
    {
        //
    }

    abstract public function concreteEditables();

    abstract public function buildConcreteData();
}
