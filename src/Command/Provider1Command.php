<?php

namespace App\Command;

use App\Service\TodoService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Provider1Command extends Command
{
    protected static $defaultName = 'app:provider-1';
    protected static $defaultDescription = 'get todo from provider 1';

    private $client;
    private $todos;
    private $todoService;

    public function __construct(HttpClientInterface $client, TodoService $todoService)
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
            'http://www.mocky.io/v2/5d47f24c330000623fa3ebfa'
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

        foreach ($content as $todo) {
            $this->todos[] = [
                'name' => $todo['id'],
                'duration' => $todo['sure'],
                'difficulty' => $todo['zorluk']
            ];
        }

        $this->todoService->addMultipleTask($this->todos);

        $io->success(sprintf('Success: %s Found %s task', $statusCode, count($this->todos)));
        return 0;
    }
}
