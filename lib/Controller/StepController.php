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
    public function index(Folder $folder, string $step = ''): Response
    {
        $steps = $folder->getSteps();
        if (!isset($steps[$step])) {
            $step = array_key_first($steps);
        }

        foreach ($steps as $name => $dummy) {
            $parameters['steps'][$name] = $this->generateUrl('step_index', ['step' => $name]);
        }
        $parameters['step'] = $step;
        $parameters['steps'][$step] = '';
        $this->data->setEntity(new Entity(1));
        $repository = new Repository();
        $this->data->setPagination($repository->getPage(3, 50, 10));

        return $this->dataRender('step/'.$steps[$step], $parameters);
    }

    /**
     * listUrl.
     *
     * @param array<int,string> $actions
     */
    protected function listUrl(Data $data, array $actions): AbstractController
    {
        if ($data->isSetEntities()) {
            foreach ($data->getEntities() as $entity) {
                $id = $entity->getId();
                $parameters = ['id' => $id];
                foreach ($actions as $action) {
                    $data->addLink($action.'-'.$id, $action, $parameters);
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
                $data->addLink('first', '_page', ['page' => 1]);
                $data->addLink('previous', '_page', ['page' => $pagination->getCurrentPage() - 1]);
            }
            if ($pagination->hasNextPage()) {
                $last = $pagination->getLastPage();
                $data->addLink('Next', '_page', ['page' => $pagination->getCurrentPage() + 1]);
                $data->addLink('Last', '_page', ['page' => $last]);
            }
            if (($last = $pagination->getLastPage()) <= 20) {
                for ($i = 1; $i <= $last; ++$i) {
                    $data->addLink('page-'.$i, '_page', ['page' => $i]);
                }
            }
        } else {
            $data->addLink('index', '_page', ['page' => 1]);
        }

        return $this;
    }

    protected function crudUrl(Data $data): AbstractController
    {
        if ($data->isSetEntity()) {
            $id = $data->getEntity()->getId();
            if (0 !== $id) {
                $parameters = ['id' => $id];
                foreach (['_edit', '_show', '_delete'] as $action) {
                    $data->addLink($action, $action, $parameters);
                }
            }
        }
        foreach (['_new', '_index'] as $action) {
            $data->addLink($action, $action);
        }

        return $this;
    }

    protected function initUrl(Data $data): AbstractController
    {
        $this->listUrl($data, ['_show', '_edit']);
        $this->paginationUrl($data);
        $this->crudUrl($data);

        return $this;
    }
}
