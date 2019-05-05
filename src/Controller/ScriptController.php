<?php

namespace App\Controller;

use App\Entity\ClassJob;
use App\Entity\Job;
use App\Utils\Xiv;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ScriptController extends AbstractController{

    public function addJobs(){
        $xiv = new Xiv();

        $entityManager = $this->getDoctrine()->getManager();

        //Adding class
        $craftClass = $xiv->getCraftClass();
        $classEntity = $entityManager->getRepository(ClassJob::class)
            ->findOneBy(['api_id' => $craftClass->ID]);

        if (!$classEntity)$classEntity = new ClassJob(); //New ClassJob

        $classEntity->setName($craftClass->Name);
        $classEntity->setNameFr($craftClass->Name_fr);
        $classEntity->setApiId($craftClass->ID);

        //Persist class
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($classEntity);

        foreach ($xiv->getCraftJobs() as $job){
            $jobEntity = $entityManager->getRepository(Job::class)
                ->findByApiId($job->ID);

            if (!$jobEntity) $jobEntity = new Job();

            $jobEntity->setName($job->Name);
            $jobEntity->setNameFr($job->Name_fr);
            $jobEntity->setAbbreviation($job->Name);
            $jobEntity->setAbbreviationFr($job->Name);
            $jobEntity->setIconUrl($job->Icon);
            $jobEntity->setApiId($job->ID);
            $jobEntity->setClassJob($classEntity);

            //Persist job
            $entityManager->persist($jobEntity);
        }
        //Adding in database
        $entityManager->flush();

        return $this->render(
            'test/test.html.twig', [
                'leves' => null
            ]
        );
    }

}