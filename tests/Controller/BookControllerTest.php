<?php

namespace App\Tests\Controller;

use App\Entity\BookName;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookControllerTest extends WebTestCase
{
    public function testCreateBook(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/book', [
            'name' => 'Effective Symfony',
            'author' => 'Fabien Potencier',
            'year' => 2000
        ]);

        self::assertResponseIsSuccessful();
    }

    public function testIndex(): void
    {
        $client = static::createClient();
        $book = self::getContainer()->get('doctrine')->getRepository(BookName::class)->findOneBy(['name' => 'Test Driven Symfony1']);
        $client->request('GET', '/book/' . $book->getId());
        self::assertResponseIsSuccessful();
    }
}
