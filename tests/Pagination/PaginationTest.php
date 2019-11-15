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
    /**
     * testPages.
     */
    public function testPages(): void
    {
        $pagination = new Pagination(1, 0, 10);
        $this->assertSame($pagination->getLastPage(), 0);
        $this->assertSame($pagination->getCurrentPage(), 0);
        $this->assertTrue(!$pagination->HasToPaginate());

        $pagination = new Pagination(0, 1, 10);
        $this->assertSame($pagination->getLastPage(), 1);
        $this->assertSame($pagination->getCurrentPage(), 1);
        $this->assertTrue(!$pagination->HasToPaginate());

        $pagination = new Pagination(3, 20, 10);
        $this->assertSame($pagination->getLastPage(), 2);
        $this->assertSame($pagination->getCurrentPage(), 2);
        $this->assertTrue($pagination->HasToPaginate());

        $pagination = new Pagination(1, 21, 10);
        $this->assertSame($pagination->getLastPage(), 3);
        $this->assertSame($pagination->getCurrentPage(), 1);
        $this->assertTrue($pagination->HasToPaginate());
    }

    public function testNavigation(): void
    {
        $pagination = new Pagination(1, 50, 10);
        $this->assertTrue(!$pagination->HasPreviousPage());
        $this->assertTrue($pagination->HasNextPage());

        $pagination = new Pagination(3, 50, 10);
        $this->assertTrue($pagination->HasPreviousPage());
        $this->assertTrue($pagination->HasNextPage());
        $this->assertSame($pagination->getFirstPage(), 1);
        $this->assertSame($pagination->getPreviousPage(), 2);
        $this->assertSame($pagination->getCurrentPage(), 3);
        $this->assertSame($pagination->getNextPage(), 4);
        $this->assertSame($pagination->getLastPage(), 5);

        $pagination = new Pagination(5, 50, 10);
        $this->assertTrue($pagination->HasPreviousPage());
        $this->assertTrue(!$pagination->HasNextPage());
    }

    public function testEntitiesCount(): void
    {
        $pagination = new Pagination(1, 0, 10);
        $this->assertSame(\count($pagination->GetEntities()), 0);

        $pagination = new Pagination(1, 25, 10);
        $this->assertSame(\count($pagination->GetEntities()), 10);

        $pagination = new Pagination(2, 25, 10);
        $this->assertSame(\count($pagination->GetEntities()), 10);

        $pagination = new Pagination(3, 25, 10);
        $this->assertSame(\count($pagination->GetEntities()), 5);
    }
}
