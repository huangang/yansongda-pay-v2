<?php

namespace Huangang\YansongdaPayV2\Pay\Gateways\Wechat;

use Huangang\YansongdaPayV2\Pay\Events;
use Huangang\YansongdaPayV2\Pay\Exceptions\GatewayException;
use Huangang\YansongdaPayV2\Pay\Exceptions\InvalidArgumentException;
use Huangang\YansongdaPayV2\Pay\Exceptions\InvalidSignException;
use Huangang\YansongdaPayV2\Supports\Collection;

class PosGateway extends Gateway
{
    /**
     * Pay an order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $endpoint
     *
     * @throws GatewayException
     * @throws InvalidArgumentException
     * @throws InvalidSignException
     */
    public function pay($endpoint, array $payload): Collection
    {
        unset($payload['trade_type'], $payload['notify_url']);

        $payload['sign'] = Support::generateSign($payload);

        Events::dispatch(new Events\PayStarted('Wechat', 'Pos', $endpoint, $payload));

        return Support::requestApi('pay/micropay', $payload);
    }

    /**
     * Get trade type config.
     *
     * @author yansongda <me@yansongda.cn>
     */
    protected function getTradeType(): string
    {
        return 'MICROPAY';
    }
}
