<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 * @ORM\Table(name="author",indexes={@ORM\Index(name="filter_idx", columns={"name", "surname", "country"})})})
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank(
     *  message = "This field cannot be empty."
     * )
     * @Assert\Length(
     *  max=40,
     *  maxMessage = "Title cannot be longer than 40 characters"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank(
     *  message = "This field cannot be empty."
     * )
     * @Assert\Length(
     *  max=40,
     *  maxMessage = "Title cannot be longer than 40 characters"
     * )
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(
     *  message = "Field country must be select."
     * )
     */
    private $country;

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function __toString()
    {
        return $this->name.' '.$this->surname;
    }
}
