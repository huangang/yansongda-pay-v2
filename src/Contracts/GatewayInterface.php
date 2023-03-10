<?php

namespace Huangang\YansongdaPayV2\Pay\Contracts;

use Symfony\Component\HttpFoundation\Response;
use Huangang\YansongdaPayV2\Supports\Collection;

interface GatewayInterface
{
    /**
     * Pay an order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $endpoint
     *
     * @return Collection|Response
     */
    public function pay($endpoint, array $payload);
}
