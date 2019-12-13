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

namespace App\Tests\Repository;

use Mazarini\TestBundle\Fake\Repository;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * @var Repository
     */
    public $repository;

    public function setUp(): void
    {
        $this->repository = new Repository();
    }

    public function testZeroEntities(): void
    {
        $pagination = $this->repository->getPage(1, 0, 10);
        $entities = $pagination->getEntities();
        $this->assertSame(\count($entities), 0);
    }

    /**
     * testOnePage.
     *
     * @dataProvider provideCount
     */
    public function testEntitiesCount(int $current, int $count): void
    {
        $pagination = $this->repository->getPage($current, 25, 10);
        $entities = $pagination->getEntities();
        $this->assertSame(\count($entities), $count);
    }

    /**
     * testOnePage.
     *
     * @dataProvider provideId
     */
    public function testEntitiesId(int $current, int $first, int $last): void
    {
        $pagination = $this->repository->getPage($current, 25, 10);
        $entities = $pagination->getEntities();
        $this->assertSame($entities[0]->getId(), $first);
        $this->assertSame($entities[$last - $first]->getId(), $last);
    }

    /**
     * provideCount.
     *
     * @return \Traversable<mixed,array>
     */
    public function provideCount(): \Traversable
    {
        // [current,count]
        yield [1, 10];
        yield [2, 10];
        yield [3, 05];
    }

    /**
     * provideId.
     *
     * @return \Traversable<mixed,array>
     */
    public function provideId(): \Traversable
    {
        // [current,first,last]
        yield [-1, 1, 10];
        yield [0, 1, 10];
        yield [1, 1, 10];
        yield [2, 11, 20];
        yield [3, 21, 25];
        yield [4, 21, 25];
    }
}
