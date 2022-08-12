<?php

namespace App\Controller;

use App\Service\TaskShareService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_default")
     */
    public function index(TaskShareService $taskShareService): Response
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'devs' => $taskShareService->shareTask()
        ]);
    }
}
