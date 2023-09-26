<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private string $name = "";

    #[ORM\ManyToMany(targetEntity: Series::class, mappedBy: 'categories')]
    private Collection $series;

    public function __construct()
    {
        $this->series = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Series>
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    public function addSeries(Series $series): static
    {
        if (!$this->series->contains($series)) {
            $this->series->add($series);
            $series->addCategory($this);
        }

        return $this;
    }

    public function removeSeries(Series $series): static
    {
        if ($this->series->removeElement($series)) {
            $series->removeCategory($this);
        }

        return $this;
    }
}
