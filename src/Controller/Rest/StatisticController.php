<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 11/01/2019
 * Time: 18:42
 */

namespace App\Controller\Rest;


use App\Entity\Pen;
use App\Entity\Statistic;
use App\Repository\PenRepository;
use App\Repository\StatisticRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class StatisticController extends AbstractFOSRestController
{
    /**
     * @var StatisticRepository
     */
    private $statisticRepository;

    public function __construct(StatisticRepository $statisticRepository){
        $this->statisticRepository = $statisticRepository;
    }

    /**
     * Create a statistic in base
     * @Rest\Post("/stat")
     * @param Request $request
     * @return View
     */
    public function postStatistic(Request $request):View
    {
        /*$idPen = $request->get('idPen');
        $pen = $this->penRepository->findById($idPen);
        if($pen == null){
            $pen = new Pen();
            $pen->setId($idPen);
        }*/

        $statistic = new Statistic();
        $statistic->setCountry($request->get('country'));


        //$pen->addStatistic($statistic);

        //$statistic->setIp($request->get('ip'));
        //date_default_timezone_set('Europe/Paris');
        //$statistic->setCreatedAt(date("Y-m-d H:i:s"));
        //$statistic->setCreatedAt(new \DateTime('@'.strtotime('now')));

        $this->statisticRepository->save($statistic);
        //$this->penRepository->save($pen);

        return View::create($statistic, Response::HTTP_CREATED);
        //return new Response($serializer->serialize($statistic,'json'));
    }
}