<?php

namespace Huangang\YansongdaPayV2\Pay\Gateways\Wechat;

use Huangang\YansongdaPayV2\Pay\Exceptions\GatewayException;
use Huangang\YansongdaPayV2\Pay\Exceptions\InvalidArgumentException;
use Huangang\YansongdaPayV2\Pay\Exceptions\InvalidSignException;
use Huangang\YansongdaPayV2\Pay\Gateways\Wechat;
use Huangang\YansongdaPayV2\Supports\Collection;

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
