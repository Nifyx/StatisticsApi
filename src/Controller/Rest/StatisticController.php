<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 11/01/2019
 * Time: 18:42
 */

namespace App\Controller\Rest;


use App\Entity\Statistic;
use App\Repository\StatisticRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StatisticController extends AbstractFOSRestController
{
    /**
     * @var StatisticRepository
     */
    private $statisticRepository;

    /**
     * Create a statistic in base
     * @Rest\Post("/stat")
     * @param Request $request
     * @return View
     */
    public function postStatistic(Request $request): View
    {
        $statistic = new Statistic();
        $statistic->setCountry($request->get('country'));
        $statistic->setIp($request->get('ip'));
        date_default_timezone_set('Europe/Paris');
        $statistic->setDateAndHours(date("Y-m-d H:i:s"));

        $this->statisticRepository->save($statistic);

        return View::create($statistic, Response::HTTP_CREATED);
    }
}