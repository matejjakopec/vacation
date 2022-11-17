<?php

namespace App\Controller;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

class RedirectController extends AbstractController
{
    private Security $security;


    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher)
    {
        $this->security = $security;
    }

    #[Route(path: 'redirect', name: 'redirect_after_login')]
    public function redirectAfterLogin(ManagerRegistry $doctrine){
        $username = $this->security->getUser()->getUserIdentifier();
        $user = $doctrine->getRepository(User::class)->findOneBy(['username' => $username]);
        if(in_array('ROLE_ADMIN', $user->getRoles())){
            return $this->redirectToRoute('admin');
        }
        return $this->redirectToRoute('user_info');
    }

}