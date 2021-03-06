<?php

namespace Application\Repository;

use Application\Entity\Movement;
use Doctrine\ORM\EntityRepository;

class MovementRepository extends EntityRepository
{
    /**
     * @param int $accountId
     * @param ?\DateTime $date
     * @return float
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBalance(int $accountId, ?\DateTime $date = null): float
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COALESCE(SUM(m.amount), 0) AS balance')
            ->from(Movement::class, 'm')
            ->where('m.account=:accountId')
            ->setParameter(':accountId', $accountId);

        if ($date) {
            $qb->andWhere('m.date<=:date')->setParameter(':date', $date->format('Y-m-d'));
        }

        $result = $qb->getQuery()->getOneOrNullResult();
        return (float) $result['balance'];
    }

    public function search(array $params = []): array
    {
        $cleanParams  = [];
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('m')
            ->from(Movement::class, 'm')
            ->where('1=1');

        if (!empty($params['accountId'])) {
            $qb->andWhere('m.account = :accountId');
            $cleanParams['accountId'] = $params['accountId'];
        }
        if (!empty($params['dateMin'])) {
            $qb->andWhere('m.date >= :dateMin');
            $cleanParams['dateMin'] = $params['dateMin'];
        }
        if (!empty($params['dateMax'])) {
            $qb->andWhere('m.date <= :dateMax');
            $cleanParams['dateMax'] = $params['dateMax'];
        }
        if (!empty($params['category'])) {
            $qb->andWhere('m.category = :category');
            $cleanParams['category'] = $params['category'];
        }
        if (!empty($params['description'])) {
            $qb->andWhere('m.description LIKE :description');
            $cleanParams['description'] = '%' . $params['description'] . '%';
        }
        if (is_numeric($params['amountMin'])) {
            $qb->andWhere('m.amount >= :amountMin');
            $cleanParams['amountMin'] = (float) $params['amountMin'];
        }
        if (is_numeric($params['amountMax'])) {
            $qb->andWhere('m.amount <= :amountMax');
            $cleanParams['amountMax'] = (float) $params['amountMax'];
        }

        return $qb->setParameters($cleanParams)->orderBy('m.date', 'DESC')->getQuery()->getResult();
    }

    /**
     * @param int $userId
     * @return float
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalExpense(int $userId): float
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COALESCE(SUM(m.amount), 0) AS amount')
            ->from(Movement::class, 'm')
            ->innerJoin('m.category', 'c')
            ->where('c.user=:userId')
            ->setParameter(':userId', $userId);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    public function getMovementByDay(int $userId, string $minDate, string $maxDate): array
    {
        // SELECT date, SUM(amount) AS amount FROM movement INNER JOIN account ON movement.accountId=account.id
        // WHERE userId=1 AND recap=1 AND date >= '2019-02-10' AND date<='2019-02-25' GROUP BY date
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('m.date, SUM(m.amount) AS amount')
            ->from(Movement::class, 'm')
            ->innerJoin('m.account', 'a')
            ->where("a.user=$userId AND m.amount < 0 AND a.recap=1 AND m.date BETWEEN :minDate AND :maxDate")
            ->setParameter(':minDate', $minDate)
            ->setParameter(':maxDate', $maxDate)
            ->groupBy('m.date')
            ->orderBy('m.date');

        return $qb->getQuery()->getResult();
    }
}
