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

use Mazarini\TestBundle\Fake\Repository;
use Mazarini\TestBundle\Fake\UrlGenerator;
use Mazarini\TestBundle\Tool\Factory;
use Mazarini\TestBundle\Tool\Folder;
use Mazarini\ToolsBundle\Controller\AbstractController;
use Mazarini\ToolsBundle\Data\Data;
use Mazarini\ToolsBundle\Data\Link;
use Mazarini\ToolsBundle\Data\Links;
use Mazarini\ToolsBundle\Data\LinkTree;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;

class StepController extends AbstractController
{
    /**
     * @var Folder
     */
    protected $folder;

    /**
     * @var Factory
     */
    protected $fakeFactory;

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
        $this->fakeFactory = $fakeFactory;

        parent::__construct($requestStack, new UrlGenerator());
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

        return $this->redirectToRoute('step_index', ['step' => $step, 'page' => array_key_first($this->pages)]);
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
            if ($step === $this->step) {
                $link = new LinkTree($step);
                $link->active();
                $menu[$step] = $link;
                foreach (array_keys($this->pages) as $page) {
                    if ($page === $this->page) {
                        $link[$page] = new Link($page, '');
                    } else {
                        $link[$page] = new Link($page, $this->generateUrl('step_index', ['step' => $step, 'page' => $page]));
                    }
                }
            } else {
                $menu[$step] = new Link($step, $this->generateUrl('step_home_step', ['step' => $step]));
            }
        }
    }

    protected function getLinks(string $name, int $count = 5): Links
    {
        $links = new Links('#'.$name.'-1');
        $name .= '-';
        for ($i = 1; $i <= $count; ++$i) {
            $key = $name.$i;
            $links->addLink(new Link($key, '#'.$key));
        }

        return $links;
    }

    protected function getTree(string $label, string $name, int $count = 5): LinkTree
    {
        $tree = new LinkTree($name, $label);
        $name .= '-';
        for ($i = 1; $i <= $count; ++$i) {
            $key = $name.$i;
            $tree->addLink(new Link($key, '#'.$key));
        }

        return $tree;
    }

    protected function getCrudData(int $id): Data
    {
        $data = new Data(new UrlGenerator(), 'crud', 'show', sprintf('#crud_show-%d', $id));
        $data->setEntity((new Repository())->Find($id));
        $this->setUrl($data);

        return $data;
    }

    protected function getPaginationData(int $pageCourante, int $nbEntity): Data
    {
        $data = new Data(new UrlGenerator(), 'crud', 'page', sprintf('#crud_page-%d', $pageCourante));
        $repository = new Repository();
        $data->setPagination($repository->getPage($pageCourante, $nbEntity));
        $this->setUrl($data);

        return $data;
    }

    protected function beforeAction(string $action): void
    {
        $this->parameters['steps'] = $this->steps = $this->folder->getSteps();
    }

    protected function afterAction(string $action): void
    {
        $this->data->getLinks()->addLink(new Link('active', '', 'Active'));
        $this->data->getLinks()->addLink(new Link('disable', '#', 'Disable'));
        $this->data->getLinks()->addLink(new Link('current', '/Data/Links.html', 'Current'));
        $this->data->getLinks()->addLink(new Link('normal', '/normal', 'Normal'));
        $this->parameters['symfony']['version'] = Kernel::VERSION;
        $this->parameters['php']['version'] = PHP_VERSION;
        $this->parameters['php']['extensions'] = get_loaded_extensions();

        $this->parameters['list'] = $this->getLinks('item', 7);
        $this->parameters['list']['item-2'] = new Link('item-2', '#', 'Disable');

        $this->parameters['tree'] = $tree = $this->getTree('Tree', 'item', 5);
        $tree['item-1'] = $item1 = $this->getTree('Item-1', 'item-1', 2);
        $item1['item-1-1'] = $this->getTree('Item-1-1', 'item-1-1', 3);
        $item1['item-1-2'] = $this->getTree('Item-1-2', 'item-1-2', 2);
        $tree['item-2'] = $this->getTree('Item-2', 'item-2', 2);
        $tree['item-4'] = $this->getTree('Item-4', 'item-4', 2);

        $this->parameters['dataPagination'] = $this->fakeFactory->getPaginationData();
        $this->parameters['dataCrud'] = $this->fakeFactory->getCrudData();
    }

    protected function beforeRender(string $action): void
    {
    }
}
