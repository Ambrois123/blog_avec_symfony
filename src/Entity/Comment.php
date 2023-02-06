<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $PublishedAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Post $no = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->PublishedAt;
    }

    public function setPublishedAt(\DateTimeImmutable $PublishedAt): self
    {
        $this->PublishedAt = $PublishedAt;

        return $this;
    }

    public function getNo(): ?Post
    {
        return $this->no;
    }

    public function setNo(?Post $no): self
    {
        $this->no = $no;

        return $this;
    }
}
