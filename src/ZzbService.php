<?php

namespace ZzbSdk;

use ZzbSdk\Exception\ZzbException;
use ZzbSdk\Model\DownloadFilmInfoRequest;
use ZzbSdk\Model\DownloadReportRecordRequest;
use ZzbSdk\Model\GetCinemaInfoRequest;
use ZzbSdk\Model\GetScreenInfoRequest;
use ZzbSdk\Model\LockSeatsInfoRequest;
use ZzbSdk\Model\QueryOrderInfoRequest;
use ZzbSdk\Model\QuerySessionInfoRequest;
use ZzbSdk\Model\QueryScreenSeatInfoRequest;
use ZzbSdk\Model\QuerySeatStatusInfoRequest;
use ZzbSdk\Model\RefundTicketRequest;
use ZzbSdk\Model\ReleaseSeatsInfoRequest;
use ZzbSdk\Model\ReportTicketRequest;
use ZzbSdk\Model\SubmitOrderRequest;
use ZzbSdk\Model\ZzbCinema;
use ZzbSdk\Model\ZzbCinemaScreen;
use ZzbSdk\Model\ZzbFilmPage;
use ZzbSdk\Model\ZzbLockSeatsResult;
use ZzbSdk\Model\ZzbQueryOrderResult;
use ZzbSdk\Model\ZzbRefundTicketResult;
use ZzbSdk\Model\ZzbReleaseSeatsResult;
use ZzbSdk\Model\ZzbResult;
use ZzbSdk\Model\ZzbScreenSeatInfo;
use ZzbSdk\Model\ZzbSeatStatusResult;
use ZzbSdk\Model\ZzbSessionPage;
use ZzbSdk\Model\ZzbSubmitOrderResult;
use ZzbSdk\Model\ZzbTicket;

/**
 * 专资办接口服务
 */
class ZzbService
{
    private Config $config;
    private ?\CurlHandle $curlHandle = null;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 票房数据上报
     *
     * @param ZzbTicket[] $ticketList 影票信息
     * @return ZzbResult
     */
    public function reportTicket(array $ticketList): ZzbResult
    {
        $appId = $this->resolveAppId();
        $request = ReportTicketRequest::create($appId, $this->config->channelCode, $this->config->version, $ticketList);
        $bodyData = $request->toArray();
        $signature = $this->sign($bodyData);

        $url = $this->config->reportUrl . '/reportTicket';

        $headers = ["X-Signature: $signature"];
        // The reportTicket endpoint expects the business payload under the data key.
        // Keep the signature in the request header and omit the query-style root fields.
        $response = $this->post($url, ['data' => $bodyData['data']], true, $headers);
        return ZzbResult::fromArray($response);
    }

    /**
     * 基于订单详情执行售票上报。
     *
     * @param string   $cinemaCode 影院编码
     * @param string   $lockOrderId 锁座订单号
     * @param string[] $ticketNos 指定上报票号，留空表示整单
     * @param string|null $operationDatetime 操作时间，留空时取订单时间
     * @return ZzbResult
     */
    public function reportTicketByLockOrderId(
        string $cinemaCode,
        string $lockOrderId,
        int $startNumberByDay,
        array $ticketNos = [],
        ?string $operationDatetime = null
    ): ZzbResult {
        $orderResult = $this->queryOrderInfo($cinemaCode, $lockOrderId);
        if (!$orderResult->isSuccess() || !$orderResult->data) {
            throw new ZzbException('查询订单详情失败，无法执行售票上报');
        }

        $tickets = $this->buildReportTicketsFromOrderDetail($orderResult->data, $startNumberByDay, $ticketNos, 1, $operationDatetime);

        return $this->reportTicket($tickets);
    }

    /**
     * 基于订单详情执行退票。
     *
     * @param string   $cinemaCode 影院编码
     * @param string   $lockOrderId 锁座订单号
     * @param string[] $ticketNos 指定退票票号，留空表示整单
     * @return ZzbResult<ZzbRefundTicketResult>
     */
    public function refundTicketByLockOrderId(string $cinemaCode, string $lockOrderId, array $ticketNos = []): ZzbResult
    {
        $orderResult = $this->queryOrderInfo($cinemaCode, $lockOrderId);
        if (!$orderResult->isSuccess() || !$orderResult->data) {
            throw new ZzbException('查询订单详情失败，无法执行退票');
        }

        $orderId = $orderResult->data->orderId ?? '';
        if ($orderId === '') {
            throw new ZzbException('订单详情缺少orderId，无法执行退票');
        }

        $ticketList = $this->buildRefundTicketsFromOrderDetail($orderResult->data, $ticketNos);

        return $this->refundTicket($cinemaCode, $orderId, $ticketList);
    }

