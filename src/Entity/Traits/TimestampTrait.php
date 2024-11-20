<?php

namespace App\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Overblog\GraphQLBundle\Annotation as GQL;

#[ORM\HasLifecycleCallbacks]
trait TimestampTrait
{
    #[ORM\Column(name: "is_enabled", type: Types::BOOLEAN, nullable: false)]
    #[GQL\Field(type: 'Bool')]
    public bool $isEnabled = true;

    #[ORM\Column(name: "is_deleted", type: Types::BOOLEAN, nullable: true)]
    #[GQL\Field(type: 'DateTimeType')]
    public bool $isDeleted = false;

    #[ORM\Column(name: "created_at", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[GQL\Field(type: 'DateTimeType')]
    public ?DateTime $createdAt;

    #[ORM\Column(name: "updated_at", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[GQL\Field(type: 'DateTimeType')]
    public ?DateTime $updatedAt;

    #[ORM\Column(name: "deleted_at", type: Types::DATETIME_MUTABLE, nullable: true)]
    #[GQL\Field(type: 'DateTimeType')]
    public ?DateTime $deletedAt;

    public function __clone()
    {
        $this->createdAt = null;
        $this->updatedAt = null;
    }

    public function getClassName(): string
    {
        return __CLASS__;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }
    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }
    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }
    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getIsEnabled(): bool
    {
        return $this->isEnabled;
    }
    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist() {
        $this->createdAt = new DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate() {
        if(is_null($this->createdAt)) {
            $this->createdAt = new DateTime();
        }
        $this->updatedAt = new DateTime();
    }

}