<?php

namespace YansongdaV2\Pay\Gateways\Wechat;

use YansongdaV2\Pay\Events;
use YansongdaV2\Pay\Exceptions\GatewayException;
use YansongdaV2\Pay\Exceptions\InvalidArgumentException;
use YansongdaV2\Pay\Exceptions\InvalidSignException;
use YansongdaV2\Supports\Collection;

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
