<?php

namespace App\Service;

use App\Entity\Task;

class TaskShareService
{
    /**
     * @var TaskService
     */
    private $taskService;
    private $developers = [
        "DEV1" => [
            "duration" => "1",
            "difficulty" => "1",
            "weekly_work_duration" => "45",
            "weekly_point" => "45",
            "task" => [],
        ],
        "DEV2" => [
            "duration" => "1",
            "difficulty" => "2",
            "weekly_work_duration" => "45",
            "weekly_point" => "90",
            "task" => [],
        ],
        "DEV3" => [
            "duration" => "1",
            "difficulty" => "3",
            "weekly_work_duration" => "45",
            "weekly_point" => "135",
            "task" => [],
        ],
        "DEV4" => [
            "duration" => "1",
            "difficulty" => "4",
            "weekly_work_duration" => "45",
            "weekly_point" => "180",
            "task" => [],
        ],
        "DEV5" => [
            "duration" => "1",
            "difficulty" => "5",
            "weekly_work_duration" => "45",
            "weekly_point" => "225",
            "task" => [],
        ],
    ];

    public function __construct(TaskService $taskService) {
        $this->taskService = $taskService;
    }

    public function shareTask(): array
    {
        $tasks = $this->taskService->getAllTasks();
        $tasks = $this->sortTasks($tasks);
        $this->assignTasks($tasks);
        return $this->developers;
    }
    //Sort tasks by score points
    private function sortTasks(array $tasks): array
    {
        usort($tasks, function ($a, $b) {
            return $a->getPoint() < $b->getPoint();
        });
        return $tasks;
    }
    //Assign tasks to developers by score points and weekly working hours
    private function assignTasks(array $tasks): void
    {
        foreach ($tasks as $key => $task) {
           $this->assignTask($task);
        }
    }
    //Assign task to developer by weekly max score points and max working hours
    private function assignTask(Task $task): void
    {
        $maxScorePoint = 0;
        $maxWorkingHours = 0;
        $developer = "";
        foreach ($this->developers as $key => $developerData) {
            if (
                ($developerData["weekly_point"] > $maxScorePoint) && (($developerData["weekly_point"] - $task->getPoint()) >= 0) &&
                ($developerData["weekly_work_duration"] > $maxWorkingHours) &&
                (($developerData["weekly_work_duration"] - $this->getDeveloperTaskTime($task, $developerData)) >= 0)
            ) {
                $maxScorePoint = $developerData["weekly_point"];
                $developer = $key;
            }
        }
        if ($developer != "") {
            $this->developers[$developer]["task"][] = $task;
            $this->developers[$developer]["weekly_point"] -= $task->getPoint();
            $this->developers[$developer]["weekly_work_duration"] -= $this->getDeveloperTaskTime($task, $this->developers[$developer]);
        }
    }

    private function getDeveloperTaskTime(Task $task, array $developer) {
        return $task->getDuration() * ($task->getDifficulty() / $developer["difficulty"]);
    }
}