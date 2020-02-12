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
 * You should have received a copy of the GNU General Public License
 */

namespace App\Tests\Tool;

use Mazarini\TestBundle\Tool\Folder;
use PHPUnit\Framework\TestCase;

class FolderTest extends TestCase
{
    /**
     * testNewEntity.
     */
    public function testSteps(): void
    {
        $folder = new Folder();
        $steps = $folder->getSteps();
        $this->assertTrue(\count($steps) > 0);

        $step = array_key_first($steps);
        $this->assertSame($step, mb_substr($steps[$step], 3));

        $pages = $folder->getPages($steps[$step]);
        $this->assertTrue(\count($pages) > 0);

        $page = array_key_first($pages);
        $this->assertSame($page.'.html.twig', mb_substr($pages[$page], 3));

        $this->assertTrue(file_exists('templates/step/'.$steps[$step].'/'.$pages[$page]));
    }
}