    /**
     * 基于订单详情执行退票上报。
     *
     * @param string   $cinemaCode 影院编码
     * @param string   $lockOrderId 锁座订单号
     * @param string[] $ticketNos 指定上报票号，留空表示整单
     * @param string|null $operationDatetime 操作时间，留空时取订单时间
     * @return ZzbResult
     */
    public function refundReportTicketByLockOrderId(
        string $cinemaCode,
        string $lockOrderId,
        int $startNumberByDay,
        array $ticketNos = [],
        ?string $operationDatetime = null
    ): ZzbResult {
        $orderResult = $this->queryOrderInfo($cinemaCode, $lockOrderId);
        if (!$orderResult->isSuccess() || !$orderResult->data) {
            throw new ZzbException('查询订单详情失败，无法执行退票上报');
        }

        $tickets = $this->buildReportTicketsFromOrderDetail($orderResult->data, $startNumberByDay, $ticketNos, 2, $operationDatetime);

        return $this->reportTicket($tickets);
    }

    /**
     * 数据比对文件下载
     *
     * @param string $startShowDate 开始时间
     * @param string $endShowDate   结束时间
     * @return string 文件内容
     */
    public function downloadReportRecord(string $startShowDate, string $endShowDate): string
    {
        $request = DownloadReportRecordRequest::create($this->config->certId, $this->config->channelCode, $startShowDate, $endShowDate, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/data/downloadReportRecord";

        return $this->post($url, $request->toArray(), false); // Return raw body
    }

    /**
     * 影片信息下载
     *
     * @param string $cinemaCode       影院编码
     * @param string $startPublishDate 开始上映日期
     * @param string $endPublishDate   结束上映日期
     * @param int    $page             页码
     * @return ZzbResult<ZzbFilmPage>
     */
    public function downloadFilmInfo(string $cinemaCode, string $startPublishDate, string $endPublishDate, int $page): ZzbResult
    {
        $request = DownloadFilmInfoRequest::create($this->config->certId, $cinemaCode, $startPublishDate, $endPublishDate, $page, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/queryFilmInfo";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbFilmPage::fromArray($result->data);
        }

        return $result;
    }

    /**
     * 影院排片信息查询
     *
     * @param string      $cinemaCode 影院编码
     * @param string|null $startDate  开始日期，格式 yyyy-MM-dd
     * @param string|null $endDate    结束日期，格式 yyyy-MM-dd
     * @param int         $page       页码
     * @return ZzbResult<ZzbSessionPage>
     */
    public function querySessionInfo(string $cinemaCode, ?string $startDate = null, ?string $endDate = null, int $page = 1): ZzbResult
    {
        $appId = $this->resolveAppId();
        $request = QuerySessionInfoRequest::create($appId, $cinemaCode, $startDate, $endDate, $page, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/querySessionInfo";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbSessionPage::fromArray($result->data, $response['pageable'] ?? null);
        }

        return $result;
    }

    /**
     * 场次座位售出状态查询
     *
     * @param string   $cinemaCode 影院编码
     * @param string   $sessionCode 场次编码
     * @param string[] $seatStatus 过滤状态，默认 LOCKED/SOLD/UNAVAILABLE
     * @return ZzbResult<ZzbSeatStatusResult>
     */
    public function querySeatStatusInfo(string $cinemaCode, string $sessionCode, array $seatStatus = ['LOCKED', 'SOLD', 'UNAVAILABLE']): ZzbResult
    {
        $appId = $this->resolveAppId();
        $request = QuerySeatStatusInfoRequest::create($appId, $cinemaCode, $sessionCode, $seatStatus, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/querySeatStatusInfo";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbSeatStatusResult::fromArray($result->data);
        }

        return $result;
    }

    /**
     * 影厅座位信息查询
     *
     * @param string $cinemaCode 影院编码
     * @param string $screenCode 影厅编码
     * @return ZzbResult<ZzbScreenSeatInfo>
     */
    public function queryScreenSeatInfo(string $cinemaCode, string $screenCode): ZzbResult
    {
        $appId = $this->resolveAppId();
        $request = QueryScreenSeatInfoRequest::create($appId, $cinemaCode, $screenCode, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/queryScreenSeatInfo";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbScreenSeatInfo::fromArray($result->data);
        }

        return $result;
    }

    /**
     * 座位锁定
     *
     * @param string $cinemaCode 影院编码
     * @param string $sessionCode 场次编码
     * @param string $appLockId 业务锁座单号
     * @param array  $seatList 座位列表，每项至少包含 seatCode
     * @return ZzbResult<ZzbLockSeatsResult>
     */
    public function lockSeatsInfo(string $cinemaCode, string $sessionCode, string $appLockId, array $seatList): ZzbResult
    {
        $appId = $this->resolveAppId();
        $request = LockSeatsInfoRequest::create($appId, $cinemaCode, $sessionCode, $appLockId, $seatList, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/lockSeatsInfo";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbLockSeatsResult::fromArray($result->data);
        }

        return $result;
    }

    /**
     * 座位解锁
     *
     * @param string $cinemaCode 影院编码
     * @param string $lockOrderId 锁座订单号
     * @param int    $count 解锁座位数
     * @return ZzbResult<ZzbReleaseSeatsResult>
     */
    public function releaseSeatsInfo(string $cinemaCode, string $lockOrderId, int $count): ZzbResult
    {
        $appId = $this->resolveAppId();
        $request = ReleaseSeatsInfoRequest::create($appId, $cinemaCode, $lockOrderId, $count, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/releaseSeatsInfo";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbReleaseSeatsResult::fromArray($result->data);
        }

        return $result;
    }

    /**
     * 确认订单交易
     *
     * @param string $lockOrderId 锁座订单号
     * @param string $cinemaCode 影院编码
     * @param string $sessionCode 场次编码
     * @param array  $seatList 座位列表
     * @return ZzbResult<ZzbSubmitOrderResult>
     */
    public function submitOrder(string $lockOrderId, string $cinemaCode, string $sessionCode, array $seatList): ZzbResult
    {
        $appId = $this->resolveAppId();
        $request = SubmitOrderRequest::create($appId, $lockOrderId, $cinemaCode, $sessionCode, $seatList, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/submitOrder";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbSubmitOrderResult::fromArray($result->data);
        }

        return $result;
    }

    /**
     * 订单信息查询
     *
     * @param string $cinemaCode 影院编码
     * @param string $lockOrderId 锁座订单号
     * @return ZzbResult<ZzbQueryOrderResult>
     */
    public function queryOrderInfo(string $cinemaCode, string $lockOrderId): ZzbResult
    {
        $appId = $this->resolveAppId();
        $request = QueryOrderInfoRequest::create($appId, $cinemaCode, $lockOrderId, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/queryOrderInfo";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbQueryOrderResult::fromArray($result->data);
        }

        return $result;
    }

    /**
     * 退票
     *
     * @param string $cinemaCode 影院编码
     * @param string $orderId 订单号
     * @param array  $ticketList 票列表，每项至少包含 ticketNo
     * @return ZzbResult<ZzbRefundTicketResult>
     */
    public function refundTicket(string $cinemaCode, string $orderId, array $ticketList): ZzbResult
    {
        $appId = $this->resolveAppId();
        $request = RefundTicketRequest::create($appId, $cinemaCode, $orderId, $ticketList, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/refundTicket";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbRefundTicketResult::fromArray($result->data);
        }

        return $result;
    }

    /**
     * 影院信息下载
     *
     * @param string $cinemaCode 影院编码
     * @return ZzbResult<ZzbCinema>
     */
    public function getCinemaInfo(string $cinemaCode): ZzbResult
    {
        $appId = $this->resolveAppId();
        $request = GetCinemaInfoRequest::create($appId, $cinemaCode, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/queryCinemaInfo";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbCinema::fromArray($result->data);
        }

        return $result;
    }

    /**
     * 影厅信息下载
     *
     * @param string $cinemaCode 影院编码
     * @return ZzbResult<ZzbCinemaScreen>
     */
    public function getScreenInfo(string $cinemaCode): ZzbResult
    {
        $request = GetScreenInfoRequest::create($this->config->certId, $cinemaCode, $this->config->version);
        $request->signature = $this->sign($request->toArray());

        $url = $this->config->serviceUrl . "/queryScreenInfo";
        $response = $this->post($url, $request->toArray());
        $result = ZzbResult::fromArray($response);

        if ($result->data) {
            $result->data = ZzbCinemaScreen::fromArray($result->data);
        }

        return $result;
    }

    /**
     * 签名
     *
     * @param array $data 签名数据
     * @return string 签名结果
     */
    private function sign(array $data): string
    {
        $contentData = [];
        foreach ($data as $key => $value) {
            if ($key !== 'signature') {
                $contentData[$key] = $value;
            }
        }

        if ($this->config->interfaceKey) {
            // 现网 queryCinemaInfo/queryScreenInfo/queryFilmInfo 使用网络代售接口签名：
            // 将请求报文原文与 password、timestamp 作为同级字段，按键名升序序列化后做 SM3，再 Base64。
            $contentData['password'] = $this->config->interfaceKey;
            $contentData = $this->sortSigningData($contentData);
            $content = json_encode($contentData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);

            $digest = openssl_digest($content, 'sm3', true);
            if ($digest === false) {
                throw new ZzbException('SM3 摘要失败');
            }

            return base64_encode($digest);
        }

        $contentData = $this->sortSigningData($contentData);
        $content = json_encode($contentData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);

        // 尝试使用 OpenSSL 3 的 SM2 算法
        $privateKey = $this->loadPrivateKey();
        if (!$privateKey) {
            throw new ZzbException('无法加载私钥，请检查证书文件和密码');
        }

        // 检查私钥是否为 EC 密钥（SM2 使用椭圆曲线）
        $keyDetails = openssl_pkey_get_details($privateKey);
        if ($keyDetails['type'] !== OPENSSL_KEYTYPE_EC) {
            // 如果不是 EC 密钥，尝试使用 RSA SHA256
            $signature = '';
            $algo = OPENSSL_ALGO_SHA256;

            // 尝试 HMAC 签名 (针对 AppID 370100)
            // 如果私钥加载失败或不是标准私钥，尝试使用配置中的接口密钥
            // 假设接口密钥存储在 config 中，这里暂不实现

            if (!openssl_sign($content, $signature, $privateKey, $algo)) {
                throw new ZzbException('签名失败: ' . openssl_error_string());
            }
            openssl_free_key($privateKey);
            return urlencode(base64_encode($signature));
        }

        // 使用 SM2 算法签名（OpenSSL 3.0+）
        // SM2 签名需要指定曲线名称
        $signature = '';

        // OpenSSL 3 使用 EVP_PKEY_sign，需要指定 digest
        // 尝试使用 SM3 哈希算法
        if (!openssl_sign($content, $signature, $privateKey, 'SM3')) {
            // 如果 SM3 不支持，回退到 SHA256
            $signature = '';
            if (!openssl_sign($content, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                throw new ZzbException('签名失败: ' . openssl_error_string());
            }
        }

        openssl_free_key($privateKey);
        return urlencode(base64_encode($signature));
    }

    /**
     * 加载私钥
     * 支持 PEM 文件，如果是 PFX/P12 需要先转换
     */
    private function loadPrivateKey()
    {
        $certFile = $this->config->certFile;
        $password = $this->config->certFilePwd;

        // 尝试直接加载 PEM 私钥
        if (file_exists($certFile)) {
            $key = openssl_pkey_get_private("file://$certFile", $password);
            if ($key !== false) {
                return $key;
            }
        }

        // 如果是 PFX/P12 文件，尝试读取并转换
        if (file_exists($certFile) && (
            pathinfo($certFile, PATHINFO_EXTENSION) === 'pfx' ||
            pathinfo($certFile, PATHINFO_EXTENSION) === 'p12'
        )) {
            $pfxData = file_get_contents($certFile);
            if (openssl_pkcs12_read($pfxData, $certs, $password)) {
                if (isset($certs['pkey'])) {
                    $key = openssl_pkey_get_private($certs['pkey']);
                    if ($key !== false) {
                        return $key;
                    }
                }
            }
        }

        return false;
    }

    /**
     * 递归排序签名/请求数据，保持对象字段顺序稳定。
     */
    private function sortSigningData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->isAssoc($value) ? $this->sortSigningData($value) : array_map(
                    fn($item) => is_array($item) ? ($this->isAssoc($item) ? $this->sortSigningData($item) : $item) : $item,
                    $value
                );
            }
        }

