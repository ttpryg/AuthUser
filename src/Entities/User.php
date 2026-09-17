<?php

namespace Ttpryg\AuthUser\Entities;

use DateTimeImmutable;
use DateTimeInterface;
use Ttpryg\AuthUser\Contracts\AuthenticatableInterface;

class User implements AuthenticatableInterface
{
    private int|string|null $id;
    private ?string $username;
    private string $email;
    private string $passwordHash;
    private bool $isActive;
    private array $metadata;
    private ?DateTimeInterface $createdAt;
    private ?DateTimeInterface $updatedAt;
    private ?DateTimeInterface $deletedAt;

    public function __construct(
        string $email,
        string $passwordHash,
        ?string $username = null,
        bool $isActive = true,
        array $metadata = [],
        int|string|null $id = null,
        ?DateTimeInterface $createdAt = null,
        ?DateTimeInterface $updatedAt = null,
        ?DateTimeInterface $deletedAt = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->isActive = $isActive;
        $this->metadata = $metadata;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
        $this->deletedAt = $deletedAt;
    }

    public function getAuthIdentifier(): int|string
    {
        return $this->id;
    }

    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function setId(int|string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'is_active' => $this->isActive,
            'metadata' => $this->metadata,
            'created_at' => $this->createdAt?->format(DateTimeInterface::ATOM),
            'updated_at' => $this->updatedAt?->format(DateTimeInterface::ATOM),
            'deleted_at' => $this->deletedAt?->format(DateTimeInterface::ATOM),
        ];
    }
}
