<?php

namespace App\Entity;

use App\Repository\EpisodeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EpisodeRepository::class)
 */
class Episode
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
     * @ORM\Column(type="text")
     */
    private $resume;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_prem_diff;

    /**
     * @ORM\ManyToOne(targetEntity=Saison::class, inversedBy="episodes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $saison;

    

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

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    public function getDatePremDiff(): ?\DateTimeInterface
    {
        return $this->date_prem_diff;
    }

    public function setDatePremDiff(?\DateTimeInterface $date_prem_diff): self
    {
        $this->date_prem_diff = $date_prem_diff;

        return $this;
    }

    public function getSaison(): ?Saison
    {
        return $this->saison;
    }

    public function setSaison(?Saison $saison): self
    {
        $this->saison = $saison;

        return $this;
    }

    public function dataJson(){
        $data=[
            'id'=>$this->getId(),
            'nom'=>$this->getNom(),
            'date'=>$this->getDatePremDiff(),
            "resume"=>$this->getResume(),
            'serieId'=>$this->getSaison()->getSerie()->getId(),
            'serieNom'=>$this->getSaison()->getSerie()->getNom(),
            'saison'=>$this->getSaison()->getNumero()
        ];
        return $data;
    }
}
