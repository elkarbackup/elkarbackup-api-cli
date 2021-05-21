<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpClient\Exception\TransportException;

class GetClientCommand extends BaseCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('client:details')
            ->setDescription('Get a client')
            ->addArgument('id', InputArgument::REQUIRED, "Client's id")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $httpClient = HttpClient::create();
        $url = $input->getOption('apiUrl');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        try {
            $id = $this->parseInt($input->getArgument('id'));
        } catch (\InvalidArgumentException $e) {
            $output->writeln("Id of the client must be a integer");
            return self::INVALID_ARGUMENT;
        }
        $response = $httpClient->request('GET', $url.'/api/clients/'.$id, ['auth_basic' => [$username, $password],]);
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
