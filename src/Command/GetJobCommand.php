<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GetJobCommand extends BaseCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('job:details')
            ->setDescription('Get a job\'s details')
            ->addArgument('id', InputArgument::REQUIRED, "Job's id")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        try {
            $id = $this->parseInt($input->getArgument('id'));
        } catch (\InvalidArgumentException $e) {
            $output->writeln("Id of the job must be a integer");
            return self::INVALID_ARGUMENT;
        }
        $url = $input->getOption('apiUrl');
        $response = $httpClient->request('GET', $url.'/api/jobs/'.$id, ['auth_basic' => [$username, $password],]);
        try {
            $status = $response->getStatusCode();
        } catch (TransportException $e) {
            $output->writeln($e->getMessage());
            return self::COMMUNICATION_ERROR;
        }
        if (200 == $status) {
            $output->writeln($response->getContent());
            return self::SUCCESS;
        }
        return $this->manageError($response, $output);
    }
}
