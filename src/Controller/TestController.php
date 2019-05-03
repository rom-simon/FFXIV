<?php

namespace App\Controller;

use App\Utils\Xiv;
use App\Utils\XivApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class TestController extends AbstractController
{
    public function api()
    {
        $job = Yaml::parseFile('./../config/Jobs/Carpenter.yaml');
        $leves = $job['leves'];

        $api = new XivApi();
//        $test = $api->content->Quest()->one(65539 . "?language=fr");
//        $test = $api->search->find('quest')->results();
//        $test = $api->search->find('Driving Up The Wall&indexes=Leve')->indexes(['Leve'])->results();

        $datas = [];
        foreach ($leves as $lvl=>$name){
            $infos = $api->search->indexes(['leve'])->find($name)->limit(1)->results();
            $datas[$lvl] = $api->content->Leve()->one($infos->Results[0]->ID);
        }
//        $api->content->Leve()->list();
        return $this->render(
            'test/test.html.twig', [
                'leves' => $datas
            ]
        );
    }

    public function jobs()
    {
        $api = new Xiv();
        dump($api->getCraftJobs());
        exit;
    }
}