        if ($this->isAssoc($data)) {
            ksort($data);
        }

        return $data;
    }

    private function isAssoc(array $data): bool
    {
        return array_keys($data) !== range(0, count($data) - 1);
    }

    private function resolveAppId(): string
    {
        $appId = $this->config->appId ?? $this->config->certId;
        if (!is_string($appId) || $appId === '') {
            throw new ZzbException('缺少 appId/certId 配置');
        }

        return $appId;
    }

    /**
     * @param ZzbQueryOrderResult $orderDetail
     * @param string[] $ticketNos
     * @return ZzbTicket[]
     */
    private function buildReportTicketsFromOrderDetail(
        ZzbQueryOrderResult $orderDetail,
        int $startNumberByDay,
        array $ticketNos = [],
        int $operation = 1,
        ?string $operationDatetime = null
    ): array {
        $ticketNos = array_values(array_filter(array_map('strval', $ticketNos)));
        $tickets = [];
        $cinemaCode = $orderDetail->cinemaCode ?? '';

        foreach ($orderDetail->ticketList as $item) {
            $ticketNo = $item->ticketNo ?? '';
            if ($ticketNo === '') {
                continue;
            }
            if ($ticketNos && !in_array($ticketNo, $ticketNos, true)) {
                continue;
            }

            $ticket = new ZzbTicket();
            $ticket->numberByDay = $startNumberByDay + count($tickets);
            $ticket->parentChannelCode = $cinemaCode;
            $ticket->childChannelCode = '00000000';
            $ticket->ticketNo = $ticketNo;
            $ticket->cinemaCode = $cinemaCode;
            $ticket->screenCode = $orderDetail->screenCode ?? '';
            $ticket->seatCode = $item->seatCode ?? '';
            $ticket->filmCode = $orderDetail->filmCode ?? '';
            $ticket->sessionCode = $orderDetail->sessionCode ?? '';
            $ticket->sessionDatetime = $orderDetail->sessionDatetime ?? '';
            $ticket->ticketPrice = (float) ($item->ticketPrice ?? 0);
            $ticket->screenServiceFee = (float) ($item->screenServiceFee ?? 0);
            $ticket->netServiceFee = (float) ($item->netServiceFee ?? 0);
            $ticket->saleChannelCode = $this->config->channelCode;
            $ticket->operation = $operation;
            if ($operationDatetime !== null && $operationDatetime !== '') {
                $ticket->operationDatetime = $operationDatetime;
            } elseif ($operation === 2) {
                $ticket->operationDatetime = date('Y-m-d H:i:s');
            } else {
                $ticket->operationDatetime = $orderDetail->orderTime ?? $orderDetail->sessionDatetime ?? '';
            }
            $tickets[] = $ticket;
        }

        if (!$tickets) {
            throw new ZzbException('未匹配到可上报的ticketNo');
        }

        return $tickets;
    }

    /**
     * @param ZzbQueryOrderResult $orderDetail
     * @param string[] $ticketNos
     * @return array<int,array{ticketNo:string}>
     */
    private function buildRefundTicketsFromOrderDetail(ZzbQueryOrderResult $orderDetail, array $ticketNos = []): array
    {
        $ticketNos = array_values(array_filter(array_map('strval', $ticketNos)));
        $tickets = [];

        foreach ($orderDetail->ticketList as $item) {
            $ticketNo = $item->ticketNo ?? '';
            if ($ticketNo === '') {
                continue;
            }
            if ($ticketNos && !in_array($ticketNo, $ticketNos, true)) {
                continue;
            }

            $tickets[] = ['ticketNo' => $ticketNo];
        }

        if (!$tickets) {
            throw new ZzbException('未匹配到可退票的ticketNo');
        }

        return $tickets;
    }

    /**
     * 发送 POST 请求
     *
     * @param string $url
     * @param array $data
     * @param bool $decodeJson 是否解码 JSON 响应
     * @param array $headers HTTP headers
     * @return mixed
     */
    private function post(string $url, array $data, bool $decodeJson = true, array $headers = [])
    {
        if ($this->config->interfaceKey) {
            $data = $this->sortSigningData($data);
        }

        $ch = $this->initCurl();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION));

        $defaultHeaders = ['Content-Type: application/json'];
        $mergedHeaders = array_merge($defaultHeaders, $headers);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $mergedHeaders);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new ZzbException('CURL Error: ' . curl_error($ch));
        }

        if ($httpCode >= 400) {
            throw new ZzbException("HTTP Error: $httpCode - $response");
        }

        if ($decodeJson) {
            return json_decode($response, true);
        }

        return $response;
    }

    /**
     * 发送 GET 请求
     *
     * @param string $url
     * @return array
     */
    private function get(string $url): array
    {
        $ch = $this->initCurl();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new ZzbException('CURL Error: ' . curl_error($ch));
        }

        if ($httpCode >= 400) {
            throw new ZzbException("HTTP Error: $httpCode - $response");
        }

        return json_decode($response, true);
    }

    /**
     * 初始化 cURL 句柄
     */
    private function initCurl(): \CurlHandle
    {
        if ($this->curlHandle) {
            return $this->curlHandle;
        }

        $ch = curl_init();

        // 默认直连专资办接口，避免继承本机 http_proxy/https_proxy/all_proxy。
        if ($this->config->proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->config->proxy);
        } else {
            curl_setopt($ch, CURLOPT_PROXY, '');
        }

        // SSL 配置
        // 如果是测试环境（通过配置判断），可以禁用 SSL 验证
        // 这里暂时禁用验证以进行测试，生产环境应启用
        $isTestMode = strpos($this->config->serviceUrl, '218.241.227.141') !== false;
        if ($isTestMode) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }

        // 设置客户端证书
        if ($this->config->certFile && $this->config->certFilePwd) {
            $certPath = $this->config->certFile;
            $certPwd = $this->config->certFilePwd;

            // 检查文件扩展名，如果是 PFX/P12 需要转换为 PEM
            $ext = strtolower(pathinfo($certPath, PATHINFO_EXTENSION));
            if ($ext === 'pfx' || $ext === 'p12') {
                // 读取 PFX 文件
                $pfxData = file_get_contents($certPath);
                if ($pfxData === false) {
                    throw new ZzbException("无法读取证书文件: $certPath");
                }

                // 解析 PFX 获取证书和私钥
                if (!openssl_pkcs12_read($pfxData, $certs, $certPwd)) {
                    throw new ZzbException("无法解析 PFX 证书: " . openssl_error_string());
                }

                // 临时保存 PEM 格式的证书和私钥
                $tempDir = sys_get_temp_dir();
                $certPemPath = $tempDir . '/zzb_cert_' . uniqid() . '.pem';
                $keyPemPath = $tempDir . '/zzb_key_' . uniqid() . '.pem';

                file_put_contents($certPemPath, $certs['cert']);
                file_put_contents($keyPemPath, $certs['pkey']);

                curl_setopt($ch, CURLOPT_SSLCERT, $certPemPath);
                curl_setopt($ch, CURLOPT_SSLKEY, $keyPemPath);
                curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $certPwd);

                // 注册关闭函数清理临时文件
                register_shutdown_function(function () use ($certPemPath, $keyPemPath) {
                    @unlink($certPemPath);
                    @unlink($keyPemPath);
                });
            } else {
                // 假设是 PEM 格式
                // 检查文件内容以确定是私钥、证书还是合并文件
                $content = file_get_contents($certPath, false, null, 0, 1024);
                $hasKey = strpos($content, 'PRIVATE KEY') !== false; // 包括 ENCRYPTED PRIVATE KEY, RSA PRIVATE KEY 等
                $hasCert = strpos($content, 'BEGIN CERTIFICATE') !== false;

                if ($hasKey && $hasCert) {
                    // 合并文件（私钥 + 证书）：直接使用
                    curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
                    curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $certPwd);
                } elseif ($hasKey) {
                    // 仅私钥文件：尝试查找同目录下的证书文件
                    $keyPath = $certPath;
                    $dir = dirname($certPath);
                    $certNames = ['certificate_clean.pem', 'certificate.pem', 'cert.pem'];
                    $foundCert = null;
                    foreach ($certNames as $name) {
                        $potentialCert = $dir . '/' . $name;
                        if (file_exists($potentialCert)) {
                            $foundCert = $potentialCert;
                            break;
                        }
                    }

                    if ($foundCert) {
                        curl_setopt($ch, CURLOPT_SSLCERT, $foundCert);
                        curl_setopt($ch, CURLOPT_SSLKEY, $keyPath);
                        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $certPwd);
                        // 证书文件通常未加密，不设置 CURLOPT_SSLCERTPASSWD
                    } else {
                        // 未找到证书文件，回退到仅使用私钥（可能无法建立双向认证）
                        curl_setopt($ch, CURLOPT_SSLCERT, $keyPath);
                        curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $certPwd);
                    }
                } else {
                    // 仅证书文件
                    curl_setopt($ch, CURLOPT_SSLCERT, $certPath);
                    curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $certPwd);
                }
            }
        }

        // 设置 CA 证书
        if ($this->config->trustFile) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->config->trustFile);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $this->curlHandle = $ch;
        return $ch;
    }

    public function __destruct()
    {
        if ($this->curlHandle) {
            curl_close($this->curlHandle);
        }
    }
}
