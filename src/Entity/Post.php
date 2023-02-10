<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ApiResource(
    normalizationContext:['groups'=>['post:read']],
    denormalizationContext:['groups'=>['post:write']],
    collectionOperations:['get'],
    itemOperations:['get', "put", "delete"]
)]

class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    #[Groups(['post:read'])]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]

    #[Groups(['post:read', 'post:write'])]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(length: 60)]
    
    #[Groups(['post:read', 'post:write'])]
    private ?string $slug = null;


    #[ORM\Column(length: 255, nullable: true)]

    #[Groups(['post:read', 'post:write'])]
    private ?string $summary = null;

    #[ORM\Column(type: Types::TEXT)]

    #[Groups(['post:read', 'post:write'])]
    private ?string $content = null;

    #[ORM\Column]

    #[Groups(['post:read', 'post:write'])]
    private ?\DateTimeImmutable $PublishedAt = null;

   

    #[ORM\ManyToMany(targetEntity: Tags::class)]
    private Collection $tags;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'no', targetEntity: User::class)]
    private Collection $auteur;

    public function __construct()
    {
       
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->auteur = new ArrayCollection();
        $this->PublishedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

        return $this;
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

   
   
    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tags $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tags $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAuteur(): Collection
    {
        return $this->auteur;
    }

    public function addAuteur(User $auteur): self
    {
        if (!$this->auteur->contains($auteur)) {
            $this->auteur->add($auteur);
            $auteur->setNo($this);
        }

        return $this;
    }

    public function removeAuteur(User $auteur): self
    {
        if ($this->auteur->removeElement($auteur)) {
            // set the owning side to null (unless already changed)
            if ($auteur->getNo() === $this) {
                $auteur->setNo(null);
            }
        }

        return $this;
    }
    

}
