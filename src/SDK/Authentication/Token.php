<?php

namespace SDK\Authentication;

class Token
{

    private string $clientId;
    private string $secret;
    private string $grantType;
    private DateTimeImmutable $expiresAt;
    private array $settings;

    
    public function __construct(string $clientId, string $secret,string $grantType,array $settings)
    {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->grantType = $grantType;
        $this->settings = $settings;
        $this->expiresAt = (new \DateTimeImmutable())->modify("+7 days")->getTimestamp();
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId($clientId): void
    {
        $this->clientId = $clientId;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function setSecret($secret): void
    {
        $this->secret = $secret;
    }

    public function getGrantType(): string
    {
        return $this->grantType;
    }

    public function setGrantType($grantType): void
    {
        $this->grantType = $grantType;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings($settings): void
    {
        $this->settings = $settings;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt($expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}