<?php

namespace Huangang\YansongdaPayV2\Pay\Gateways\Alipay;

use Huangang\YansongdaPayV2\Pay\Contracts\GatewayInterface;
use Huangang\YansongdaPayV2\Pay\Events;
use Huangang\YansongdaPayV2\Pay\Exceptions\GatewayException;
use Huangang\YansongdaPayV2\Pay\Exceptions\InvalidConfigException;
use Huangang\YansongdaPayV2\Pay\Exceptions\InvalidSignException;
use Huangang\YansongdaPayV2\Supports\Collection;

class TransferGateway implements GatewayInterface
{
    /**
     * Pay an order.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param string $endpoint
     *
     * @throws GatewayException
     * @throws InvalidConfigException
     * @throws InvalidSignException
     */
    public function pay($endpoint, array $payload): Collection
    {
        $payload['method'] = 'alipay.fund.trans.uni.transfer';
        $payload['sign'] = Support::generateSign($payload);

        Events::dispatch(new Events\PayStarted('Alipay', 'Transfer', $endpoint, $payload));

        return Support::requestApi($payload);
    }

    /**
     * Find.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @param $order
     */
    public function find($order): array
    {
        return [
            'method' => 'alipay.fund.trans.order.query',
            'biz_content' => json_encode(is_array($order) ? $order : ['out_biz_no' => $order]),
        ];
    }
}
