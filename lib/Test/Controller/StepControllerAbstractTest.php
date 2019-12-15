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

namespace Mazarini\TestBundle\Test\Controller;

use Mazarini\TestBundle\Tool\Folder;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

abstract class StepControllerAbstractTest extends UrlControllerAbstractTest
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * testUrls.
     *
     * @dataProvider getUrls
     *
     * @param array<string,mixed> $parameters
     */
    public function testUrls(string $url, int $response = 200, string $method = 'GET', array $parameters = []): void
    {
        $this->client->request($method, $url, $parameters);

        $message = sprintf('The %s URL loads correctly.', $url);
        $this->assertSame(
            $response,
            $this->client->getResponse()->getStatusCode(),
            sprintf('The %s URL loads correctly.', $url)
        );
    }

    /**
     * getUrls.
     *
     * @return \Traversable<int,array>
     */
    public function getUrls(): \Traversable
    {
        $folder = new Folder();
        $steps = $folder->getSteps();
        foreach ($steps as $step => $dummy) {
            yield ['/'.$step.'.html'];
        }
    }
}
