<?php

namespace Fesor\SymfonyValidationExtras;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class FixedJson extends Collection
{
    public function initializeNestedConstraints()
    {
        $this->expandConstraintsShortCuts();
        $this->expandOptionalFields();

        parent::initializeNestedConstraints();
    }

    private function expandConstraintsShortCuts()
    {
        $this->fields = array_map(function ($constraint) {
            if (is_string($constraint)) {
                return $this->expandConstraint($constraint);
            }

            return $constraint;
        }, $this->fields);
    }

    private function expandConstraint(string $constraint)
    {
        $constraint = mb_strtolower($constraint);

        $nullSafeValidator = new Assert\NotNull();
        if ('?' === mb_substr($constraint, 0, 1)) {
            $constraint = mb_substr($constraint, 1);
            $nullSafeValidator = null;
        }

        if ('null' === $constraint) {
            $nullSafeValidator = null;
        }

        $expandedConstraint = [
                'null'      => Assert\IsNull::class,
                'email'     => Assert\Email::class,
                'date'      => Assert\Date::class,
                'datetime'  => Assert\DateTime::class,
                'time'      => Assert\Time::class,
                'file'      => Assert\File::class,
                'image'     => Assert\Image::class,
                'url'       => Assert\Url::class,
            ][$constraint] ?? new Assert\Type($constraint);

        if (is_string($expandedConstraint)) {
            $expandedConstraint = new $expandedConstraint();
        }

        return array_values(array_filter([$nullSafeValidator, $expandedConstraint]));
    }

    private function expandOptionalFields()
    {
        $fields = [];
        foreach ($this->fields as $key => $constraint) {
            if ($constraint instanceof Assert\Required) {
                $fields[$key] = $constraint;

                continue;
            }

            if ('?' === mb_substr($key, -1)) {
                $fields[mb_substr($key, 0, -1)] = new Assert\Optional($constraint);
            } else {
                $fields[$key] = new Assert\Required($constraint);
            }
        }

        $this->fields = $fields;
    }
}
