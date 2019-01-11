<?php
/**
 * Created by PhpStorm.
 * User: apluchon2017
 * Date: 11/01/2019
 * Time: 15:27
 */

namespace App\Entity;

/**
 * Interface StatisticRepositoryInterface
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
}