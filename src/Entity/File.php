<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1000)
     */
    private $path;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity=ProjectInfo::class, inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\OneToMany(targetEntity=ProjectStatistics::class, mappedBy="file")
     */
    private $statistic;

    public function __construct()
    {
        $this->statistic = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getProject(): ?ProjectInfo
    {
        return $this->project;
    }

    public function setProject(?ProjectInfo $project): self
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return Collection|ProjectStatistics[]
     */
    public function getStatistic(): Collection
    {
        return $this->statistic;
    }

    public function addStatistic(ProjectStatistics $statistic): self
    {
        if (!$this->statistic->contains($statistic)) {
            $this->statistic[] = $statistic;
            $statistic->setFile($this);
        }

        return $this;
    }

    public function removeStatistic(ProjectStatistics $statistic): self
    {
        if ($this->statistic->contains($statistic)) {
            $this->statistic->removeElement($statistic);
            // set the owning side to null (unless already changed)
            if ($statistic->getFile() === $this) {
                $statistic->setFile(null);
            }
        }

        return $this;
    }
}
