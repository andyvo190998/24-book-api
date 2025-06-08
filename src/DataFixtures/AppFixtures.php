<?php

namespace App\DataFixtures;

use App\Entity\BookName;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $book = new BookName();
        $book->setName('Test Driven Symfony1');
        $book->setAuthor('Fabien Potencier');
        $book->setYear(2000);

        $manager->persist($book);
        $manager->flush();
    }
}
