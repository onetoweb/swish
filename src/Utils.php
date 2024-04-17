<?php

namespace Onetoweb\Swish;

use Ramsey\Uuid\Uuid;

final class Utils
{
    /**
     * @return string
     */
    public static function Uuid4Hex(): string
    {
        return strtoupper((string) Uuid::uuid4()->getHex());
    }
}
