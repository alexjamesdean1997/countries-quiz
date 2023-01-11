<?php

namespace App\Controller;

use App\Entity\Country;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\SecurityAuthenticator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface $authenticator,
        SecurityAuthenticator $formAuthenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setUsername($form->get('username')->getData());
            $user->setRoles(['ROLE_USER']);
            $user->setCreatedAt(new DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

            return $authenticator->authenticateUser(
                $user,
                $formAuthenticator,
                $request);
        }

        $countries    = $this->doctrine->getRepository(Country::class)->findAll();
        shuffle($countries);

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'countries'        => $countries,
        ]);
    }
}
