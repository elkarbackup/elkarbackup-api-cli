<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;

class DeleteJobCommand extends BaseCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('job:delete')
            ->setDescription('Delete a job')
            ->addArgument('id', InputArgument::REQUIRED, "Job's id")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $url = $input->getOption('apiUrl');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $id = $input->getArgument('id');
        $output->writeln("Delete job ".$id);
        $response = $httpClient->request('DELETE', $url.'/api/jobs/'.$id, ['auth_basic' => [$username, $password],]);
        if (204 == $response->getStatusCode()){
            $output->writeln("Job ".$id." successfully deleted");
        } else {
            $output->writeln($response->getContent());
        }
    }
}
