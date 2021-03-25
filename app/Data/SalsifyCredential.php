<?php

namespace App\Data;

class SalsifyCredential
{
    private string $token;
    private string $orgId;
    private string $channelId;

    public function __construct(string $token, string $orgId, string $channelId)
    {
        $this->token = $token;
        $this->orgId = $orgId;
        $this->channelId = $channelId;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getOrgId(): string
    {
        return $this->orgId;
    }

    public function getChannelId(): string
    {
        return $this->channelId;
    }
}