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

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

//class HomeControllerTest extends HomeControllerAbstractTest
class HomeControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser;
     */
    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @dataProvider getUrls
     */
    public function testUrls(string $url, bool $connected = false): void
    {
        $response = Response::HTTP_MOVED_PERMANENTLY;
        $this->client->request('GET', $url);

        $this->assertSame(
            $response,
            $this->client->getResponse()->getStatusCode(),
            sprintf('The %s URL redirect correctly with status %d (really : %d).', $url, $response, $this->client->getResponse()->getStatusCode())
        );

        $url .= '/';
        $response = Response::HTTP_MOVED_PERMANENTLY;
        $this->client->request('GET', $url);

        $this->assertSame(
            $response,
            $this->client->getResponse()->getStatusCode(),
            sprintf('The %s URL redirect correctly with status %d (really : %d).', $url, $response, $this->client->getResponse()->getStatusCode())
        );
    }

    /**
     * getUrls.
     *
     * @return \Traversable<int,array>
     */
    public function getUrls(): \Traversable
    {
        yield [''];
        yield ['/step'];
        yield ['/step/System'];
    }
}
