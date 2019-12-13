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

namespace App\Controller;

use Mazarini\TestBundle\Controller\StepController as BaseController;
use Mazarini\TestBundle\Tool\Folder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/test")
 */
class UrlController extends BaseController
{
    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack, 'test');
    }

    /**
     * @Route("/", name="test_home")
     */
    public function home(Folder $folder, string $step = ''): Response
    {
        return parent::home($folder);
    }

    /**
     * @Route("/{step}.html", name="test_index")
     */
    public function index(Folder $folder, string $step = ''): Response
    {
        return parent::index($folder, $step);
    }
}
