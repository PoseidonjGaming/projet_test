<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SerieRepository::class)
 * @ORM\Table(name="`serie`")
 */
class Serie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_diff;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $resume;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $affiche;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url_ba;

    /**
     * @ORM\OneToMany(targetEntity=Saison::class, mappedBy="serie", orphanRemoval=true)
     */
    private $saisons;

    /**
     * @ORM\OneToMany(targetEntity=Personnage::class, mappedBy="serie", orphanRemoval=true)
     */
    private $personnages;


    public function __construct()
    {
        $this->saisons = new ArrayCollection();
        $this->personnages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDiff(): ?\DateTimeInterface
    {
        return $this->date_diff;
    }

    public function setDateDiff(\DateTimeInterface $date_diff): self
    {
        $this->date_diff = $date_diff;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    public function getAffiche(): ?string
    {
        return $this->affiche;
    }

    public function setAffiche(?string $affiche): self
    {
        $this->affiche = $affiche;

        return $this;
    }

    public function getUrlBa(): ?string
    {
        return $this->url_ba;
    }

    public function setUrlBa(?string $url_ba): self
    {
        $this->url_ba = $url_ba;

        return $this;
    }

    /**
     * @return Collection|Saison[]
     */
    public function getSaisons(): Collection
    {
        return $this->saisons;
    }

    public function addSaison(Saison $saison): self
    {
        if (!$this->saisons->contains($saison)) {
            $this->saisons[] = $saison;
            $saison->setSerie($this);
        }

        return $this;
    }

    public function removeSaison(Saison $saison): self
    {
        if ($this->saisons->removeElement($saison)) {
            // set the owning side to null (unless already changed)
            if ($saison->getSerie() === $this) {
                $saison->setSerie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Personnage[]
     */
    public function getPersonnages(): Collection
    {
        return $this->personnages;
    }

    public function addPersonnage(Personnage $personnage): self
    {
        if (!$this->personnages->contains($personnage)) {
            $this->personnages[] = $personnage;
            $personnage->setSerie($this);
        }

        return $this;
    }

    public function removePersonnage(Personnage $personnage): self
    {
        if ($this->personnages->removeElement($personnage)) {
            // set the owning side to null (unless already changed)
            if ($personnage->getSerie() === $this) {
                $personnage->setSerie(null);
            }
        }

        return $this;
    }




    public function dataJson()
    {
        $nbEp = 0;
        foreach ($this->getSaisons() as $uneSaison) {
            $nbEp += count($uneSaison->getEpisodes());
        }
        $resumeTemp = $this->getResume();


        $data = [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
            'date' => $this->getDateDiff(),
            'resume' => $this->getResume(),
            'affiche' => $this->getAffiche(),
            'Ba' => $this->getUrlBa(),
            'saison' => count($this->getSaisons()),
            'episodes' => $nbEp,
        ];
        return $data;
    }
}
