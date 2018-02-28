<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Theme;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ThemeFixture extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     * @throws \Doctrine\Common\DataFixtures\BadMethodCallException
     */
    public function load(ObjectManager $manager)
    {
        $themes = [];
        $themeData = ["SQL", "Symfony", "PHP", "Javascript", "Ionic"];

        foreach ($themeData as $item) {
            $entity = new Theme();
            $entity->setName($item);
            $themes[] = $entity;
            $this->addReference("theme_{$item}", $entity);

            $manager->persist($entity);
        }
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 5;
    }
}