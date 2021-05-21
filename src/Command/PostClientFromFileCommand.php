<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PostClientFromFileCommand extends BaseCommand
{
    
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('client:create:file')
            ->setDescription('Create client from json file')
            ->addArgument('inputFile', InputArgument::REQUIRED, "Json file with the client data")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $httpClient = HttpClient::create();
        $url = $input->getOption('apiUrl');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $inputFilename = $input->getArgument('inputFile');
        $inputFile = fopen($inputFilename, 'r');
        $json = fread($inputFile, filesize($inputFilename));
        fclose($inputFile);
        $response = $httpClient->request('POST', $url.'/api/clients', [
            'auth_basic' => [
                $username,
                $password
            ],
            'json' => json_decode($json, true)
        ]);
        try {
            $status = $response->getStatusCode();
        } catch (TransportException $e) {
            $output->writeln($e->getMessage());
            return self::COMMUNICATION_ERROR;
        }
        if (201 == $status) {
            $data = json_decode($response->getContent(), true);
            $id = $data['id'];
            $output->writeln("Client ".$id." successfully created");
            return self::SUCCESS;
        }
        return $this->manageError($response, $output);
    }
}
