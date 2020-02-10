<?php

namespace FormationBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="FormationBundle\Repository\ClientRepository")
 */
class Client extends BaseUser
{
    public function __construct() {
        $this->formations = new ArrayCollection();
        $this->certificates = new ArrayCollection();
        $this->training_purchases = new ArrayCollection();
        $this->certificate_purchases = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    protected $phone;

    /**
    * @ORM\ManyToMany(targetEntity="FormationBundle\Entity\Training")
     * @ORM\JoinTable(name="client_training")
    */
    protected $formations;

    /**
    * @ORM\ManyToMany(targetEntity="FormationBundle\Entity\Certificate")
     * @ORM\JoinTable(name="client_certificate")
    */
    protected $certificates;

    /**
     * @return mixed
     */
    public function getFormations()
    {
        return $this->formations;
    }

    /**
     * @param mixed $formations
     */
    public function setFormations($formations)
    {
        $this->formations = $formations;
    }

    /**
     * @return mixed
     */
    public function getCertificates()
    {
        return $this->certificates;
    }

    /**
     * @param mixed $certificates
     */
    public function setCertificates($certificates)
    {
        $this->certificates = $certificates;
    }



    public function addCertificate($certificate)
    {
        if(!$this->certificates->contains($certificate))
            $this->certificates[] = $certificate;
        return $this;
    }

    /**
     * @ORM\OneToMany(targetEntity="FormationBundle\Entity\Rating", mappedBy="client")
     */
    private $rate;

    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @ORM\OneToMany(targetEntity="FormationBundle\Entity\FormationPayment", mappedBy="client")
     */
    private $training_purchases;

    public function getTraining_purchases()
    {
        return $this->training_purchases;
    }

    /**
     * @ORM\OneToMany(targetEntity="FormationBundle\Entity\CertificatePayment", mappedBy="client")
     */
    private $certificate_purchases;

    public function getCertificate_purchases()
    {
        return $this->certificate_purchases;
    }

    /**
     * check is has certificate
     *
     * @param Certificate $certificate
     *
     * @return Boolean
     */
    public function hasCertificate($certificate)
    {
        return $this->certificates->contains($certificate);
    }

    public function addTraining($training)
    {
        if (!$this->formations->contains($training))
            $this->formations[] = $training;
        return $this;
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Client
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Client
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Client
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Client
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}

