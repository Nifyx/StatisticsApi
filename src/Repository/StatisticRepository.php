<?php

namespace App\Repository;

use App\Entity\Statistic\Statistic;
use App\Entity\Statistic\StatisticRepositoryInterface;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PenRepository
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
     * PenRepository constructor.
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

    /**
     * @param String $idPen
     * @param String $time_start
     * @param String $time_end
     * @return array
     */
    public function getPenByPeriod(String $idPen, String $time_start, String $time_end): array
    {
        $time_start_format = date('Y-m-d H:i:s',$time_start);
        $time_end_format = date('Y-m-d H:i:s',$time_end);

        $query = $this->entityManager->createQuery('SELECT s 
            FROM App\Entity\Statistic\Statistic s 
            WHERE s.pen = :idPen 
            AND s.createdAt 
            BETWEEN :time_start AND :time_end')
            ->setParameter('idPen',$idPen)
            ->setParameter('time_start',$time_start_format)
            ->setParameter('time_end',$time_end_format);

        return $query->getResult();
    }

    /**
     * @param String $idPen
     * @param String $time_start
     * @param String $time_end
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getTotalViewsForPenByPeriod(String $idPen, String $time_start, String $time_end): int
    {
        $time_start_format = date('Y-m-d H:i:s',$time_start);
        $time_end_format = date('Y-m-d H:i:s',$time_end);

        $connection = $this->entityManager->getConnection();

        $sql = 'SELECT COUNT(*) as nbView 
                FROM gcftp_apiStats.statistic s
                WHERE s.pen_id = :idPen
                AND s.created_at BETWEEN :time_start AND :time_end';

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'idPen' => $idPen,
            'time_start' => $time_start_format,
            'time_end' => $time_end_format
        ]);

        $totalViews = $stmt->fetch()['nbView'];

        return $totalViews;
    }

    /**
     * @param String $time_start
     * @param String $time_end
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getTotalViewsForPensByPeriod(String $time_start, String $time_end): int
    {
        $time_start_format = date('Y-m-d H:i:s',$time_start);
        $time_end_format = date('Y-m-d H:i:s',$time_end);

        $connection = $this->entityManager->getConnection();

        $sql = 'SELECT COUNT(*) as nbView 
                FROM gcftp_apiStats.statistic s
                WHERE s.created_at BETWEEN :time_start AND :time_end';

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'time_start' => $time_start_format,
            'time_end' => $time_end_format
        ]);

        $totalViews = $stmt->fetch()['nbView'];

        return $totalViews;
    }

    /**
     * @param String $idPen
     * @param String $time_start
     * @param String $time_end
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getTotalViewsByOrigin(String $idPen, String $time_start, String $time_end): array
    {
        $time_start_format = date('Y-m-d H:i:s',$time_start);
        $time_end_format = date('Y-m-d H:i:s',$time_end);

        $connection = $this->entityManager->getConnection();

        $sql = 'SELECT COUNT(*) as nbView, origin 
                FROM gcftp_apiStats.statistic s
                WHERE s.pen_id = :idPen
                AND s.created_at BETWEEN :time_start AND :time_end
                GROUP BY s.origin';

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'idPen' => $idPen,
            'time_start' => $time_start_format,
            'time_end' => $time_end_format
        ]);

        $origin = $stmt->fetchAll();

        return $origin;
    }

    /**
     * @param String $idPen
     * @param String $time_start
     * @param String $time_end
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getStatsPerDay(String $idPen, String $time_start, String $time_end): array
    {
        //String date
        $time_start_format = date('Y-m-d',$time_start);
        $time_end_format = date('Y-m-d',$time_end);

        //DateTime date
        $date_start = new \DateTime($time_start_format);
        $date_end = new \DateTime($time_end_format);

        $connection = $this->entityManager->getConnection();

        $daysArray = array();

        /**
         * We do a request for each day for the pen given
         */
        while($date_start < $date_end){
            $sql = "SELECT COUNT(*) as nbView, origin 
                FROM gcftp_apiStats.statistic s
                WHERE s.pen_id = :idPen
                AND s.created_at > :date_start AND s.created_at < DATE_ADD(:date_start, INTERVAL 1 DAY)
                GROUP BY s.origin";

            $stmt = $connection->prepare($sql);

            //Give parameter
            $stmt->execute([
                'idPen' => $idPen,
                'date_start' => $date_start->format('Y-m-d')
            ]);

            //Put the result of the query in an array
            $originArray = $stmt->fetchAll();

            /**
             * Get all stat who comes from codepen
             */
            $originCodePen = "";
            $nbViewCodePen = 0;
            foreach($originArray as $key => $value){
                if($value['origin'] == "codepen"){
                    $originCodePen = $originArray[$key]['origin'];
                    $nbViewCodePen = $originArray[$key]['nbView'];
                    unset($originArray[$key]);
                }
            }

            /**
             * Give details on the externals view
             */
            $nbTotalViewsExternal = 0;
            $arrayList = array();
            foreach($originArray as $key => $value){
                $nbTotalViewsExternal += $value['nbView'];
                array_push($arrayList,['url' => $value['origin'], 'totalViews' => $value['nbView']]);
            }

            /**
             * Give stat for each day
             */
            array_push($daysArray,[
                'day' => $date_start->getTimestamp(),
                'totalViewsDay' => $nbViewCodePen + $nbTotalViewsExternal,
                'origins' => array(
                    $originCodePen => ['totalViews' => $nbViewCodePen],
                    'externals' => array(
                        'totalViews' => $nbTotalViewsExternal,
                        'list' => $arrayList
                    )
                )
            ]);

            //Add one day to the date
            date_add($date_start,new \DateInterval('P1D'));
        }
        return $daysArray;
    }

    /**
     * @param String $idPen
     * @param String $time_start
     * @param String $time_end
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getLocationByPenOnPeriod(String $idPen, String $time_start, String $time_end): array
    {
        $time_start_format = date('Y-m-d H:i:s',$time_start);
        $time_end_format = date('Y-m-d H:i:s',$time_end);

        $connection = $this->entityManager->getConnection();

        $sql = 'SELECT COUNT(*) as nbView, country 
                FROM gcftp_apiStats.statistic s
                WHERE s.pen_id = :idPen
                AND s.created_at BETWEEN :time_start AND :time_end
                GROUP BY s.country';

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'idPen' => $idPen,
            'time_start' => $time_start_format,
            'time_end' => $time_end_format
        ]);

        $countries = $stmt->fetchAll();
        $arrayCountries = array();
        foreach($countries as $key => $value){
            array_push($arrayCountries,['totalViews' => $value['nbView'], 'name' => $value['country']]);
        }

        return $arrayCountries;
    }

    /**
     * @param String $time_start
     * @param String $time_end
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllPensOnPeriod(String $time_start, String $time_end): array
    {
        $time_start_format = date('Y-m-d H:i:s',$time_start);
        $time_end_format = date('Y-m-d H:i:s',$time_end);

        $connection = $this->entityManager->getConnection();

        $sql = 'SELECT COUNT(*) as nbView, pen_id
                FROM gcftp_apiStats.statistic s
                WHERE s.created_at BETWEEN :time_start AND :time_end
                GROUP BY s.pen_id';

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            'time_start' => $time_start_format,
            'time_end' => $time_end_format
        ]);

        $pens = $stmt->fetchAll();
        $arrayPens = array();
        foreach($pens as $key => $value){
            array_push($arrayPens,['totalViews' => $value['nbView'], 'id' => $value['pen_id']]);
        }

        return $arrayPens;
    }
}
