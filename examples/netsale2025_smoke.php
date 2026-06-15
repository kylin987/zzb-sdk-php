<?php

use ZzbSdk\Config;
use ZzbSdk\Exception\ZzbException;
use ZzbSdk\Model\ZzbTicket;
use ZzbSdk\Signer\VstkSignerInterface;
use ZzbSdk\ZzbService;

require __DIR__ . '/../vendor/autoload.php';

function main(): void
{
    $options = getopt('', [
        'all',
        'dry-run',
        'send-report',
        'download-record',
        'confirm-submit-test-data',
        'vstk-signer-url:',
        'start-date:',
        'end-date:',
        'number-by-day:',
        'ticket-no:',
        'help',
    ]);

    if (isset($options['help'])) {
        printHelp();
        return;
    }

    $baseDir = getenv('NETSALE_BASE_DIR') ?: getcwd();
    $channelCode = getenv('NETSALE_CHANNEL_CODE') ?: '12345678';
    $certId = getenv('NETSALE_CERT_ID') ?: $channelCode;
    $reportUrl = getenv('NETSALE_REPORT_URL') ?: 'https://panda.zgdypw.cn:8087/report/report';
    $serviceUrl = getenv('NETSALE_SERVICE_URL') ?: 'https://panda.zgdypw.cn:8085/service';
    $certFile = getenv('NETSALE_CERT_FILE') ?: $baseDir . '/client_cert.pem';
    $keyFile = getenv('NETSALE_KEY_FILE') ?: $baseDir . '/client_key.pem';
    $trustFile = getenv('NETSALE_TRUST_FILE') ?: $baseDir . '/zzb_rootcert.pem';
    $startDate = $options['start-date'] ?? getenv('NETSALE_START_DATE') ?: date('Y-m-d', strtotime('-1 day'));
    $endDate = $options['end-date'] ?? getenv('NETSALE_END_DATE') ?: $startDate;
    $numberByDay = (int) ($options['number-by-day'] ?? getenv('NETSALE_NUMBER_BY_DAY') ?: 1);
    $ticketNo = $options['ticket-no'] ?? getenv('NETSALE_TICKET_NO') ?: '130904010Ba0102';
    $signerUrl = $options['vstk-signer-url'] ?? getenv('NETSALE_VSTK_SIGNER_URL') ?: '';

    $sendReport = isset($options['all']) || isset($options['send-report']);
    $downloadRecord = isset($options['all']) || isset($options['download-record']);
    $dryRun = isset($options['dry-run']) || (!$sendReport && !$downloadRecord);

    printHeader('netsale2025 smoke');
    printConfig([
        'mode' => Config::MODE_NETSALE_2025,
        'reportUrl' => $reportUrl,
        'serviceUrl' => $serviceUrl,
        'channelCode' => $channelCode,
        'certId' => $certId,
        'certFile' => $certFile,
        'keyFile' => $keyFile,
        'trustFile' => $trustFile,
        'dryRun' => $dryRun ? 'yes' : 'no',
    ]);
    checkRequiredFiles([$certFile, $keyFile, $trustFile]);
    printCertificateSummary($certFile, 'client certificate');
    printCertificateSummary($trustFile, 'trust root');

    $signer = $dryRun ? new DryRunVstkSigner() : null;
    if (!$dryRun && $downloadRecord) {
        if ($signerUrl === '') {
            throw new RuntimeException('download-record requires --vstk-signer-url or NETSALE_VSTK_SIGNER_URL');
        }
        $signer = new HttpVstkSigner($signerUrl);
    }

    $serviceClass = $dryRun ? DryRunZzbService::class : ZzbService::class;
    $service = new $serviceClass(new Config([
        'mode' => Config::MODE_NETSALE_2025,
        'reportUrl' => $reportUrl,
        'serviceUrl' => $serviceUrl,
        'channelCode' => $channelCode,
        'certId' => $certId,
        'certFile' => $certFile,
        'keyFile' => $keyFile,
        'trustFile' => $trustFile,
        'vstkSigner' => $signer,
    ]));

    if ($dryRun || $sendReport) {
        if (!$dryRun && !isset($options['confirm-submit-test-data'])) {
            throw new RuntimeException('send-report submits test ticket data; add --confirm-submit-test-data to continue');
        }

        printHeader($dryRun ? 'dry-run reportTicket sale' : 'send reportTicket sale');
        printResult($service->reportTicket([buildTicket($channelCode, $ticketNo, $numberByDay, 1)]));

        printHeader($dryRun ? 'dry-run reportTicket refund' : 'send reportTicket refund');
        printResult($service->reportTicket([buildTicket($channelCode, $ticketNo, $numberByDay + 1, 2)]));
    }

    if ($dryRun || $downloadRecord) {
        printHeader($dryRun ? 'dry-run downloadReportRecord' : 'downloadReportRecord');
        $content = $service->downloadReportRecord($startDate, $endDate);
        if (!$dryRun) {
            saveDownloadContent($content, $baseDir, $startDate, $endDate);
        }
    }

    printHeader('done');
}

