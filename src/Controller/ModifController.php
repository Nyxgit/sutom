<?php
// src/Controller/ModifController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ModifController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function modif(Request $request, AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $passwordHasher): Response
    {
        $id = $request->get('id');
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if ($user) {
            $form = $this->createForm(UserRegistrationFormType::class, $user);
            $form->handleRequest($request);

            $error = $authenticationUtils->getLastAuthenticationError();

            if ($form->isSubmitted() && $form->isValid()) {
                $userData = $form->getData();

                if ($userData->getPassword() !== null) {
                    $hashedPassword = $passwordHasher->hashPassword(
                        $user,
                        $userData->getPassword()
                    );
                    $user->setPassword($hashedPassword);
                }

                if ($request->request->get('action') === 'modifier') {
                    $user->setUsername($userData->getUsername());

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    return $this->redirectToRoute('sutom_home', ['id' => $id]);
                } elseif ($request->request->get('action') === 'supprimer') {
                    $this->entityManager->remove($user);
                    $this->entityManager->flush();

                    return $this->redirectToRoute('sutom_inscription');
                }
            }

            return $this->render('sutom/modifuser.html.twig', [
                'form' => $form->createView(),
                'error' => $error,
            ]);
        } else {
            return $this->redirectToRoute('sutom_modif');
        }
    }
}
