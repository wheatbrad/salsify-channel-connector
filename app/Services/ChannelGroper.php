<?php declare(strict_types=1);

namespace App\Services;

use App\Data\SalsifyCredential;
use GuzzleHttp\Client;

/**
 * Service
 */
final class ChannelGroper
{
    private Client $httpClient;
    private string $token;
    private string $orgId;
    private string $channelId;

    public function __construct(SalsifyCredential $credentials, Client $httpClient)
    {
        $this->token = $credentials->getToken();
        $this->orgId = $credentials->getOrgId();
        $this->channelId = $credentials->getChannelId();
        $this->httpClient = $httpClient;
    }

    public function initiateChannelRun(): void
    {
        $this->httpClient->request(
            'POST',
            "https://app.salsify.com/api/orgs/$this->orgId/channels/$this->channelId/runs",
            [
                'headers' => ['Authorization' => "Bearer $this->token"]
            ]
        );
    }

    public function getChannelData(): \Generator
    {
        $channelRunData = $this->getChannelRunStatus();
        
        while ($channelRunData->status === 'running') {
            sleep(2);
            $channelRunData = $this->getChannelRunStatus();
        }

        $response = $this->httpClient->request('GET', $channelRunData->product_export_url);
        $stream = $response->getBody();
        $dataGenerator = function () use ($stream) {
            while (!$stream->eof()) {
                yield $stream->read(1024);
            }
        };

        return $dataGenerator();
    }

    private function getChannelRunStatus(): \stdClass
    {
        $response = $this->httpClient->request(
            'GET',
            "https://app.salsify.com/api/channels/$this->channelId/runs/latest",
            [
                'headers' => ['Authorization' => "Bearer $this->token"]
            ]
        );

        return json_decode($response->getBody()->getContents());
    }
}