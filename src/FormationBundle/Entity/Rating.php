<?php

namespace FormationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Rating
 *
 * @ORM\Table(name="rating")
 * @ORM\Entity(repositoryClass="FormationBundle\Repository\RatingRepository")
 */
class Rating
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="rate", type="smallint")
     */
    private $rate;

    /**
     * @var string
     *
     * @ORM\Column(name="review", type="text", length=2550)
     */
    protected $review;

    /**
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @param string $review
     */
    public function setReview($review)
    {
        $this->review = $review;
    }

    /**
     * @ORM\ManyToOne(targetEntity="FormationBundle\Entity\Training", inversedBy="rate")
     * @ORM\JoinColumn(nullable=false)
     */
    private $training;

    /**
     * @ORM\ManyToOne(targetEntity="FormationBundle\Entity\Client", inversedBy="rate")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    public function setTraining(Training $training)
    {
        $this->training =  $training;
        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(Client $client)
    {
        $this->client =  $client;
        return $this;
    }

    public function getTraining()
    {
        return $this->trainings;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rate
     *
     * @param integer $rate
     *
     * @return Rating
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return int
     */
    public function getRate()
    {
        return $this->rate;
    }
}

