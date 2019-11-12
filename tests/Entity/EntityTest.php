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

use Mazarini\TestBundle\Entity\Fake;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    /**
     * testNewEntity.
     */
    public function testNewEntity(): void
    {
        $entity = new Fake();
        $this->assertSame($entity->getId(), 0);
        $this->assertTrue($entity->isNew());
        for ($i = 1; $i < 10; ++$i) {
            $getCol = 'getCol'.$i;
            $this->assertSame($entity->$getCol(), 'row 00 / col 0'.$i);
        }
    }

    /**
     * testEntity.
     */
    public function testEntity(): void
    {
        $entity = new Fake();
        $entity->setId(1);
        $this->assertSame($entity->getId(), 1);
        $this->assertTrue(!$entity->isNew());
    }
}
