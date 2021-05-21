<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class UpdateClientFromFileCommand extends BaseCommand
{
    
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('client:update:file')
            ->setDescription('Update client from json file')
            ->addArgument('id', InputArgument::REQUIRED, "Id of the client to update")
            ->addArgument('inputFile', InputArgument::REQUIRED, "Json file with data to replace")
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
        $inputFilename = $input->getArgument('inputFile');
        $inputFile = fopen($inputFilename, 'r');
        if ($inputFile){
            $json = fread($inputFile, filesize($inputFilename));
            fclose($inputFile);
        } else {
            $output->writeln("Error with the file");
            return self::INVALID_ARGUMENT;
        }
        $response = $httpClient->request('PUT', $url.'/api/clients/'.$id, [
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
        if (200 == $status) {
            $output->writeln("Client ".$id." successfully updated");
            return self::SUCCESS;
        }
        return $this->manageError($response, $output);
    }
}
