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

use Mazarini\ToolsBundle\Pagination\Pagination;
use Mazarini\ToolsBundle\Pagination\PaginationInterface;

class Repository
{
    public function findPage(int $currentPage = 1, int $totalCount = 60, int $pageSize = 10): PaginationInterface
    {
        /*
         * No result
         */
        if (0 === $totalCount) {
            return new Pagination(new \ArrayIterator([]), $currentPage, $totalCount, $pageSize);
        }
        /**
         * Compute pages.
         */
        $currentPage = Pagination::CURRENT_PAGE($currentPage, $pageSize, $totalCount);
        /**
         * Start position
         * ie first id for test.
         */
        $first = ($currentPage - 1) * $pageSize + 1;
        /**
         * Last position
         * ie last id for test.
         */
        $last = min($first + $pageSize - 1, $totalCount);
        /**
         * Create fake result remplacing database acces.
         */
        $entities = [];
        for ($i = $first; $i <= $last; ++$i) {
            $entities[$i] = new Entity($i);
        }

        return new Pagination(new \ArrayIterator($entities), $currentPage, $totalCount, $pageSize);
    }
}
