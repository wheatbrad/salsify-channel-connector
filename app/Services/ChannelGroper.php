<?php declare(strict_types=1);

namespace App\Services;

use App\Data\SalsifyCredential;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

final class ChannelGroper
{
    private Client $httpClient;
    private string $token;
    private string $orgId;
    private string $channelId;

    public function __construct(SalsifyCredential $credentials)
    {
        $this->httpClient = new Client();
        $this->token = $credentials->getToken();
        $this->orgId = $credentials->getOrgId();
        $this->channelId = $credentials->getChannelId();
    }

    public function initiateChannelRun(): void
    {
        new Request(
            'POST',
            "https://app.salsify.com/api/orgs/$this->orgId/channels/$this->channelId/runs",
            ['Authorization' => "Bearer $this->token"]
        );
    }

    public function getChannelData(): \Generator
    {
        $channelRunData = $this->getChannelRunStatus();
        
        while ($channelRunData->status === 'running') {
            sleep(2);
            $channelRunData = $this->getChannelRunStatus();
        }

        $request = new Request('GET', $channelRunData->product_export_url);
        $response = $this->httpClient->sendRequest($request);
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
        $request = new Request(
            'GET',
            "https://app.salsify.com/api//channels/$this->channelId/runs/latest",
            ['Authorization' => "Bearer $this->token"]
        );
        $response = $this->httpClient->sendRequest($request);

        return json_decode($response->getBody()->getContents());
    }
}