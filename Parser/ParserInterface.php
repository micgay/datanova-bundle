<?php

namespace Laposte\DatanovaBundle\Parser;

interface ParserInterface
{
    /**
     * @param string $dataset
     *
     * @return false|array
     */
    public function parse($dataset);
}
