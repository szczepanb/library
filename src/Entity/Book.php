<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Library\Validator\Constraints as CustomAssert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @ORM\Table(name="book",indexes={@ORM\Index(name="filter_idx", columns={"title", "author_id"})})})
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(
     *  message = "This field cannot be empty."
     * )
     * @Assert\Length(
     *  max=50,
     *  maxMessage = "Title cannot be longer than 50 characters"
     * )
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Author", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * 
     * @Assert\NotBlank(
     *  message = "This field cannot be empty."
     * )
     * @CustomAssert\AuthorExist()
     */
    private $author;

    /**
     * @ORM\Column(type="date")
     * 
     * @Assert\NotBlank(
     *  message = "This field cannot be empty."
     * )
     * @Assert\Date
     * @var string A "Y-m-d" formatted value
     */
    private $publication_date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Translations", mappedBy="book", orphanRemoval=true, cascade={"persist", "remove"})
     * @Assert\NotBlank(
     *  message = "This field cannot be empty."
     * )
     */
    private $translations;

    public function __construct()
    {
        $this->translations = new ArrayCollection();
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

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publication_date;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate)
    {
        $this->publication_date = $publicationDate;
    }

    /**
     * @return Collection|Translations[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * 
     */
    public function setTranslations(Collection $translations)
    {
        $this->translations = $translations;
    }

    public function addTranslation(Translations $translation): self
    {
        if (!$this->translations->contains($translation)) {
            $this->translations[] = $translation;
            $translation->setBook($this);
        }

        return $this;
    }

    public function removeTranslations(): self
    {
        $this->translations->clear();
        return $this;
    }
}
