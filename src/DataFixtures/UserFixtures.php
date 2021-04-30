<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

use Psr\Container\ContainerInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;
    private $container;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, ContainerInterface $container)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $now_datetime = new \DateTime('NOW', new \DateTimeZone('UTC'));

        // foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles]) {
        foreach ($this->getUserData() as [$username, $password, $email, $roles]) {
            $user = new User();
            // $user->setFullName($fullname);
            $user->setDateajout($now_datetime);
            $user->setSamaccountname($username);
            $user->setDisplayname($username);
            $user->setMail('A RENSEIGNER');
            $user->setDistinguishedname('A RENSEIGNER');
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setIsActive(true);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    private function getUserData() : array
    {
        $ldap_dom = '@' . $this->container->getParameter('ldap_dom');

        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
            // ['administrateur', 'Bbgci2018', 'administrateur@email.com', ['ROLE_ADMIN']],
            ['administrateur', 'Admin123', 'administrateur' . $ldap_dom, ['ROLE_ADMIN']],
            ['fidelin.kouame', 'Guillaume%2019', 'fidelin.kouame' . $ldap_dom, ['ROLE_ADMIN']],
            // ['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@symfony.com', ['ROLE_ADMIN']],
            // ['Tom Doe', 'tom_admin', 'kitten', 'tom_admin@symfony.com', ['ROLE_ADMIN']],
            // ['John Doe', 'john_user', 'kitten', 'john_user@symfony.com', ['ROLE_USER']],
        ];
    }


}