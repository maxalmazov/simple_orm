<?php

declare(strict_types=1);

namespace App\Entity\Collection;

class FacultyCollection extends AbstractEntityCollection
{
    public function targetClass() {
        return __CLASS__;
    }
}