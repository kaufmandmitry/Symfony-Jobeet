<?php
namespace Ens\JobeetBundle\Tests;

use Doctrine\ORM\Tools\SchemaTool;
use Liip\FunctionalTestBundle\Test\WebTestCase;


abstract class BaseTestSetup extends WebTestCase
{
    protected $client;
    protected $container;
    protected $em;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        if (!isset($metadatas)) {
            $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        }
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropDatabase();
        if (!empty($metadatas)) {
            $schemaTool->createSchema($metadatas);
        }
        $this->postFixtureSetup();

        $this->loadFixtures(array(
            'Ens\JobeetBundle\DataFixtures\ORM\LoadJobData',
            'Ens\JobeetBundle\DataFixtures\ORM\LoadCategoryData'
        ));

    }
}
