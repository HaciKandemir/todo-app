<?php

namespace App\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TaskRepository
     */
    private $taskRepository;

    public function __construct(
        EntityManagerInterface $em,
        TaskRepository $taskRepository
    ) {
        $this->em = $em;
        $this->taskRepository = $taskRepository;
    }

    public function addMultipleTask(array $tasks): void
    {
        foreach ($tasks as $taskData) {
            $task = new Task();
            $task->setName($taskData['name']);
            $task->setDuration($taskData['duration']);
            $task->setDifficulty($taskData['difficulty']);

            $this->taskRepository->add($task);
        }
        $this->em->flush();
    }

    public function getAllTasks(): array
    {
        return $this->taskRepository->findAll();
    }
}