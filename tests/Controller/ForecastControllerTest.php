<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Location;
use App\Repository\LocationRepository;

class ForecastControllerTest extends WebTestCase
{
    private LocationRepository $repository;

    private string $path = '/';

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient([], [
            'HTTP_HOST'       => 'localhost:8000'
        ]);
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Location::class);
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
    }
    
    public function testIndex(): void
    {
        $this->client->request('GET', $this->path);
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Weather Forecast');
    }

    public function testBadInput(): void
    {
        $this->client->request('GET', $this->path);
        
        $crawler = $this->client->request('GET', $this->path);
        $buttonCrawlerNode = $crawler->selectButton('Submit');
        $form = $buttonCrawlerNode->form();

        // No city
        $form['location[country]'] = 'Spain';
        $this->client->submit($form);
        $this->assertSelectorExists('.form-error-message');
        $this->assertSelectorTextContains('.form-error-message', 'This value should not be blank.');

        // City contains numbers.
        $form['location[country]'] = 'Spain';
        $form['location[city]'] = 'Madrid88';
        $this->client->submit($form);
        $this->assertSelectorExists('.form-error-message');
        $this->assertSelectorTextContains('.form-error-message', 'The string "Madrid88" contains an illegal character: it can only contain letters.');
    }

    public function testSuccessfulForecast(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->client->request('GET', $this->path);
        
        $crawler = $this->client->request('GET', $this->path);
        $buttonCrawlerNode = $crawler->selectButton('Submit');
        $form = $buttonCrawlerNode->form();

        $form['location[country]'] = 'Spain';
        $form['location[city]'] = 'Madrid';
        $this->client->submit($form);
        $this->assertSelectorNotExists('.form-error-message');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $location = $this->repository->findOneBy([
            'country' => 'Spain',
            'city' => 'Madrid',
        ]);
        $this->assertSelectorTextContains('h4', 'Results');
        self::assertNotNull($location->getLongitude());
        self::assertNotNull($location->getLatitude());
        self::assertNotNull($location->getTemperature());

        // Check if location duplicating "country-city" pair won't be created.
        $originalNumObjectsInRepository = count($this->repository->findAll());
        $this->client->submit($form);
        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));

    }

    protected function tearDown() : void
    {
        foreach ($this->repository->findAll() as $object) {
            $object = $this->entityManager->merge($object);
            $this->entityManager->remove($object);
            $this->entityManager->flush();
        }
        parent::tearDown();
    }
}
