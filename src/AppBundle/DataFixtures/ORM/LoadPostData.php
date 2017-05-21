<?php
//this class load fake post data into db
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Post;

class LoadPostData implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        // manual Faker: https://github.com/fzaninotto/Faker#installation
        $faker = \Faker\Factory::create();
        
        for($i = 0; $i < 500; $i++) {
            $fakePost = new Post;
            $fakePost->setTitle($faker->sentence(3));
            $fakePost->setLead($faker->text(150));
            $fakePost->setContent($faker->text(500));
            $fakePost->setCreatedAt($faker->dateTimeThisYear);

            $manager->persist($fakePost);
        }
        $manager->flush();
    }
}
