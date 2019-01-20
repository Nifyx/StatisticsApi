<?php

namespace App\Repository;

use App\Entity\Pen\Pen;
use App\Entity\Pen\PenRepositoryInterface;
use App\Entity\Statistic;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PenRepository
 * @package App\Repository
 */
final class PenRepository implements PenRepositoryInterface
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
     * PenRepository constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $this->entityManager->getRepository(Pen::class);
    }
    /**
     * @param String $penId
     * @return Pen
     */
    public function findById(String $penId): ?Pen
    {
        return $this->objectRepository->find($penId);
    }
    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->objectRepository->findAll();
    }
    /**
     * @param Pen $pen
     */
    public function save(Pen $pen): void
    {
        $this->entityManager->persist($pen);
        $this->entityManager->flush();
    }
    /**
     * @param Pen $pen
     */
    public function delete(Pen $pen): void
    {
        $this->entityManager->remove($pen);
        $this->entityManager->flush();
    }
}
