<?php

namespace FormationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Instructor
 *
 * @ORM\Table(name="instructor")
 * @ORM\Entity(repositoryClass="FormationBundle\Repository\InstructorRepository")
 */
class Instructor
{

    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

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
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="salary", type="string", length=255)
     */
    private $salary;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * One Customer has One Cart.
     * @ORM\OneToMany(targetEntity="Training", mappedBy="instructor")
     */
    private $formations;

    /**
     * @return mixed
     */
    public function getFormations()
    {
        return $this->formations;
    }

    public function addFormation($formation)
    {
        if (!$this->formations->contains($formation)) {
            $this->formations[] = $formation;
            $formation->setInstructor($this);
        }
        return $this;
    }
    /**
     * @param mixed $formations
     */
    public function setFormations($formations)
    {
        $this->formations = $formations;
    }

    /**
     * @ORM\Column(type="string")
     */
    private $img;

    public function getImg()
    {
        return $this->img;
    }

    public function setImg($imgfilename)
    {
        $this->img = $imgfilename;

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
     * @return Instructor
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
     * @return Instructor
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
     * @return Instructor
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
     * Set salary
     *
     * @param string $salary
     *
     * @return Instructor
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;

        return $this;
    }

    /**
     * Get salary
     *
     * @return string
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Instructor
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

