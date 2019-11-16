<?php

/*
 * Copyright (C) 2019 Mazarini <mazarini@protonmail.com>.
 * This file is part of mazarini/test-bundle.
 *
 * mazarini/test-bundle is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * mazarini/test-bundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License
 */

namespace App\Tests\Entity;

use Mazarini\TestBundle\Fake\Entity;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    public function testGet(): void
    {
        $entity = new Entity(1);
        for ($i = 1; $i < 10; ++$i) {
            $getCol = 'getCol'.$i;
            $this->assertSame($entity->$getCol(), 'row 01 / col 0'.$i);
        }
    }
}
