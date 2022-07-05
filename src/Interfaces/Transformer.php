<?php

namespace Tatter\Visits\Interfaces;

interface Transformer
{
    public static function transform(array $data): array;
}
