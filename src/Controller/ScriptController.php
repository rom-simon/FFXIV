<?php

namespace App\Controller;

use App\Entity\ClassJob;
use App\Entity\Job;
use App\Utils\Xiv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ScriptController extends AbstractController{

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function addJobs(){
        $xiv = new Xiv();

        $entityManager = $this->getDoctrine()->getManager();

        $classJobs = [];
        $classJobs[] = [
            'class' => $xiv->getCraftClass(),
            'jobs' => $xiv->getCraftJobs()
        ];
        $classJobs[] = [
            'class' => $xiv->getGatherClass(),
            'jobs' => $xiv->getGatherJobs()
        ];

        foreach ($classJobs as $params){
            $classEntity = $entityManager->getRepository(ClassJob::class)
                ->importClass($params['class']);
            foreach ($params['jobs'] as $job){
                $jobEntity = $entityManager->getRepository(Job::class)
                    ->importJob($job, $classEntity);
            }
        }

        return $this->render(
            'test/test.html.twig', [
                'leves' => null
            ]
        );
    }

}