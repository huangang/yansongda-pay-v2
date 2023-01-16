<?php

namespace YansongdaV2\Pay\Gateways\Wechat;

use YansongdaV2\Pay\Exceptions\GatewayException;
use YansongdaV2\Pay\Exceptions\InvalidArgumentException;
use YansongdaV2\Pay\Exceptions\InvalidSignException;
use YansongdaV2\Pay\Gateways\Wechat;
use YansongdaV2\Supports\Collection;

class MiniappGateway extends MpGateway
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
        $payload['appid'] = Support::getInstance()->miniapp_id;

        if (Wechat::MODE_SERVICE === $this->mode) {
            $payload['sub_appid'] = Support::getInstance()->sub_miniapp_id;
            $this->payRequestUseSubAppId = true;
        }

        return parent::pay($endpoint, $payload);
    }
}
