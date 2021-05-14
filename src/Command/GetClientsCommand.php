<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class GetClientsCommand extends Command
{
    protected function configure()
    {
        $this
        ->setName('GetClients')
        ->setDescription('Gets client list')
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://127.0.0.1/api/clients', ['auth_basic' => ['root', 'root'],]);
        $output->writeln($response->getContent());
    }
}

