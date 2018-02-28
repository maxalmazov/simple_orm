<?php

namespace App\Entity\Collection;

class FacultyCollection extends AbstractEntityCollection
{
    public function targetClass() {
        return __CLASS__;
    }
}