<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;

class GetClientsCommand extends Command
{
    protected function configure()
    {
        $this
        ->setName('client:list')
        ->setDescription('Gets client list')
        ->addArgument('file', InputArgument::OPTIONAL, "Output file for the clients' list")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://127.0.0.1/api/clients', ['auth_basic' => ['root', 'root'],]);
        $output->writeln("Get clients");
        $filename = $input->getArgument('file');
        if ($filename) {
            $file = fopen($filename, 'w');
            fwrite($file, $response->getContent());
            fclose($file);
        } else {
            $output->writeln($response->getContent());
        }
    }
}

