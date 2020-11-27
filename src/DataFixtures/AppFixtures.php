<?php

namespace App\DataFixtures;

use App\Entity\Website;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $weblist = array(
            'Prevor' => 'https://www.prevor.com',
            'Env Prevor' => 'https://environnement.prevor.com/',
            'Dressilk' => 'https://www.dressilk.com',
            'Toto' => 'https://www.toto.tata/'
        );

        foreach ($weblist as $key => $value) {
            $weblist = new Website();
            $weblist->setName($key)->setUrl($value);

            $manager->persist($weblist);
        }

        $manager->flush();
    }
}
