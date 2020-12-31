<?php

/*
 * Copyright (C) 2019-2020 Mazarini <mazarini@protonmail.com>.
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
 * You should have received a copy of the GNU General Public License.
 */

namespace Mazarini\ToolsBundle\Tree;

/**
 * @template-extends \ArrayIterator<string, Node>
 */
class Node extends \ArrayIterator
{
    protected Node $parent;
    protected string $key;

    /**
     * add a node.
     *
     * @return self<string,Node>
     */
    public function add(self $node): self
    {
        $node->setParent($this);
        $key = $this->getChildKey();
        $this[] = $node;

        return $this;
    }

    public function hasNodes(): int
    {
        return 0 === $this->count();
    }

    public function getCurrent(): self
    {
        return $parent->getCurrent();
    }

    public function isCurrent(self $current): bool
    {
        if ($current === $this) {
            return true;
        }
        foreach ($this as $node) {
            if (true === $node->isCurrent($current)) {
                return true;
            }
        }

        return false;
    }

    protected function getKey()
    {
        return (string) ($this->count() + 1);
    }
}
