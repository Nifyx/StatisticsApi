<?php
/**
 * Created by PhpStorm.
 * User: Antoine
 * Date: 11/01/2019
 * Time: 18:42
 */

namespace App\Controller\Rest;


use App\Entity\Pen\Pen;
use App\Entity\Statistic\Statistic;
use App\Repository\PenRepository;
use App\Repository\StatisticRepository;
use Doctrine\DBAL\DBALException;
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
     * @var PenRepository
     */
    private $penRepository;

    public function __construct(StatisticRepository $statisticRepository, PenRepository $penRepository){
        $this->statisticRepository = $statisticRepository;
        $this->penRepository = $penRepository;
    }

    /**
     * Create a new view in db
     * @Rest\Post("/newView")
     * @param Request $request
     * @return View
     */
    public function postStatistic(Request $request):View
    {
        $idPen = $request->get('idPen');
        $pen = $this->penRepository->findById($idPen);
        if($pen == null){
            $pen = new Pen();
            $pen->setId($idPen);
            $this->penRepository->save($pen);
        }

        $statistic = new Statistic();
        $statistic->setCountry($request->get('country'));
        $statistic->setPen($pen);
        $statistic->setOrigin($request->get('origin'));

        $this->statisticRepository->save($statistic);

        $view = $this->view($statistic, Response::HTTP_CREATED)
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Access-Control-Allow-Origin','*');

        return $view;
    }

    /**
     * @param Request $request
     * @Rest\POST("/getPenByPeriod")
     * @return View
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postGetPenByPeriod(Request $request):View
    {
        $id = $request->request->get('id');
        $time_start = $request->request->get('time_start');
        $time_end = $request->request->get('time_end');

        $pens = $this->statisticRepository->getPenByPeriod($id, $time_start, $time_end);
        $viewTotal = $this->statisticRepository->getTotalViewsForPenByPeriod($id, $time_start, $time_end);
        $originArray = $this->statisticRepository->getTotalViewsByOrigin($id, $time_start, $time_end);
        $daysArray = $this->statisticRepository->getStatsPerDay($id, $time_start, $time_end);
        $originCodePen = "";
        $nbViewCodePen = 0;

        /**
         * Get stat from codepen
         */
        foreach($originArray as $key => $value){
            if($value['origin'] == "codepen.io"){
                $originCodePen = $originArray[$key]['origin'];
                $nbViewCodePen = $originArray[$key]['nbView'];
                unset($originArray[$key]);
            }
        }

        /**
         * Create the list for externals stats
         */
        $nbTotalViewsExternal = 0;
        $arrayList = array('list' => array());
        foreach($originArray as $key => $value){
            $nbTotalViewsExternal += $value['nbView'];
            array_push($arrayList['list'],['url' => $value['origin'], 'totalViews' => $value['nbView']]);
        }

        /**
         * Create the json to return
         */
        $data = array(
            'totalViews' => $viewTotal,
            'origins' => array(
                $originCodePen => array('totalViews' => $nbViewCodePen),
                'externals' => $arrayList
            ),
            'days' => $daysArray
        );

        $view = $this->view($data, Response::HTTP_OK)
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Access-Control-Allow-Origin','*');

        return $view;
    }

    /**
     * @param Request $request
     * @Rest\Post("/getLocationByPeriod")
     * @return View
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postGetLocationByPeriod(Request $request):View
    {
        $id = $request->request->get('id');
        $time_start = $request->request->get('time_start');
        $time_end = $request->request->get('time_end');

        $totalViews = $this->statisticRepository->getTotalViewsForPenByPeriod($id,$time_start,$time_end);
        $countriesArray = $this->statisticRepository->getLocationByPenOnPeriod($id,$time_start,$time_end);

        $jsonCountries = array(
            'totalViews' => $totalViews,
            'countries' => $countriesArray
        );

        $view = $this->view($jsonCountries, Response::HTTP_OK)
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Access-Control-Allow-Origin','*');

        return $view;
    }

    /**
     * @param Request $request
     * @Rest\Post("/getAllPenByPeriod")
     * @return View
     * @throws \Doctrine\DBAL\DBALException
     */
    public function postGetAllPenByPeriod(Request $request):View
    {
        $time_start = $request->request->get('time_start');
        $time_end = $request->request->get('time_end');

        $totalViews = $this->statisticRepository->getTotalViewsForPensByPeriod($time_start,$time_end);
        $pensArray = $this->statisticRepository->getAllPensOnPeriod($time_start,$time_end);

        $jsonPen = array(
            'totalViews' => $totalViews,
            'pens' => $pensArray
        );

        $view = $this->view($jsonPen, Response::HTTP_OK)
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Access-Control-Allow-Origin','*');

        return $view;
    }
}