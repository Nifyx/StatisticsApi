<?php

namespace App\Repository;

use App\Entity\Statistic;
use App\Entity\StatisticRepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class StatisticRepository
 * @package App\Repository
 */
final class StatisticRepository implements StatisticRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ObjectRepository
     */
    private $objectRepository;

    /**
     * StatisticRepository constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $this->entityManager->getRepository(Statistic::class);
    }
    /**
     * @param int $statId
     * @return Statistic
     */
    public function findById(int $statId): ?Statistic
    {
        return $this->objectRepository->find($statId);
    }
    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->objectRepository->findAll();
    }
    /**
     * @param Statistic $statistic
     */
    public function save(Statistic $statistic): void
    {
        $this->entityManager->persist($statistic);
        $this->entityManager->flush();
    }
    /**
     * @param Statistic $statistic
     */
    public function delete(Statistic $statistic): void
    {
        $this->entityManager->remove($statistic);
        $this->entityManager->flush();
    }
}
