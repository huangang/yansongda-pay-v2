<?php

namespace Huangang\YansongdaPayV2\Pay\Gateways\Alipay;

use Huangang\YansongdaPayV2\Pay\Contracts\GatewayInterface;
use Huangang\YansongdaPayV2\Pay\Exceptions\InvalidArgumentException;
use Huangang\YansongdaPayV2\Supports\Collection;

abstract class Gateway implements GatewayInterface
{
    /**
     * Mode.
     *
     * @var string
     */
    protected $mode;

    /**
     * Bootstrap.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->mode = Support::getInstance()->mode;
    }

    /**
     * Pay an order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $endpoint
     *
     * @return Collection
     */
    abstract public function pay($endpoint, array $payload);
}
