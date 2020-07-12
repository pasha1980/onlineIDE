<?php

namespace App\Entity;

use App\Repository\ProjectInfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectInfoRepository::class)
 */
class ProjectInfo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $projectName;

    /**
     * @ORM\Column(type="integer")
     */
    private $countOfFiles;

    /**
     * @ORM\Column(type="integer")
     */
    private $countOfFolders;

    /**
     * @ORM\Column(type="integer")
     */
    private $countOfLines;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cli;

    /**
     * @ORM\Column(type="boolean")
     */
    private $layout;

    /**
     * @ORM\Column(type="boolean")
     */
    private $website;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $filenames = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $folders = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=ProjectStatistics::class, mappedBy="projectInfo")
     */
    private $projectStatistics;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="project")
     */
    private $files;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="projectInfos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->projectStatistics = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }

    public function setProjectName(string $projectName): self
    {
        $this->projectName = $projectName;

        return $this;
    }

    public function getCountOfFiles(): ?int
    {
        return $this->countOfFiles;
    }

    public function setCountOfFiles(int $countOfFiles): self
    {
        $this->countOfFiles = $countOfFiles;

        return $this;
    }

    public function getCountOfFolders(): ?int
    {
        return $this->countOfFolders;
    }

    public function setCountOfFolders(int $countOfFolders): self
    {
        $this->countOfFolders = $countOfFolders;

        return $this;
    }

    public function getCountOfLines(): ?int
    {
        return $this->countOfLines;
    }

    public function setCountOfLines(int $countOfLines): self
    {
        $this->countOfLines = $countOfLines;

        return $this;
    }

    public function getCli(): ?bool
    {
        return $this->cli;
    }

    public function setCli(bool $cli): self
    {
        $this->cli = $cli;

        return $this;
    }

    public function getLayout(): ?bool
    {
        return $this->layout;
    }

    public function setLayout(bool $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function getWebsite(): ?bool
    {
        return $this->website;
    }

    public function setWebsit(bool $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getFilenames(): ?array
    {
        return $this->filenames;
    }

    public function setFilenames(?array $filenames): self
    {
        $this->filenames = $filenames;

        return $this;
    }

    public function getFolders(): ?array
    {
        return $this->folders;
    }

    public function setFolders(?array $folders): self
    {
        $this->folders = $folders;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|ProjectStatistics[]
     */
    public function getProjectStatistics(): Collection
    {
        return $this->projectStatistics;
    }

    public function addProjectStatistic(ProjectStatistics $projectStatistic): self
    {
        if (!$this->projectStatistics->contains($projectStatistic)) {
            $this->projectStatistics[] = $projectStatistic;
            $projectStatistic->setProjectInfo($this);
        }

        return $this;
    }

    public function removeProjectStatistic(ProjectStatistics $projectStatistic): self
    {
        if ($this->projectStatistics->contains($projectStatistic)) {
            $this->projectStatistics->removeElement($projectStatistic);
            // set the owning side to null (unless already changed)
            if ($projectStatistic->getProjectInfo() === $this) {
                $projectStatistic->setProjectInfo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setProject($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getProject() === $this) {
                $file->setProject(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
