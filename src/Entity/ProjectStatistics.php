<?php

namespace App\Entity;

use App\Repository\ProjectStatisticsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectStatisticsRepository::class)
 */
class ProjectStatistics
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $changing;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $filePath;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=ProjectInfo::class, inversedBy="projectStatistics")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projectInfo;

    /**
     * @ORM\ManyToOne(targetEntity=File::class, inversedBy="statistic")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChanging()
    {
        return $this->changing;
    }

    public function setChanging($changing): self
    {
        $this->changing = $changing;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;

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

    public function getProjectInfo(): ?ProjectInfo
    {
        return $this->projectInfo;
    }

    public function setProjectInfo(?ProjectInfo $projectInfo): self
    {
        $this->projectInfo = $projectInfo;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }
}
