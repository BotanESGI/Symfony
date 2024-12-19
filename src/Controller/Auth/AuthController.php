<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('error', 'Déconnecte toi pour accéder à cette page.');
            return $this->redirectToRoute('page_homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'register')]
    public function register(): Response
    {
        return $this->render('auth/register.html.twig');
    }

    #[Route('/forgot-password/check-email', name: 'check_email')]
    public function checkEmail(): Response
    {
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('auth/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    #[Route('/forgot-password', name: 'forgot-password')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('page_homepage');
        }

        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($request->isMethod('POST') && $request->request->has('_email')) {
            $email = $request->get('_email');
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cette adresse e-mail.');
                return $this->redirectToRoute('forgot-password');
            }

            try {
                $resetToken = $this->resetPasswordHelper->generateResetToken($user);
                $this->entityManager->flush();

                $email = (new TemplatedEmail())
                    ->from(new Address('streami@noreply.com', 'Streami Mail Bot'))
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->htmlTemplate('auth/email/reset.html.twig')
                    ->context([
                        'resetToken' => $resetToken->getToken(),
                        'recipientEmail' => $user->getEmail(),
                        'resetUrl' => $this->generateUrl('reset-password', ['token' => $resetToken->getToken()], UrlGeneratorInterface::ABSOLUTE_URL),
                    ]);

                $mailer->send($email);

                $this->setTokenObjectInSession($resetToken);
                $this->addFlash('success', 'Un e-mail a été envoyé avec les instructions pour réinitialiser votre mot de passe.');

            } catch (ResetPasswordExceptionInterface $e) {
                $this->addFlash('error', 'Erreur lors de la génération du token. Veuillez réessayer.' . $e->getMessage());
                return $this->redirectToRoute('forgot-password');
            }
        }

        return $this->render('auth/forgot.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'reset-password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ExpiredResetPasswordTokenException $e) {
            // Gérer le cas où le token est expiré
            $this->addFlash('error', 'Le token a expiré. Veuillez demander un nouveau lien de réinitialisation.');
            return $this->redirectToRoute('forgot-password');
        } catch (\Exception $e) {
            // Gérer d'autres exceptions
            $this->addFlash('error', 'Une erreur s\'est produite lors de la validation du token.');
            return $this->redirectToRoute('forgot-password');
        }

        if (!$user) {
            $this->addFlash('error', 'Le token est invalide ou a expiré.');
            return $this->redirectToRoute('forgot-password');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $plainPasswordConfirm = $form->get('plainPasswordConfirm')->getData();

            if ($plainPassword !== $plainPasswordConfirm) {
                $form->get('plainPasswordConfirm')->addError(new FormError('Les mots de passe doivent correspondre.'));
            }

            if ($form->isValid()) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
                $this->entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');
                return $this->redirectToRoute('login');
            }
        }

        return $this->render('auth/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    #[Route('/confirm-email', name: 'confirm-email')]
    public function confirmEmail(): Response
    {
        return $this->render('auth/confirm.html.twig');
    }
}