function buildTicket(string $channelCode, string $ticketNo, int $numberByDay, int $operation): ZzbTicket
{
    $ticket = new ZzbTicket();
    $ticket->numberByDay = $numberByDay;
    $ticket->parentChannelCode = '00000000';
    $ticket->childChannelCode = '00000000';
    $ticket->ticketNo = $ticketNo;
    $ticket->cinemaCode = getenv('NETSALE_CINEMA_CODE') ?: '13090401';
    $ticket->screenCode = getenv('NETSALE_SCREEN_CODE') ?: '0000000000000001';
    $ticket->seatCode = getenv('NETSALE_SEAT_CODE') ?: '88888888010010011101';
    $ticket->filmCode = getenv('NETSALE_FILM_CODE') ?: '000000252022';
    $ticket->sessionCode = getenv('NETSALE_SESSION_CODE') ?: 'SE00001234567890';
    $ticket->sessionDatetime = getenv('NETSALE_SESSION_DATETIME') ?: date('Y-m-d H:i:s', strtotime('+1 hour'));
    $ticket->ticketPrice = (float) (getenv('NETSALE_TICKET_PRICE') ?: 56.00);
    $ticket->screenServiceFee = (float) (getenv('NETSALE_SCREEN_SERVICE_FEE') ?: 0.00);
    $ticket->netServiceFee = (float) (getenv('NETSALE_NET_SERVICE_FEE') ?: 0.00);
    $ticket->saleChannelCode = $channelCode;
    $ticket->operation = $operation;
    $ticket->operationDatetime = getenv('NETSALE_OPERATION_DATETIME') ?: date('Y-m-d H:i:s');

    return $ticket;
}

function checkRequiredFiles(array $files): void
{
    foreach ($files as $file) {
        if (!is_file($file)) {
            throw new RuntimeException("missing file: {$file}");
        }
    }
}

function printCertificateSummary(string $file, string $label): void
{
    $raw = file_get_contents($file);
    $parsed = $raw === false ? false : openssl_x509_parse($raw);
    if (!is_array($parsed)) {
        echo "[{$label}] unable to parse {$file}\n";
        return;
    }

    $subject = formatDn($parsed['subject'] ?? []);
    $issuer = formatDn($parsed['issuer'] ?? []);
    $from = isset($parsed['validFrom_time_t']) ? date('Y-m-d H:i:s', $parsed['validFrom_time_t']) : '';
    $to = isset($parsed['validTo_time_t']) ? date('Y-m-d H:i:s', $parsed['validTo_time_t']) : '';

    echo "[{$label}] {$file}\n";
    echo "  subject: {$subject}\n";
    echo "  issuer: {$issuer}\n";
    echo "  valid: {$from} -> {$to}\n";
}

function formatDn(array $dn): string
{
    $parts = [];
    foreach ($dn as $key => $value) {
        $parts[] = $key . '=' . (is_array($value) ? implode(',', $value) : $value);
    }

    return implode(', ', $parts);
}

function printConfig(array $config): void
{
    foreach ($config as $key => $value) {
        echo str_pad($key, 14) . ': ' . $value . "\n";
    }
}

function printHeader(string $title): void
{
    echo "\n== {$title} ==\n";
}

