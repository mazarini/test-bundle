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
use Mazarini\TestBundle\Fake\Pagination;
use Mazarini\TestBundle\Tool\Folder;
use Mazarini\ToolsBundle\Controller\ControllerAbstract;
use Mazarini\ToolsBundle\Data\Data;
use Mazarini\ToolsBundle\Href\Hrefs;
use Mazarini\ToolsBundle\Href\Link;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;

class StepController extends ControllerAbstract
{
    public function __construct(RequestStack $requestStack, Hrefs $hrefs, Data $data)
    {
        parent::__construct($requestStack, $hrefs, $data);
        $this->parameters['symfony']['version'] = Kernel::VERSION;
        $this->parameters['php']['version'] = PHP_VERSION;
        $this->parameters['php']['extensions'] = get_loaded_extensions();
    }

    /**
     * @Route("/", name="step_INDEX")
     * @Route("/{step}.html", name="step_index")
     */
    public function index(Folder $folder, $step = '')
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
        $parameters['entity'] = new Entity(1);
        $parameters['pagination'] = new Pagination(3, 50, 10);

        return $this->dataRender('step/'.$steps[$step], $parameters);
    }

    protected function InitUrl(Data $data): ControllerAbstract
    {
        $data->getHrefs()['current'] = new Link('');
        $data->getHrefs()['disabled'] = new Link('#');
        $this->addUrl($data->getHrefs(), 'INDEX');

        return $this;
    }

    protected function addUrl(Hrefs $hrefs, string $name, array $parameters = [], string $complement = null): ControllerAbstract
    {
        if (null === $complement) {
            $complement = $name;
        }
        $hrefs->addLink($complement, '#'.$complement);

        return $this;
    }
}
