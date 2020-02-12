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
 * You should have received a copy of the GNU General Public License
 */

namespace Mazarini\TestBundle\Tool;

use Mazarini\TestBundle\Fake\Entity;
use Mazarini\TestBundle\Fake\Repository;
use Mazarini\TestBundle\Fake\UrlGenerator;
use Mazarini\ToolsBundle\Controller\CrudUrlTrait;
use Mazarini\ToolsBundle\Controller\ListUrlTrait;
use Mazarini\ToolsBundle\Controller\PageUrlTrait;
use Mazarini\ToolsBundle\Data\Data;
use Mazarini\ToolsBundle\Data\Link;
use Mazarini\ToolsBundle\Data\Links;
use Mazarini\ToolsBundle\Data\LinkTree;
use Mazarini\ToolsBundle\Pagination\Pagination;
use Mazarini\ToolsBundle\Pagination\PaginationInterface;

class Factory
{
    // Todo put in a sigle trait
    use CrudUrlTrait;
    use ListUrlTrait;
    use PageUrlTrait;

    protected function setUrl(Data $data): void
    {
        $this->setCrudUrl($data);
        $this->setListUrl($data);
        $this->setPageUrl($data);
        $this->setNewUrl($data);
    }

    // Todo put in a sigle trait (end)

    public function getUrlGenerator(): UrlGenerator
    {
        return new UrlGenerator();
    }

    /**
     * getRepository.
     */
    public function getRepository(): Repository
    {
        return new Repository();
    }

    public function getEntity(int $id = 0): Entity
    {
        return $this->getRepository()->find($id);
    }

    public function getLinks(string $name, int $count = 5): Links
    {
        $links = new Links('#'.$name.'-1');
        $name .= '-';
        for ($i = 1; $i <= $count; ++$i) {
            $key = $name.$i;
            $links->addLink(new Link($key, '#'.$key));
        }

        return $links;
    }

    public function getTree(string $label, string $name, int $count = 5): LinkTree
    {
        $tree = new LinkTree($name, $label);
        $name .= '-';
        for ($i = 1; $i <= $count; ++$i) {
            $key = $name.$i;
            $tree->addLink(new Link($key, '#'.$key));
        }

        return $tree;
    }

    public function getLinksData(): Data
    {
        $data = new Data($this->getUrlGenerator(), 'links', 'button', '/current');
        $data->addLink('normal', '/normal', 'Normal');
        $data->addLink('disable', '#', 'Disable');
        $data->addLink('active', '/current', 'Active');

        return $data;
    }

    public function getData(string $route = 'crud_page', ?Entity $entity = null, ?PaginationInterface $pagination = null): Data
    {
        [$baseRoute,$action] = explode('_', $route);

        if (null === $entity and null === $pagination) {
            $entity = $this->getEntity('new' === $action ? 0 : 1);
            $pagination = $this->getRepository()->getPage(3, 60);
        }

        $urlParameters = [];
        $urlGenerator = $this->getUrlGenerator();
        if ('new' === $action) {
            $urlParameters = [];
        } elseif ('page' === $action) {
            if ($pagination instanceof Pagination) {
                $urlParameters = ['page' => $pagination->getCurrentPage()];
            }
        } else {
            if ($entity instanceof Entity) {
                $urlParameters = ['id' => $entity->getId()];
            }
        }
        $url = $urlGenerator->generate($route, $urlParameters);

        $data = new Data($urlGenerator, $baseRoute, $action, $url);

        if ($pagination instanceof Pagination) {
            $data->setPagination($pagination);
        }

        if ($entity instanceof Entity) {
            $data->setEntity($entity);
        }

        $this->setUrl($data);

        return $data;
    }

    public function getCrudData(string $route = 'crud_show', int $id = 1): Data
    {
        [$baseRoute,$action] = explode('_', $route);
        if ('new' === $action) {
            $id = 0;
        }

        return $this->getData($route, $this->getEntity($id), null);
    }

    public function getPaginationData(string $route = 'crud_page', int $currentPage = 3, int $nbEntity = 60): Data
    {
        return $this->getData($route, null, $this->getRepository()->getPage($currentPage, $nbEntity));
    }
}
