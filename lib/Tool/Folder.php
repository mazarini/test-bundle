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

namespace Mazarini\TestBundle\Tool;

class Folder
{
    private $testStep;

    /**
     * __construct.
     */
    public function __construct(string $root = '')
    {
        if ('' === $root) {
            $this->testStep = \dirname(__DIR__, 2);
        } else {
            $this->testStep = $root;
        }
        if (false === mb_strpos($this->testStep, '/templates/')) {
            $this->testStep .= '/templates/step';
        }
    }

    /**
     * getSteps.
     */
    public function getSteps(): array
    {
        $dirs = glob($this->testStep.'/??-*\.html\.twig');
        $steps = [];
        if (\is_array($dirs)) {
            foreach ($dirs as $dir) {
                $steps[mb_substr(basename($dir, '.html.twig'), 3)] = basename($dir);
            }
        }

        return $steps;
    }
}
