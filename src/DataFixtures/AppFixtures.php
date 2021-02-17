<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Website;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new Admin();
        $admin->setEmail('webmaster@prevor.com')
            ->setPassword('JeJe2018*');
        $encoded = $this->encoder->encodePassword($admin, $admin->getPassword());
        $admin->setPassword($encoded);
        $manager->persist($admin);
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
