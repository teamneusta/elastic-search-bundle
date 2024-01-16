<?php

declare(strict_types=1);

namespace Neusta\ElasticSearchBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NeustaElasticSearchBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
