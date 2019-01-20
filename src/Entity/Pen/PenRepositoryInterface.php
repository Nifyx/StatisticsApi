<?php
/**
 * Created by PhpStorm.
 * User: apluchon2017
 * Date: 11/01/2019
 * Time: 15:27
 */

namespace App\Entity\Pen;

/**
 * Interface PenRepositoryInterface
 * @package App\Entity
 */
interface PenRepositoryInterface
{
    /**
     * @param String $penId
     * @return Pen
     */
    public function findById(String $penId): ?Pen;
    /**
     * @return array
     */
    public function findAll(): array;
    /**
     * @param Pen $pen
     */
    public function save(Pen $pen): void;
    /**
     * @param Pen $pen
     */
    public function delete(Pen $pen): void;
}