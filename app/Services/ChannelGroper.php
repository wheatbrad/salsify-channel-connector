<?php declare(strict_types=1);

namespace App\Services;

use App\Data\SalsifyCredential;
use GuzzleHttp\Client;

/**
 * Service to interrogate channel metadata and stream output. 
 */
final class ChannelGroper
{
    private Client $httpClient;
    private string $token;
    private string $orgId;
    private string $channelId;

    const CHUNK_SIZE = 1048576;
    const NUM_HOURS = 20;

    public function __construct(SalsifyCredential $credentials, Client $httpClient)
    {
        $this->token = $credentials->getToken();
        $this->orgId = $credentials->getOrgId();
        $this->channelId = $credentials->getChannelId();
        $this->httpClient = $httpClient;
    }

    /**
     * Stream dumped channel data from cloud storage. 
     *
     * @return \Generator
     */
    public function getChannelData(): \Generator
    {
        $channelRunData = $this->getChannelRunStatus();
        
        if ($this->mustInitiateChannelRun(@$channelRunData->ended_at)) {
            $this->initiateChannelRun();
        }
        
        while ($channelRunData->status !== 'completed') {
            sleep(2);
            $channelRunData = $this->getChannelRunStatus();
        }

        $response = $this->httpClient->request('GET', $channelRunData->product_export_url);
        $stream = $response->getBody();
        $dataGenerator = function () use ($stream) {
            while (!$stream->eof()) {
                yield $stream->read(ChannelGroper::CHUNK_SIZE);
            }
        };

        return $dataGenerator();
    }

    /**
     * Determine, based on channel run metadata, if channel data
     * dump should be run again. 
     *
     * @param string|null $endingTimestamp
     * @return boolean
     */
    private function mustInitiateChannelRun(?string $endingTimestamp): bool
    {
        // Channel run had not previously been initiated
        if (is_null($endingTimestamp)) {
            return true;
        }

        $lastRunEndedAt = new \DateTime($endingTimestamp);
        
        return $lastRunEndedAt->diff(new \DateTime())->h > ChannelGroper::NUM_HOURS;
    }

    /**
     * POST request to endpoint to initiate channel data dump.
     * 
     * @return void
     */
    private function initiateChannelRun(): void
    {
        $this->httpClient->request(
            'POST',
            "https://app.salsify.com/api/orgs/$this->orgId/channels/$this->channelId/runs",
            [
                'headers' => ['Authorization' => "Bearer $this->token"]
            ]
        );
    }

    /**
     * GET request to endpoint for channel data dump metadata.
     * 
     * @return \stdClass
     */
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