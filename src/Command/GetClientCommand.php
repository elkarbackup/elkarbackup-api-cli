<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GetClientCommand extends Command
{
    protected function configure()
    {
        $this
        ->setName('client:details')
        ->setDescription('Get a client')
        ->addArgument('id', InputArgument::REQUIRED, "Client's id")
        ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, "Output file to save client")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $id = $input->getArgument('id');
        $response = $httpClient->request('GET', 'http://127.0.0.1/api/clients/'.$id, ['auth_basic' => ['root', 'root'],]);
        $output->writeln("Get client ".$id);
        $filename = $input->getOption('output');
        if ($filename) {
            $file = fopen($filename, 'w');
            fwrite($file, $response->getContent());
            fclose($file);
        } else {
            $output->writeln($response->getContent());
        }
    }
}

