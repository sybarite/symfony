<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Iterator;

use Symfony\Component\Finder\Glob;

/**
 * FilenameFilterIterator filters files by patterns (a regexp, a glob, or a string).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FilenameFilterIterator extends MultiplePcreFilterIterator
{

    /**
     * Filters the iterator values.
     *
     * @return Boolean true if the value should be kept, false otherwise
     */
    public function accept()
    {
        // should at least match one rule
        if ($this->matchRegexps) {
            $match = false;
            foreach ($this->matchRegexps as $regex) {
                if (preg_match($regex, $this->getFilename())) {
                    $match = true;
                    break;
                }
            }
        } else {
            $match = true;
        }

        // should at least not match one rule to exclude
        if ($this->noMatchRegexps) {
            $exclude = false;
            foreach ($this->noMatchRegexps as $regex) {
                if (preg_match($regex, $this->getFilename())) {
                    $exclude = true;
                    break;
                }
            }
        } else {
            $exclude = false;
        }

        return $match && !$exclude;
    }

    /**
     * Converts glob to regexp.
     *
     * PCRE patterns are left unchanged.
     * Glob strings are transformed with Glob::toRegex().
     *
     * @param string $str Pattern: glob or regexp
     *
     * @return string regexp corresponding to a given glob or regexp
     */
    protected function toRegex($str)
    {
        if (preg_match('/^([^a-zA-Z0-9\\\\]).+?\\1[ims]?$/', $str)) {
            return $str;
        }

        return Glob::toRegex($str);
    }
}
