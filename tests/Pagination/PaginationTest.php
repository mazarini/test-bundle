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

namespace App\Tests\Pagination;

use Mazarini\TestBundle\Fake\Pagination;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    public function testZeroEntities(): void
    {
        $pagination = new Pagination(1, 0, 10);
        $this->assertSame(\count($pagination->GetEntities()), 0);
    }

    /**
     * testOnePage.
     *
     * @dataProvider providePage
     */
    public function testEntitiesCount(int $current, int $count, int $first, int $last): void
    {
        $pagination = new Pagination($current, 25, 10);
        $entities = $pagination->GetEntities();
        $this->assertSame(\count($entities), $count);
        $this->assertSame($entities[$first]->getId(), $first);
        $this->assertSame($entities[$last]->getId(), $last);
    }

    public function providePage(): \Traversable
    {
        yield [1, 10, 01, 10];
        yield [2, 10, 11, 20];
        yield [3, 05, 21, 25];
    }
}
