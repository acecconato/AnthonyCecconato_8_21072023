<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\UseCase\Task\CreateTask;
use App\UseCase\Task\DeleteTaskInterface;
use App\UseCase\Task\ListTasksInterface;
use App\UseCase\Task\MarkTaskInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route(path: '/app', name: 'app_home')]
    public function index(ListTasksInterface $listTasks, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('task/index.html.twig', [
            'tasks' => $listTasks(),
        ]);
    }

    #[Route(path: '/app/taches/creer', name: 'app_task_create')]
    public function create(Request $request, CreateTask $createTask): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            $createTask($task, $user);

            $this->addFlash('success', 'Tâche créée avec succès');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/app/taches/{id}/marquer', name: 'app_task_mark')]
    public function mark(Task $task, MarkTaskInterface $markTask, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($task->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $markTask($task);

        return $this->redirectToRoute('app_home');
    }

    #[Route(path: '/app/taches/{id}/supprimer', name: 'app_task_delete')]
    public function delete(Task $task, DeleteTaskInterface $deleteTask): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($task->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $deleteTask($task);

        $this->addFlash('success', 'Tâche supprimée');

        return $this->redirectToRoute('app_home');
    }
}
