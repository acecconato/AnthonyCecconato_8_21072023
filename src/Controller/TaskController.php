<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ListTasksDTO;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\UseCase\Task\CreateTaskInterface;
use App\UseCase\Task\DeleteTaskInterface;
use App\UseCase\Task\ListTasksInterface;
use App\UseCase\Task\MarkTaskInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{
    #[Route(path: '/app', name: 'app_home')]
    public function index(ListTasksInterface $listTasks, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        /** @var User $user */
        $user = $this->getUser();

        $listTasksDTO = new ListTasksDTO(
            $request->query->getInt('page', 1),
            $request->query->getBoolean('completed'),
            $request->query->getBoolean('anon')
        );

        return $this->render('task/index.html.twig', [
            'tasks' => $listTasks($user, $listTasksDTO),
        ]);
    }

    #[Route(path: '/app/taches/creer', name: 'app_task_create')]
    public function create(Request $request, CreateTaskInterface $createTask): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $createTask($task);

            $this->addFlash('success', 'Tâche créée avec succès');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/app/taches/{id}/marquer', name: 'app_task_mark')]
    #[IsGranted(attribute: 'owner', subject: 'task')]
    public function mark(Task $task, MarkTaskInterface $markTask): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $markTask($task);

        return $this->redirectToRoute('app_home');
    }

    #[Route(path: '/app/taches/{id}/supprimer', name: 'app_task_delete')]
    #[IsGranted(attribute: 'owner', subject: 'task')]
    public function delete(Task $task, DeleteTaskInterface $deleteTask): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $deleteTask($task);

        $this->addFlash('success', 'Tâche supprimée');

        return $this->redirectToRoute('app_home');
    }
}
