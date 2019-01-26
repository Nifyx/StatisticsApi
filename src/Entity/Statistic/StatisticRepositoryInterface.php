<?php
/**
 * Created by PhpStorm.
 * User: apluchon2017
 * Date: 11/01/2019
 * Time: 15:27
 */

namespace App\Entity\Statistic;

/**
 * Interface PenRepositoryInterface
 * @package App\Entity
 */
interface StatisticRepositoryInterface
{
    /**
     * @param int $statId
     * @return Statistic
     */
    public function findById(int $statId): ?Statistic;
    /**
     * @return array
     */
    public function findAll(): array;
    /**
     * @param Statistic $statistic
     */
    public function save(Statistic $statistic): void;
    /**
     * @param Statistic $statistic
     */
    public function delete(Statistic $statistic): void;
    /**
     * @param String $idPen
     * @param String $time_start
     * @param String $time_end
     * @return array
     */
    public function getPenByPeriod(String $idPen, String $time_start, String $time_end): array;

    /**
     * @param String $idPen
     * @param String $time_start
     * @param String $time_end
     * @return int
     */
    public function getTotalViewsForPenByPeriod(String $idPen, String $time_start, String $time_end): int;

    /**
     * @param String $idPen
     * @param String $time_start
     * @param String $time_end
     * @return mixed
     */
    public function getTotalViewsByOrigin(String $idPen, String $time_start, String $time_end): array;

    /**
     * @param String $idPen
     * @param String $time_start
     * @param String $time_end
     * @return array
     */
    public function getStatsPerDay(String $idPen, String $time_start, String $time_end): array;

    /**
     * @param String $idPen
     * @param String $time_start
     * @param String $time_end
     * @return array
     */
    public function getLocationByPenOnPeriod(String $idPen, String $time_start, String $time_end): array;

    /**
     * @param String $time_start
     * @param String $time_end
     * @return array
     */
    public function getAllPensOnPeriod(String $time_start, String $time_end): array;
}