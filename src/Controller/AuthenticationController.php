<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{

    private Security $security;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher)
    {
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(path: 'login', name: 'app_login')]
    public function login(Request $request, ManagerRegistry $doctrine):Response{
          return $this->render('login/index.html.twig', []);
      }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {

    }



}