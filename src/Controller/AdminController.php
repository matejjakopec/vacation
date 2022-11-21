<?php

namespace App\Controller;

use App\Controller\Mapper\UserMapper;
use App\Entity\Project;
use App\Entity\Team;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class AdminController extends AbstractController
{
    private Security $security;

    private UserPasswordHasherInterface $passwordHasher;

    private UserMapper $mapper;

    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher, UserMapper $mapper)
    {
        $this->mapper = $mapper;
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(path: 'admin', name: 'admin')]
    public function admin(ManagerRegistry $doctrine, UserRepository $userRepository){
        $usersRaw = $userRepository->findAll();
        $users = [];
        foreach ($usersRaw as $user){
            $users[$user->getId()] = $this->mapper->mapUser($user);
        }
        return $this->render('admin/index.html.twig',[
            'users' => $users,
        ]);
    }

    #[Route(path: 'admin/add-user', name: 'add_user')]
    public function addUser(ManagerRegistry $doctrine, Request $request){
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();
            $this->handleForm($form, $doctrine, $user);
            return $this->redirectToRoute('admin');
        }

        return $this->renderForm('admin/add_user.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/admin/edit-user/{id}')]
    public function editUser(ManagerRegistry $doctrine, Request $request, int $id, UserRepository $userRepository){
       $user = $userRepository->find($id);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleForm($form, $doctrine, $user);
            return $this->redirectToRoute('admin');
        }

        return $this->renderForm('admin/add_user.html.twig', [
            'form' => $form,
        ]);
    }

    private function handleForm($form, $doctrine, $user){
        $team = $doctrine->getRepository(Team::class)->find($form->get('team')->getData());
        $project = $doctrine->getRepository(Project::class)->find($form->get('project')->getData());
        $user->setUsername($form->get('username')->getData())
            ->setPassword($this->passwordHasher->hashPassword($user, $form->get('password')->getData()))
            ->setRole($form->get('role')->getData())
            ->setTeam($team)
            ->setProject($project)
            ->setVacationDaysLeft($form->get('vacationDaysLeft')->getData());
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
    }

}