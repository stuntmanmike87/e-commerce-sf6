<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Users;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
//use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

final class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UsersAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
        JWTService $jwt
    ): ?Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        /** @var string $form_data */
        $form_data = $form->get('plainPassword')->getData();
        
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form_data
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            // On génère le JWT de l'utilisateur
            // On crée le Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le Payload
            /** @var array<string> $payload */
            $payload = [
                'user_id' => $user->getId()
            ];

            // On génère le token
            /** @var string $secret */
            $secret = $this->getParameter('app.jwtsecret');
            $token = $jwt->generate($header, $payload, $secret );

            /** @var array<string> $context() */
            $context = ['user' => $user, 'token' => $token];
        
            // On envoie un mail
            $mail->send(
                'no-reply@monsite.net',
                (string) $user->getEmail(),
                'Activation de votre compte sur le site e-commerce',
                'register',
                $context
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,//->createView(),
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser(/*TokenInterface*/string $token, JWTService $jwt, UsersRepository $usersRepository, EntityManagerInterface $em): Response
    {
        //On vérifie si le token est valide, n'a pas expiré et n'a pas été modifié
        /** @var string $secret */
        $secret = $this->getParameter('app.jwtsecret');

        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $secret)){
            // On récupère le payload
            $payload = $jwt->getPayload($token);

            // On récupère le user du token
            $user = $usersRepository->find($payload['user_id']);

            //On vérifie que l'utilisateur existe et n'a pas encore activé son compte
            if(($user instanceof Users) && ! (bool) $user->getIsVerified()){
                $user->setIsVerified(true);
                $em->flush();
                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('profile_index');
            }
        }

        // Ici un problème se pose dans le token
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        if(!$user instanceof UserInterface){//if($user === null){
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');    
        }

        /** @var Users $user */
        
        if((bool) $user->getIsVerified()){
            $this->addFlash('warning', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('profile_index');    
        }

        // On génère le JWT de l'utilisateur
        // On crée le Header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        // On crée le Payload
        /** @var array<string> $payload */
        $payload = [
            'user_id' => $user->getId()
        ];

        // On génère le token
        /** @var string $secret */
        $secret = $this->getParameter('app.jwtsecret');

        $token = $jwt->generate($header, $payload, $secret);

        /** @var array<string> $context */
        $context = ['user' => $user, 'token' => $token];
        
        // On envoie un mail
        $mail->send(
            'no-reply@monsite.net',
            (string) $user->getEmail(),
            'Activation de votre compte sur le site e-commerce',
            'register',
            $context
        );
        $this->addFlash('success', 'Email de vérification envoyé');
        return $this->redirectToRoute('profile_index');
    }
}
