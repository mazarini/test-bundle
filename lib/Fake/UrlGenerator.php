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

namespace Mazarini\TestBundle\Fake;

class UrlGenerator extends UrlGeneratorOriginal
{
    /**
     * generate.
     *
     * @param array<string,mixed> $parameters
     *
     * @return string
     */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH)
    {
        if ('FAKE_' === mb_substr($name, 0, 5)) {
            $url = '#'.$name;
            foreach ($parameters as $key => $value) {
                $url .= '-'.$key.'='.(string) $value;
            }

            return $url;
        }

        return parent::generate($name, $parameters, $referenceType);
    }
}
