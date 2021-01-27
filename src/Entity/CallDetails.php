<?php

namespace App\Entity;

use App\Repository\CallDetailsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CallDetailsRepository::class)
 */
class CallDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @Assert\Type("integer")
     * @ORM\Column(type="integer")
     */
    public $accountBilling;

    /**
     * @Assert\Type("integer")
     * @ORM\Column(type="integer")
     */
    public $idBilling;

    /**
     * @Assert\Type("integer")
     * @ORM\Column(type="integer")
     */
    public $idSubscriber;

    /**
     * @ORM\Column(type="date")
     */
    public $date;

    /**
     * @ORM\Column(type="time")
     */
    public $time;

    /**
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255)
     */
    public $actualVolume;

    /**
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255)
     */
    public $billedVolume;

    /**
     * @Assert\Type("string")
     * @ORM\Column(type="string", length=255)
     */
    public $type;
}
