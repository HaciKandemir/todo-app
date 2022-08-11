<?php

namespace App\Command;

use App\Service\TaskService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Provider2Command extends Command
{
    protected static $defaultName = 'app:provider-2';
    protected static $defaultDescription = 'get todo from provider 2';

    private $client;
    private $todos;
    private $todoService;

    public function __construct(HttpClientInterface $client, TaskService $todoService)
    {
        $this->client = $client;
        $this->todos = [];
        $this->todoService = $todoService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $response = $this->client->request(
            'GET',
            'http://www.mocky.io/v2/5d47f235330000623fa3ebf7'
        );

        $statusCode = $response->getStatusCode();

        if  ($statusCode !== 200) {
            $io->error(sprintf('Error: %s', $statusCode));
            return 1;
        }

        $content = $response->toArray();

        if (empty($content)) {
            $io->text('No todo found');
            return 1;
        }

        foreach ($content as $id => $todo) {
            $id = array_key_first($todo);
            $this->todos[] = [
                'name' => $id,
                'duration' => $todo[$id]['estimated_duration'],
                'difficulty' => $todo[$id]['level']
            ];
        }

        $this->todoService->addMultipleTask($this->todos);

        $io->success(sprintf('Success: %s Found %s task', $statusCode, count($this->todos)));
        return 0;
    }
}