function printResult($result): void
{
    if (is_object($result) && method_exists($result, 'isSuccess')) {
        echo json_encode([
            'success' => $result->isSuccess(),
            'code' => $result->code ?? null,
            'status' => $result->status ?? null,
            'traceId' => $result->traceId ?? null,
            'data' => $result->data ?? null,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
        return;
    }

    var_dump($result);
}

function saveDownloadContent(string $content, string $baseDir, string $startDate, string $endDate): void
{
    $decoded = json_decode($content, true);
    if (is_array($decoded) && isset($decoded['code'])) {
        echo json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
        throw new ZzbException('downloadReportRecord returned JSON error');
    }

    $file = getenv('NETSALE_DOWNLOAD_FILE') ?: $baseDir . "/download_report_record_{$startDate}_{$endDate}.zip";
    file_put_contents($file, $content);
    echo "saved: {$file} (" . strlen($content) . " bytes)\n";
}

function printHelp(): void
{
    echo <<<TXT
Usage:
  php examples/netsale2025_smoke.php
  php examples/netsale2025_smoke.php --send-report --confirm-submit-test-data
  php examples/netsale2025_smoke.php --download-record --vstk-signer-url=http://127.0.0.1:18080/sign
  php examples/netsale2025_smoke.php --all --confirm-submit-test-data --vstk-signer-url=http://127.0.0.1:18080/sign

Default is dry-run. Real report submission requires --confirm-submit-test-data.

Useful env vars:
  NETSALE_BASE_DIR
  NETSALE_CHANNEL_CODE
  NETSALE_CERT_ID
  NETSALE_CERT_FILE
  NETSALE_KEY_FILE
  NETSALE_TRUST_FILE
  NETSALE_VSTK_SIGNER_URL
  NETSALE_TICKET_NO
  NETSALE_NUMBER_BY_DAY

TXT;
}

class DryRunZzbService extends ZzbService
{
    protected function post(string $url, array $data, bool $decodeJson = true, array $headers = [])
    {
        echo "POST {$url}\n";
        if ($headers) {
            echo "headers:\n";
            echo json_encode($headers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
        }
        echo "body:\n";
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION) . "\n";

        if (!$decodeJson) {
            echo "response: <raw file stream in real mode>\n";
            return 'DRY_RUN_FILE_STREAM';
        }

        return [
            'code' => '200',
            'status' => 'dry-run',
            'data' => [],
            'traceId' => 'dry-run',
        ];
    }
}

class DryRunVstkSigner implements VstkSignerInterface
{
    public function p7AttachSign(string $certId, string $plainText): string
    {
        echo "V-STK p7AttachSign certId={$certId}\n";
        echo "plainText:\n{$plainText}\n";

        return base64_encode('DRY_RUN_P7_ATTACH_SIGNATURE');
    }
}

class HttpVstkSigner implements VstkSignerInterface
{
    public function __construct(private string $url)
    {
    }

    public function p7AttachSign(string $certId, string $plainText): string
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'certId' => $certId,
            'plainText' => $plainText,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new RuntimeException('V-STK signer curl error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode >= 400) {
            throw new RuntimeException("V-STK signer HTTP {$httpCode}: {$response}");
        }

        $decoded = json_decode((string) $response, true);
        if (is_array($decoded)) {
            foreach (['signature', 'signData'] as $key) {
                if (isset($decoded[$key]) && is_string($decoded[$key]) && $decoded[$key] !== '') {
                    return $decoded[$key];
                }
            }
            foreach (['data', 'result'] as $key) {
                if (isset($decoded[$key]) && is_array($decoded[$key])) {
                    foreach (['signature', 'signData'] as $innerKey) {
                        if (isset($decoded[$key][$innerKey]) && is_string($decoded[$key][$innerKey]) && $decoded[$key][$innerKey] !== '') {
                            return $decoded[$key][$innerKey];
                        }
                    }
                }
            }
        }

        $raw = trim((string) $response);
        if ($raw === '') {
            throw new RuntimeException('V-STK signer returned empty response');
        }

        return $raw;
    }
}

main();
