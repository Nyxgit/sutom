<?php
// src/Controller/UserController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Form\FormError;




class UserController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }
    public function register(Request $request): Response
    {
        $form = $this->createForm(UserRegistrationFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();

            if ($this->isUsernameTaken($userData['username'])) {
                $form->get('username')->addError(new FormError('Ce nom d\'utilisateur est déjà pris.'));
                return $this->render('sutom/inscription.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            $user = new User();
            $user->setUsername($userData['username']);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $userData['password']
            );
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $id = $user->getId();

            return $this->redirectToRoute('sutom_mois', ['id' => $id]);
            // return $this->redirectToRoute('sutom_home', ['id' => $id]);
        }

        return $this->render('sutom/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    private function isUsernameTaken(string $username): bool
    {
        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        return $existingUser !== null;
    }
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->createForm(UserRegistrationFormType::class);

        $form->handleRequest($request);

        $error = $authenticationUtils->getLastAuthenticationError();

        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();

            $userRepository = $this->entityManager->getRepository(User::class);

            $user = $userRepository->findOneBy(['username' => $userData['username']]);

            if ($user) {
                $passwordIsValid = $this->passwordHasher->isPasswordValid($user, $userData['password']);

                if ($passwordIsValid) {
                    $id = $user->getId();
                    return $this->redirectToRoute('sutom_mois', ['id' => $id]);

                    // return $this->redirectToRoute('sutom_home', ['id' => $id]);
                }
            } else {
                $this->addFlash('error', 'Nom d\'utilisateur ou mot de passe incorrect.');
            }
        }
        $id = 0;
        return $this->render('sutom/connexion.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'id' => $id
        ]);
    }
}
