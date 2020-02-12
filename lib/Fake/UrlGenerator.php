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

namespace Mazarini\TestBundle\Fake;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * generate.
     *
     * @param string              $name
     * @param array<string,mixed> $parameters
     * @param int                 $referenceType
     *
     * @return string
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        return trim('#'.$name.'-'.implode('-', $parameters), '-');
    }

    /**
     * setStrictRequirements.
     *
     * @return void
     */
    public function setStrictRequirements(?bool $enabled)
    {
    }

    /**
     * Returns whether to throw an exception on incorrect parameters.
     * Null means the requirements check is deactivated completely.
     *
     * @return bool|null
     */
    public function isStrictRequirements()
    {
        return true;
    }

    /**
     * setContext.
     *
     * @return void
     */
    public function setContext(RequestContext $context)
    {
    }

    /**
     * getContext.
     *
     * @return RequestContext
     */
    public function getContext()
    {
        return new RequestContext();
    }
}
