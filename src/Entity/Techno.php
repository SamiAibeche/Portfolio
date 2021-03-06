<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TechnoRepository")
 */
class Techno
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=125)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=12)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 7,
     *      max = 7,
     *      exactMessage = "The background-color shoud be composed exactly with 7 characters (Example : #FFFFFF)"
     * )
     */
    private $background;

    /**
     * @ORM\Column(type="string", length=12)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 7,
     *      max = 7,
     *      exactMessage = "The color shoud be composed exactly with 7 characters (Example : #000000)"
     * )
     */
    private $color;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="technos")
     */
    private $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function setBackground(string $background): self
    {
        $this->background = $background;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addTechno($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            $project->removeTechno($this);
        }

        return $this;
    }
    public function __toString()
    {
        return $this->name;
    }
}
