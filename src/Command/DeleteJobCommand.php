<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;

class DeleteJobCommand extends Command
{
    protected function configure()
    {
        $this
        ->setName('job:delete')
        ->setDescription('Delete a job')
        ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
        ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
        ->addArgument('id', InputArgument::REQUIRED, "Job's id")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $id = $input->getArgument('id');
        $output->writeln("Delete job ".$id);
        $response = $httpClient->request('DELETE', 'http://127.0.0.1/api/jobs/'.$id, ['auth_basic' => [$username, $password],]);
        if (204 == $response->getStatusCode()){
            $output->writeln("Job ".$id." successfully deleted");
        } else {
            $output->writeln($response->getContent());
        }
    }
}

