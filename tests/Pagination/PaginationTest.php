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

use Mazarini\TestBundle\Pagination\Fake;
use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    /**
     * testNewEntity.
     */
    public function testPages(): void
    {
        $pagination = new Fake(1, 0, 10);
        $this->assertSame($pagination->getLastPage(), 0);
        $this->assertSame($pagination->getCurrentPage(), 0);
        $this->assertTrue(!$pagination->HasToPaginate());

        $pagination = new Fake(0, 1, 10);
        $this->assertSame($pagination->getLastPage(), 1);
        $this->assertSame($pagination->getCurrentPage(), 1);
        $this->assertTrue(!$pagination->HasToPaginate());

        $pagination = new Fake(3, 20, 10);
        $this->assertSame($pagination->getLastPage(), 2);
        $this->assertSame($pagination->getCurrentPage(), 2);
        $this->assertTrue($pagination->HasToPaginate());

        $pagination = new Fake(1, 21, 10);
        $this->assertSame($pagination->getLastPage(), 3);
        $this->assertSame($pagination->getCurrentPage(), 1);
        $this->assertTrue($pagination->HasToPaginate());
    }

    public function testNavigation(): void
    {
        $pagination = new Fake(1, 50, 10);
        $this->assertTrue(!$pagination->HasPreviousPage());
        $this->assertTrue($pagination->HasNextPage());

        $pagination = new Fake(3, 50, 10);
        $this->assertTrue($pagination->HasPreviousPage());
        $this->assertTrue($pagination->HasNextPage());
        $this->assertSame($pagination->getFirstPage(), 1);
        $this->assertSame($pagination->getPreviousPage(), 2);
        $this->assertSame($pagination->getCurrentPage(), 3);
        $this->assertSame($pagination->getNextPage(), 4);
        $this->assertSame($pagination->getLastPage(), 5);

        $pagination = new Fake(5, 50, 10);
        $this->assertTrue($pagination->HasPreviousPage());
        $this->assertTrue(!$pagination->HasNextPage());
    }

    public function testEntity(): void
    {
        $pagination = new Fake(1, 0, 10);
        $this->assertSame(\count($pagination->GetEntities()), 0);

        $pagination = new Fake(1, 25, 10);
        $this->assertSame(\count($pagination->GetEntities()), 10);

        $pagination = new Fake(1, 25, 10);
        $this->assertSame(\count($pagination->GetEntities()), 10);

        $pagination = new Fake(3, 25, 10);
        $this->assertSame(\count($pagination->GetEntities()), 5);
    }
}
