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

namespace Mazarini\TestBundle\Fake;

use Mazarini\ToolsBundle\Pagination\EntitiesTrait;
use Mazarini\ToolsBundle\Pagination\PageTrait;
use Mazarini\ToolsBundle\Pagination\PaginationInterface;

class Pagination implements PaginationInterface
{
    protected const PAGE_SIZE = 10;
    /**
     * var $entities array.
     */
    protected $result = [];

    /**
     * var $count int.
     */
    protected $count = 0;

    protected const SIZE = 10;

    use PageTrait;
    use EntitiesTrait;

    public function __construct(int $currentPage, int $count, int $pageSize = self::PAGE_SIZE)
    {
        $this->currentPage = $currentPage;
        $this->count = $count;
        $this->pageSize = $pageSize;
        $this->initPage();
        $this->setEntities();
    }

    public function getIterator(): array
    {
        if (0 === $this->count) {
            return [];
        }

        $start = ($this->currentPage - 1) * self::PAGE_SIZE + 1;
        $end = $start + self::PAGE_SIZE;
        if ($end > $this->count) {
            $end = $this->count + 1;
        }

        $entities = [];
        for ($i = $start; $i < $end; ++$i) {
            $entities[] = new Entity($i);
        }

        return $entities;
    }

    public function count(): int
    {
        return $this->count;
    }
}
