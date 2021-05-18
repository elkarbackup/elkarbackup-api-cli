<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GetJobCommand extends Command
{
    protected function configure()
    {
        $this
        ->setName('job:details')
        ->setDescription('Get a job\'s details')
        ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
        ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
        ->addArgument('id', InputArgument::REQUIRED, "Job's id")
        ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, "Output file to save job")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $id = $input->getArgument('id');
        $response = $httpClient->request('GET', 'http://127.0.0.1/api/jobs/'.$id, ['auth_basic' => [$username, $password],]);
        $output->writeln("Get job ".$id);
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

