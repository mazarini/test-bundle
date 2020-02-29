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

namespace Mazarini\TestBundle\Controller;

use Mazarini\TestBundle\Tool\Factory;
use Mazarini\TestBundle\Tool\Folder;
use Mazarini\ToolsBundle\Controller\CrudTrait;
use Mazarini\ToolsBundle\Data\Link;
use Mazarini\ToolsBundle\Data\LinkTree;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;

class StepController extends TestControllerAbstract
{
    use CrudTrait;

    /**
     * @var Folder
     */
    protected $folder;

    /**
     * @var array<string,string>
     */
    protected $steps = [];

    /**
     * @var string
     */
    protected $step = '';

    /**
     * @var array<string,string>
     */
    protected $pages = [];

    /**
     * @var string
     */
    protected $page = '';

    /**
     * __construct.
     */
    public function __construct(RequestStack $requestStack, Factory $fakeFactory, Folder $folder)
    {
        $this->folder = $folder;
        parent::__construct($requestStack, $fakeFactory);
    }

    /**
     * @Route("/", name="step_home")
     */
    public function home(): Response
    {
        $step = array_key_first($this->steps);
        if (null === $step) {
            $step = '';
        }

        return $this->homeStep($step);
    }

    /**
     * @Route("/{step}", name="step_home_step")
     */
    public function homeStep(string $step): Response
    {
        if (!isset($this->steps[$step])) {
            $currentUrl = $this->generateUrl('step_home_step', ['step' => $step]);
            $tryUrl = $this->generateUrl('step_home_step', ['step' => array_key_first($this->steps)]);
            throw $this->createNotFoundException(sprintf('The step "%s" does not exist. Try <a href="%s">%s</a>', $currentUrl, $tryUrl, $tryUrl));
        }

        $this->pages = $this->folder->getPages($this->steps[$step]);

        return $this->redirectToRoute('step_index', ['step' => $step, 'page' => array_key_first($this->pages)], Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * @Route("/{step}/{page}.html", name="step_index")
     */
    public function index(Folder $folder, string $step, string $page): Response
    {
        if (!isset($this->steps[$step])) {
            $currentUrl = $this->generateUrl('step_home_step', ['step' => $step]);
            $tryUrl = $this->generateUrl('step_home_step', ['step' => array_key_first($this->steps)]);
            throw $this->createNotFoundException(sprintf('The step "%s" does not exist. Try <a href="%s">%s</a>', $currentUrl, $tryUrl, $tryUrl));
        }

        $parameters['pages'] = $this->pages = $this->folder->getPages($this->steps[$step]);

        if (!isset($this->pages[$page])) {
            $currentUrl = $this->generateUrl('step_index', ['step' => $step, 'page' => $page]);
            $tryUrl = $this->generateUrl('step_index', ['step' => $step, 'page' => array_key_first($this->pages)]);
            throw $this->createNotFoundException(sprintf('The page "%s" does not exist. Try <a href="%s">%s</a>', $currentUrl, $tryUrl, $tryUrl));
        }

        $parameters['step'] = $this->step = $step;
        $parameters['page'] = $this->page = $page;

        return $this->dataRender($this->steps[$step].'/'.$this->pages[$page], $parameters);
    }

    protected function setMenu(LinkTree $menu): void
    {
        foreach (array_keys($this->steps) as $step) {
            $link = new LinkTree($step);
            $menu[$step] = $link;
            if ($step === $this->step) {
                $link->active();
                $pages = $this->pages;
            } else {
                $pages = $this->folder->getPages($this->steps[$step]);
            }
            foreach (array_keys($pages) as $page) {
                if (($page === $this->page) && ($step === $this->step)) {
                    $link[$page] = new Link($page, '');
                } else {
                    $link[$page] = new Link($page, $this->generateUrl('step_index', ['step' => $step, 'page' => $page]));
                }
            }
        }
    }

    protected function beforeAction(string $action): void
    {
        $this->parameters['steps'] = $this->steps = $this->folder->getSteps();
    }

    protected function afterAction(string $action): void
    {
        $this->parameters['symfony']['version'] = Kernel::VERSION;
        $this->parameters['php']['version'] = PHP_VERSION;
        $this->parameters['php']['extensions'] = get_loaded_extensions();

        $this->parameters['tree'] = $tree = $this->fakeFactory->getTree('Tree', 'item', 5);
        $tree['item-1'] = $item1 = $this->fakeFactory->getTree('Item-1', 'item-1', 2);
        $item1['item-1-1'] = $this->fakeFactory->getTree('Item-1-1', 'item-1-1', 3);
        $item1['item-1-2'] = $this->fakeFactory->getTree('Item-1-2', 'item-1-2', 2);
        $tree['item-2'] = $this->fakeFactory->getTree('Item-2', 'item-2', 2);
        $tree['item-4'] = $this->fakeFactory->getTree('Item-4', 'item-4', 2);

        $this->parameters['list'] = $this->fakeFactory->getLinks('item', 7);
        $this->parameters['list']['item-4'] = new Link('item-4', '#', 'Disable');

        $this->parameters['dataLinks'] = $this->fakeFactory->getLinksData();
        $this->parameters['dataPagination'] = $this->fakeFactory->getPaginationData();
        $this->parameters['dataCrud'] = $this->fakeFactory->getCrudData();
    }

    protected function beforeRender(string $action): void
    {
    }
}
