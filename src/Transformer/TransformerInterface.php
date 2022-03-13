<?php

namespace LBHounslow\Bartec\Transformer;

use LBHounslow\Bartec\Exception\TransformationException;

interface TransformerInterface
{
    /**
     * @param mixed $value
     * @return string
     * @throws TransformationException
     */
    public function transform($value);
}