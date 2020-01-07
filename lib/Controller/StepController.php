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
    public function __construct(RequestStack $requestStack, Folder $folder, string $baseRoute = 'step')
    {
        $this->parameters['steps'] = $this->steps = $folder->getSteps();
        $this->folder = $folder;

        parent::__construct($requestStack, new UrlGenerator(), $baseRoute);

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

        $this->parameters['dataPagination'] = $dataPagination = new Data(new UrlGenerator(), 'page', 'index', '#page_index-3');
        $repository = new Repository();
        $dataPagination->setPagination($repository->getPage(3, 50, 10));
        $this->setListUrl($dataPagination, ['_show' => 'Show', '_edit' => 'Edit']);
        $this->setPaginationUrl($dataPagination);

        $this->parameters['dataCrud'] = $dataCrud = new Data(new UrlGenerator(), 'crud', 'show', '#crud_show-1');
        $dataCrud->setEntity(new Entity(1));
        $this->setCrudUrl($dataCrud);
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

        return $this->dataRender('step/'.$this->steps[$step].'/'.$this->pages[$page], $parameters);
    }

    /**
     * listUrl.
     *
     * @param array<string,string> $actions
     */
    protected function setListUrl(Data $data, array $actions): AbstractController
    {
        foreach ($data->getEntities() as $entity) {
            $id = $entity->getId();
            $parameters = ['id' => $id];
            foreach ($actions as $action => $label) {
                $data->addLink(trim($action, '_').'-'.$id, $data->generateUrl($action, $parameters), $label);
            }
        }

        return $this;
    }

    protected function setPaginationUrl(Data $data): AbstractController
    {
        $pagination = $data->getPagination();
        if ($pagination->hasPreviousPage()) {
            $data->addLink('first', $data->generateUrl('_index', ['page' => 1]), '1');
            $data->addLink('previous', $data->generateUrl('_index', ['page' => $pagination->getCurrentPage() - 1]), 'Previous');
        } else {
            $data->getLinks()->addLink(new Link('first', '#', '1'));
            $data->getLinks()->addLink(new Link('previous', '#', 'Previous'));
        }
        if ($pagination->hasNextPage()) {
            $last = $pagination->getLastPage();
            $data->addLink('next', $data->generateUrl('_index', ['page' => $pagination->getCurrentPage() + 1]), 'Next');
            $data->addLink('last', $data->generateUrl('_index', ['page' => $last]), (string) $last);
        } else {
            $data->getLinks()->addLink(new Link('next', '#', 'Next'));
            $data->getLinks()->addLink(new Link('last', '#', (string) $pagination->getLastPage()));
        }
        for ($i = 1; $i <= $pagination->getLastPage(); ++$i) {
            $data->addLink('page-'.$i, $data->generateUrl('_index', ['page' => $i]), (string) $i);
        }

        return $this;
    }

    protected function setCrudUrl(Data $data): AbstractController
    {
        if ($data->isSetEntity()) {
            $id = $data->getEntity()->getId();
            if (0 !== $id) {
                $parameters = ['id' => $id];
                foreach (['_edit' => 'Edit', '_show' => 'Show', '_delete' => 'Delete'] as $action => $label) {
                    $data->addLink(trim($action, '_'), $data->generateUrl($action, $parameters), $label);
                }
            }
        }
        $data->addLink('new', $data->generateUrl('_new', []), 'Create');
        $data->addLink('index', $data->generateUrl('_index', ['page' => 1]), 'List');

        return $this;
    }

    protected function initUrl(Data $data): AbstractController
    {
        $data->getLinks()->addLink(new Link('active', '', 'Active'));
        $data->getLinks()->addLink(new Link('disable', '#', 'Disable'));
        $data->getLinks()->addLink(new Link('current', '/Data/Links.html', 'Current'));
        $data->getLinks()->addLink(new Link('normal', '/normal', 'Normal'));

        return $this;
    }

    protected function initMenu(LinkTree $menu): AbstractController
    {
        foreach (array_keys($this->steps) as $step) {
            if ($step === $this->step) {
                $link = new LinkTree($step);
                $link->active();
                $menu[$step] = $link;
                foreach (array_keys($this->pages) as $page) {
                    if ($page === $this->page) {
                        $menu[$step][$page] = new Link($page, '');
                    } else {
                        $menu[$step][$page] = new Link($page, $this->generateUrl('step_index', ['step' => $step, 'page' => $page]));
                    }
                }
            } else {
                $menu[$step] = new Link($step, $this->generateUrl('step_home_step', ['step' => $step]));
            }
        }

        return $this;
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
}
