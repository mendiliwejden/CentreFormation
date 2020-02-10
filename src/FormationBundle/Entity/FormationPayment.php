<?php

namespace FormationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FormationPayment
 *
 * @ORM\Table(name="formation_payment")
 * @ORM\Entity(repositoryClass="FormationBundle\Repository\FormationPaymentRepository")
 */
class FormationPayment
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
     * @var string
     *
     * @ORM\Column(name="chargeId", type="string", length=255)
     */
    private $chargeId;

    /**
     * @ORM\ManyToOne(targetEntity="FormationBundle\Entity\Client", inversedBy="training_purchases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
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
     * Set chargeId
     *
     * @param string $chargeId
     *
     * @return FormationPayment
     */
    public function setChargeId($chargeId)
    {
        $this->chargeId = $chargeId;

        return $this;
    }

    /**
     * Get chargeId
     *
     * @return string
     */
    public function getChargeId()
    {
        return $this->chargeId;
    }
}

