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

    public function __construct(string $environment = self::PROD)
    {
        parent::__construct($environment);
        $this->config = json_decode(json_encode(Yaml::parseFile(__DIR__ . '../../../config/xivapi.yaml')));
    }

    public function setClass(): void
    {
        $this->class = $this->content->ClassJobCategory()->list()->Results;
    }

    public function setJobs(): void
    {
        $this->jobs = $this->class = $this->content->ClassJob()->list()->Results;
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
    public function getJobs()
    {
        if ($this->jobs === null)$this->setJobs();
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
     * @throws \Exception
     */
    public function getCraftJobs(){
        $craftClass = $this->getCraftClass();
        $craftJobs = [];
        $gatherClass = $this->getGatherClass();
        $gatherJobs = [];

        $allJobs = $this->getJobs();
        foreach ($allJobs as $job){
            $job = $this->content->ClassJob()->one($job->ID);
            dump($job);
            exit;
        }
    }

}