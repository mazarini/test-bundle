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

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class HomeControllerAbstractTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;

    /**
     * @var int
     */
    protected $default;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->default = Response::HTTP_FOUND;
    }

    /**
     * testUrls.
     *
     * @dataProvider getUrls
     */
    public function testUrls(string $url, int $response = 0): void
    {
        if (0 === $response) {
            $response = $this->default;
        }
        $this->client->request('GET', $url);

        if (Response::HTTP_FOUND === $response && '' !== $url) {
            $response = Response::HTTP_MOVED_PERMANENTLY;
        }

        if (Response::HTTP_OK === $response) {
            $message = sprintf('The "%s" URL loads correctly.', $url);
        } else {
            $message = sprintf('The "%s" URL redirect correctly with code %d.', $url, $this->client->getResponse()->getStatusCode());
        }
        $this->assertSame($this->client->getResponse()->getStatusCode(), $response, $message);
    }

    /**
     * testUrls.
     *
     * @dataProvider getUrls
     */
    public function testSlashUrls(string $url, int $response = 0): void
    {
        if (0 === $response) {
            $response = $this->default;
        }
        $url .= '/';

        $this->client->request('GET', $url);

        $this->assertSame(
            $this->client->getResponse()->getStatusCode(),
            $response,
            sprintf('The "%s" URL redirect correctly with %d.', $url, $response)
        );
    }

    /**
     * getUrls.
     *
     * @return \Traversable<array>
     */
    abstract public function getUrls(): \Traversable;
}
