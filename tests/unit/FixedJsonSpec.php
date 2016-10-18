<?php

namespace unit\Fesor\SymfonyValidationExtras;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Constraints;

class FixedJsonSpec extends ObjectBehavior
{
    function it_expands_short_constraint_declarations()
    {
        $this->beConstructedWith([
            'date' => 'date',
            'datetime' => 'datetime',
            'time' => 'time',
            'url' => 'url',
            'email' => 'email',
            'file' => 'file',
            'image' => 'image',
            'custom_type' => 'type'
        ]);

        $this->fields->shouldBeLike([
            'date' => $this->requiredAndNotNull(new Constraints\Date()),
            'datetime' => $this->requiredAndNotNull(new Constraints\DateTime()),
            'time' => $this->requiredAndNotNull(new Constraints\Time()),
            'url' => $this->requiredAndNotNull(new Constraints\Url()),
            'email' => $this->requiredAndNotNull(new Constraints\Email()),
            'file' => $this->requiredAndNotNull(new Constraints\File()),
            'image' => $this->requiredAndNotNull(new Constraints\Image()),
            'custom_type' => $this->requiredAndNotNull(new Constraints\Type('type'))
        ]);
    }

    function it_expands_optional_keys()
    {
        $this->beConstructedWith([
            'foo?' => 'string',
            'bar??' => 'string',
            'buz?' => new Constraints\Required(new Constraints\NotNull())
        ]);

        $this->fields->shouldBeLike([
            'foo' => $this->optionalAndNotNull(new Constraints\Type('string')),
            'bar?' => $this->optionalAndNotNull(new Constraints\Type('string')),
            'buz?' => new Constraints\Required(new Constraints\NotNull()),
        ]);
    }

    function it_handles_null_safty()
    {
        $this->beConstructedWith([
            'foo' => '?string',
            'nill' => 'null'
        ]);

        $this->fields->shouldBeLike([
            'foo' => new Constraints\Required([new Constraints\Type('string')]),
            'nill' => new Constraints\Required([new Constraints\IsNull()])
        ]);
    }

    private function requiredAndNotNull(Constraint $constraint)
    {
        return new Constraints\Required([
            new Constraints\NotNull(),
            $constraint
        ]);
    }

    private function optionalAndNotNull(Constraint $constraint)
    {
        return new Constraints\Optional([
            new Constraints\NotNull(),
            $constraint
        ]);
    }
}
