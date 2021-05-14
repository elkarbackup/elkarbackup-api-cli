<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class PostClientCommand extends Command
{
    protected function configure()
    {
        $this
        ->setName('PostClient')
        ->setDescription('Create client')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'http://127.0.0.1/api/clients', [
            'auth_basic' => ['root', 'root'],
            'json' => [
                'description' => 'description',
                'isActive' => true,
                'maxParallelJobs' => 1,
                'name' => 'clientCLI',
                'owner' => 1,
                'quota' => -1,
                'url' => 'root@172.17.0.1'
            ]
        ]);
        $output->writeln($response->getContent());
    }
}

