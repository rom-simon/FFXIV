<?php

namespace App\Utils;

use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\Yaml\Yaml;
use XIVAPI\Api\ContentHandler;
use XIVAPI\XIVAPI;

class Xiv extends XIVAPI {
    private $config;

    private $class;
    private $jobs;

    /**
     * get method of jobs
     * @var bool $jobsFull - get full jobs informations
     */
    private $jobsFull;

    public function __construct(string $environment = self::PROD)
    {
        parent::__construct($environment);
        $this->config = json_decode(json_encode(Yaml::parseFile(__DIR__ . '../../../config/xivapi.yaml')));
    }

    public function setClass(): void
    {
        $this->class = $this->content->ClassJobCategory()->list()->Results;
    }

    /**
     * @param bool $full - true: get all details of jobs
     */
    public function setJobs($full = false): void
    {
        $jobs = $this->class = $this->content->ClassJob()->list()->Results;
        if ($full){
            $this->jobsFull = 1;
            $this->jobs = [];
            foreach ($jobs as $job){
                $this->jobs[] = $this->content->ClassJob()->one($job->ID);
            }
        } else {
            $this->jobsFull = 1;
            $this->jobs = $jobs;
        }
    }


    /**
     * @return object
     */
    public function getClass()
    {
        if ($this->class === null)$this->setClass();
        return $this->class;
    }

    /**
     * @return object
     */
    public function getJobs($full = false)
    {
        if ($this->jobs === null || $this->jobsFull !== $full)$this->setJobs($full);
        return $this->jobs;
    }

    /**
     * @return \GuzzleHttp\Promise\PromiseInterface|mixed
     * @throws \Exception
     */
    public function getCraftClass(){
        foreach ($this->getClass() as $class){
            if ($class->Name == $this->config->class->craft->name){
                return $this->content->ClassJobCategory()->one($class->ID);
            }
        }
        throw new \Exception("Crafting Class not found");
    }

    /**
     * @return \GuzzleHttp\Promise\PromiseInterface|mixed
     * @throws \Exception
     */
    public function getGatherClass(){
        foreach ($this->getClass() as $class){
            if ($class->Name == $this->config->class->gather->name){
                return $this->content->ClassJobCategory()->one($class->ID);
            }
        }
        throw new \Exception("Gathering Class not found");
    }


    /**
     * get all craft jobs
     * @throws \Exception
     */
    public function getCraftJobs(){
        $class = $this->getCraftClass();
        $jobs = [];
        $allJobs = $this->getJobs(true);
        foreach ($allJobs as $job){
            if ($job->ClassJobCategoryTargetID == $class->ID)$jobs[] = $job;
        }
        return $jobs;
    }

    /**
     * get all gather jobs
     * @throws \Exception
     */
    public function getGatherJobs(){
        $class = $this->getGatherClass();
        $jobs = [];
        $allJobs = $this->getJobs(true);
        foreach ($allJobs as $job){
            if ($job->ClassJobCategoryTargetID == $class->ID)$jobs[] = $job;
        }
        return $jobs;
    }

}