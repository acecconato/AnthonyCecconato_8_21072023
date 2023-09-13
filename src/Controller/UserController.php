<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\UseCase\User\CreateUserInterface;
use App\UseCase\User\DeleteUserInterface;
use App\UseCase\User\ListUsersInterface;
use App\UseCase\User\UpdateUserInterface;
use App\UseCase\User\UpdateUserRoleInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/comptes', name: 'app_users')]
    public function index(ListUsersInterface $listUsers, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var User $user */
        $user = $this->getUser();

        $page = max($request->query->getInt('page', 1), 1);

        return $this->render('user/index.html.twig', [
            'users' => $listUsers($user, $page),
        ]);
    }

    #[Route('/comptes/ajouter', name: 'app_users_add')]
    public function create(Request $request, CreateUserInterface $createUser): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['validation_groups' => ['Default', 'user:create']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $createUser($user, $plainPassword);

            $this->addFlash('success', 'Compte '.$user->getUsername().' crée avec succès');

            return $this->redirectToRoute('app_users');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/comptes/{id}/modifier', name: 'app_users_update')]
    public function update(User $user, Request $request, UpdateUserInterface $updateUser): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = (is_string($form->get('plainPassword')->getData()) ? $form->get('plainPassword')->getData() : null);
            $updateUser($user, $plainPassword);

            $this->addFlash('success', 'Compte '.$user->getUsername().' modifié avec succès');

            return $this->redirectToRoute('app_users');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/comptes/{id}/modifier-role', name: 'app_users_update_role')]
    public function updateUserRole(User $user, UpdateUserRoleInterface $updateUserRole, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $updateUserRole($user);
        $this->addFlash('success', 'Le rôle a bien été mis à jour');

        return $this->redirect((string) $request->headers->get('referer'));
    }

    #[Route(path: '/comptes/{id}/supprimer', name: 'app_users_delete')]
    public function deleteUser(User $user, DeleteUserInterface $deleteUser, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $deleteUser($user);
        $this->addFlash('success', "L'utilisateur a bien été supprimé");

        return $this->redirect((string) $request->headers->get('referer'));
    }
}
