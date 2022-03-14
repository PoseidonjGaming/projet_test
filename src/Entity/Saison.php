<?php

namespace App\Entity;

use App\Repository\SaisonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SaisonRepository::class)
 */
class Saison
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    

    /**
     * @ORM\Column(type="smallint", length=2)
     */
    private $numero;

    /**
     * @ORM\Column(type="string")
     */
    private $nb_episode;

    /**
     * @ORM\OneToMany(targetEntity=Episode::class, mappedBy="saison", orphanRemoval=true)
     */
    private $episodes;

    /**
     * @ORM\ManyToOne(targetEntity=Serie::class, inversedBy="saisons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $serie;

    public function __construct()
    {
        $this->episodes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNbEpisode(): ?string
    {
        return $this->nb_episode;
    }

    public function setNbEpisode(string $nb_episode): self
    {
        $this->nb_episode = $nb_episode;

        return $this;
    }

    /**
     * @return Collection|Episode[]
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): self
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes[] = $episode;
            $episode->setSaison($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): self
    {
        if ($this->episodes->removeElement($episode)) {
            // set the owning side to null (unless already changed)
            if ($episode->getSaison() === $this) {
                $episode->setSaison(null);
            }
        }

        return $this;
    }

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function setSerie(?Serie $serie): self
    {
        $this->serie = $serie;

        return $this;
    }
    
    
   
}
