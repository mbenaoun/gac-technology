<?php

namespace App\Repository;

use App\Entity\CallDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CallDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method CallDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method CallDetails[]    findAll()
 * @method CallDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CallDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CallDetails::class);
    }

    /**
     * @param string $dateBegin
     * @return mixed|null
     * @throws NonUniqueResultException
     */
    public function getTotalTimeCalls(string $dateBegin)
    {
        return $this->createQueryBuilder('c')
            ->where('c.date >= :dateBegin')
            ->andWhere('c.type LIKE :typeWhere')
            ->select('SUM(c.time) AS totalTime')
            ->setParameter('dateBegin', $dateBegin)
            ->setParameter('typeWhere', '%appel%')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return mixed|null
     * @throws NonUniqueResultException
     */
    public function getTotalSmsSent()
    {
        return $this->createQueryBuilder('c')
            ->where('c.type LIKE :typeWhere')
            ->select('COUNT(c.id) AS totalSms')
            ->setParameter('typeWhere', '%sms%')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return mixed
     */
    public function getTopTenBilledVolume()
    {
        return $this->createQueryBuilder('c')
            ->where('c.type LIKE :typeWhere')
            ->andWhere('c.time > :timeEnd OR c.time < :timeStart')
            ->select('SUM(c.billedVolume) AS totalBilledVolume, c.idSubscriber')
            ->groupBy('c.idSubscriber')
            ->orderBy('totalBilledVolume', 'DESC')
            ->setMaxResults(10)
            ->setParameter('timeStart', '08:00:00')
            ->setParameter('timeEnd', '18:00:00')
            ->setParameter('typeWhere', '%connexion%')
            ->getQuery()
            ->getResult();
    }
}
