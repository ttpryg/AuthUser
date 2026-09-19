<?php

namespace Ttpryg\AuthUser\Entities;

use DateTimeImmutable;
use DateTimeInterface;

class Role
{
    private int|string|null $id;

    private string $name;

    private string $label;

    private ?string $description;

    private ?DateTimeInterface $createdAt;

    public function __construct(
        string $name,
        string $label,
        ?string $description = null,
        int|string|null $id = null,
        ?DateTimeInterface $createdAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->label = $label;
        $this->description = $description;
        $this->createdAt = $createdAt ?? new DateTimeImmutable;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'description' => $this->description,
            'created_at' => $this->createdAt?->format(DateTimeInterface::ATOM),
        ];
    }
}
