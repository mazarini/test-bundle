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

namespace Mazarini\TestBundle\Twig;

use Mazarini\TestBundle\Factory\LinkFactory;
use Mazarini\ToolsBundle\Data\Links;
use Mazarini\ToolsBundle\Data\LinkTree;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LinkExtension extends AbstractExtension
{
    /**
     * @var LinkFactory
     */
    protected $linkFactory;

    /**
     * Constructor.
     */
    public function __construct(LinkFactory $linkFactory)
    {
        $this->linkFactory = $linkFactory;
    }

    /**
     * getFunctions.
     *
     * @return array<int,TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('linksSample', [$this, 'getLinksSample']),
            new TwigFunction('treeSample', [$this, 'getTreeSample']),
        ];
    }

    /**
     * getLinksSample.
     */
    public function getLinksSample(): Links
    {
        return $this->linkFactory->getLinksSample();
    }

    /**
     * getLinksSample.
     */
    public function getTreeSample(): LinkTree
    {
        return $this->linkFactory->getTreeSample();
    }
}
