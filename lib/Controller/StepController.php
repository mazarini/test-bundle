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

namespace Mazarini\TestBundle\Controller;

use Mazarini\TestBundle\Fake\Entity;
use Mazarini\TestBundle\Fake\Repository;
use Mazarini\TestBundle\Fake\UrlGenerator;
use Mazarini\TestBundle\Tool\Folder;
use Mazarini\ToolsBundle\Controller\AbstractController;
use Mazarini\ToolsBundle\Data\Data;
use Mazarini\ToolsBundle\Data\Links;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;

class StepController extends AbstractController
{
    /**
     * __construct.
     */
    public function __construct(RequestStack $requestStack, string $baseRoute = 'step')
    {
        parent::__construct($requestStack, new UrlGenerator(), $baseRoute);
        $this->parameters['symfony']['version'] = Kernel::VERSION;
        $this->parameters['php']['version'] = PHP_VERSION;
        $this->parameters['php']['extensions'] = get_loaded_extensions();
    }

    /**
     * @Route("/", name="step_home")
     */
    public function home(Folder $folder): Response
    {
        $step = array_key_first($folder->getSteps());

        return $this->redirectToRoute($this->data->getRoute('_index'), ['step' => $step]);
    }

    /**
     * @Route("/{step}.html", name="step_index")
     */
    public function index(Folder $folder, string $step): Response
    {
        $steps = $folder->getSteps();
        if (!isset($steps[$step])) {
            $currentUrl = $this->generateUrl('step_index', ['step' => $step]);
            $try = array_key_first($steps);
            $tryUrl = $this->generateUrl('step_index', ['step' => $try]);
            throw $this->createNotFoundException(sprintf('The page "%s" does not exist. Try <a href="%s">%s</a>', $currentUrl, $tryUrl, $tryUrl));
        }
        $this->data->setEntity(new Entity(1));
        $repository = new Repository();
        $this->data->setPagination($repository->getPage(3, 50, 10));

        $menu = new Links('', $this->generateUrl('step_index', ['step' => $step]), 'Main menu');
        $this->parameters['steps'] = $menu;
        foreach (array_keys($steps) as $name) {
            $menu->addLink($name, $this->generateUrl('step_index', ['step' => $name]), $name);
        }

        $list = new Links('', '#item-2', 'Main list');
        $this->parameters['list'] = $list;
        for ($i = 1; $i < 6; ++$i) {
            $list->addLink('item-'.$i, '#item-'.$i, 'Item-'.$i);
        }

        $sub2 = new Links('', '', 'Sub sub tree');
        for ($i = 1; $i < 6; ++$i) {
            $sub2->addLink('sub2-'.$i, '#sub2-'.$i, 'Sub2-'.$i);
        }
        $sub = new Links('', '', 'Sub tree');
        for ($i = 1; $i < 6; ++$i) {
            $sub->addLink('sub-'.$i, '#sub-'.$i, 'Sub-'.$i);
            if (3 === $i) {
                $sub->addLinks('sub2', $sub2);
            }
        }
        $tree = new Links('', '#tree', 'Main tree');
        $this->parameters['tree'] = $tree;
        for ($i = 1; $i < 6; ++$i) {
            $tree->addLink('item-'.$i, '#item-'.$i, 'Item-'.$i);
            if (4 === $i) {
                $tree->addLinks('sub', $sub);
            }
        }

        $parameters['step'] = $step;

        return $this->dataRender('step/'.$steps[$step], $parameters);
    }

    /**
     * listUrl.
     *
     * @param array<string,string> $actions
     */
    protected function listUrl(Data $data, array $actions): AbstractController
    {
        if ($data->isSetEntities()) {
            foreach ($data->getEntities() as $entity) {
                $id = $entity->getId();
                $parameters = ['id' => $id];
                foreach ($actions as $action => $label) {
                    $data->addLink($action.'-'.$id, $action, $parameters, $label);
                }
            }
        }

        return $this;
    }

    protected function PaginationUrl(Data $data): AbstractController
    {
        if ($data->isSetEntities()) {
            $pagination = $data->getPagination();
            if ($pagination->hasPreviousPage()) {
                $data->addLink('first', '_page', ['page' => 1], '1');
                $data->addLink('previous', '_page', ['page' => $pagination->getCurrentPage() - 1], 'Previous');
            } else {
                $data->getLinks()->addLink('first', '#', '1');
                $data->getLinks()->addLink('previous', '#', 'Previous');
            }
            if ($pagination->hasNextPage()) {
                $last = $pagination->getLastPage();
                $data->addLink('next', '_page', ['page' => $pagination->getCurrentPage() + 1], 'Next');
                $data->addLink('last', '_page', ['page' => $last], (string) $last);
            } else {
                $data->getLinks()->addLink('next', '#', 'Next');
                $data->getLinks()->addLink('last', '#', (string) $pagination->getLastPage());
            }
            if (($last = $pagination->getLastPage()) <= 20) {
                for ($i = 1; $i <= $last; ++$i) {
                    $data->addLink('page-'.$i, '_page', ['page' => $i], (string) $i);
                }
            }
        }

        return $this;
    }

    protected function crudUrl(Data $data): AbstractController
    {
        if ($data->isSetEntity()) {
            $id = $data->getEntity()->getId();
            if (0 !== $id) {
                $parameters = ['id' => $id];
                foreach (['_edit' => 'Edit', '_show' => 'Show', '_delete' => 'Delete'] as $action => $label) {
                    $data->addLink($action, $action, $parameters, $label);
                }
            }
        }
        $data->addLink('new', '_new', [], 'Create');
        $data->addLink('index', '_index', ['page' => 1], 'List');

        return $this;
    }

    protected function initUrl(Data $data): AbstractController
    {
        $data->getLinks()->addLink('active', '', 'Active');
        $data->getLinks()->addLink('disable', '#', 'Disable');
        $this->crudUrl($data);
        $this->paginationUrl($data);
        $this->listUrl($data, ['_show' => 'Show', '_edit' => 'Edit', '_delete' => 'Delete']);
        $data->getLinks()->setCurrentUrl('#step_page-'.$data->getPagination()->getCurrentPage());

        return $this;
    }
}
