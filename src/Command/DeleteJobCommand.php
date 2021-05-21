<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $httpClient = HttpClient::create();
        $url = $input->getOption('apiUrl');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $id = $input->getArgument('id');
        $output->writeln("Delete job ".$id);
        $response = $httpClient->request('DELETE', $url.'/api/jobs/'.$id, ['auth_basic' => [$username, $password],]);
        try {
            $status = $response->getStatusCode();
        } catch (TransportException $e) {
            $output->writeln($e->getMessage());
            return self::COMMUNICATION_ERROR;
        }
        if (204 == $status) {
            $output->writeln("Job ".$id." succesfully deleted");
            return self::SUCCESS;
        }
        return $this->manageError($response, $output);
    }
}
