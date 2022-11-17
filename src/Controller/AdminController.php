<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AdminController extends AbstractController
{
    private Security $security;

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher)
    {
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route(path: 'admin', name: 'admin')]
    public function admin(ManagerRegistry $doctrine){
        $usersRaw = $doctrine->getRepository(User::class)->findAll();
        $users = [];
        foreach ($usersRaw as $user){
            $users[$user->getId()] = [
                'username' => $user->getUsername(),
                'vacationDaysLeft' => $user->getVacationDaysLeft(),
            ];
        }
        return $this->render('admin/index.html.twig',[
            'users' => $users,
        ]);
    }

    #[Route(path: 'admin/add-user', name: 'add_user')]
    public function addUser(ManagerRegistry $doctrine, Request $request){
        $teamsList = $doctrine->getRepository(Team::class)->findAll();
        $projectsList = $doctrine->getRepository(Project::class)->findAll();
        $teams = [];
        foreach ($teamsList as $team){
            $teams[$team->getName()] = $team->getId();
        }

        $projects = [];
        foreach ($projectsList as $project){
            $projects[$project->getName()] = $project->getId();
        }

        $form = $this->createFormBuilder()
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('vacationDaysLeft', NumberType::class)
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Worker' => 'ROLE_USER',
                    'Team Lead' => 'ROLE_TEAM_LEAD',
                    'Project Lead' => 'ROLE_PROJECT_LEAD',
                    'Admin' => 'ROLE_ADMIN',
                ],
            ])
            ->add('team', ChoiceType::class, [
                'choices' => $teams,
            ])
            ->add('project', ChoiceType::class, [
                'choices' => $projects,
            ])
            ->add('save', SubmitType::class, ['label' => 'create user'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();
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

            return $this->redirectToRoute('admin');
        }

        return $this->renderForm('admin/add_user.html.twig', [
            'form' => $form,
        ]);
    }

}