<?php

namespace App\Form\Type;

use App\Entity\Project;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UserType extends AbstractType implements DataMapperInterface
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine){
        $this->doctrine = $doctrine;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $teamsList = $this->doctrine->getRepository(Team::class)->findAll();
        $projectsList = $this->doctrine->getRepository(Project::class)->findAll();
        $teams = [];
        foreach ($teamsList as $team){
            $teams[$team->getName()] = $team->getId();
        }

        $projects = [];
        foreach ($projectsList as $project){
            $projects[$project->getName()] = $project->getId();
        }
        $builder->add('username', TextType::class)
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
            ->setDataMapper($this);

    }



    public function mapDataToForms(mixed $viewData, \Traversable $forms){
        if (null === $viewData) {
            return;
        }

        // invalid data type
        if (!$viewData instanceof User) {
            throw new UnexpectedTypeException($viewData, User::class);
        }

        $forms = iterator_to_array($forms);
        $forms['team']->setData($viewData->getTeam()->getId());
        $forms['project']->setData($viewData->getProject()->getId());
        $forms['username']->setData($viewData->getUsername());
        $forms['vacationDaysLeft']->setData($viewData->getVacationDaysLeft());
        $forms['role']->setData($viewData->getRole());
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData){$forms = iterator_to_array($forms);

    }
}