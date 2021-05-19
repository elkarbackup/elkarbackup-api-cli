<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class UpdateClientFromFileCommand extends BaseCommand
{
    public function __construct($apiUrl)
    {
        parent::__construct();
        $this->apiUrl = $apiUrl;
    }

    protected function configure()
    {
        $this->setName('client:update:file')
        ->setDescription('Update client from json file')
        ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
        ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
        ->addArgument('id', InputArgument::REQUIRED, "Id of the client to update")
        ->addArgument('inputFile', InputArgument::REQUIRED, "Json file with data to replace")
        ->addArgument('outputFile', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $id = $input->getArgument('id');
        $inputFilename = $input->getArgument('inputFile');
        $inputFile = fopen($inputFilename, 'r');
        $json = fread($inputFile, filesize($inputFilename));
        fclose($inputFile);
        $response = $httpClient->request('PUT', $this->apiUrl.'/api/clients/'.$id, [
            'auth_basic' => [
                $username,
                $password
            ],
            'json' => json_decode($json, true)
        ]);
        if (200 == $response->getStatusCode()) {
            $output->writeln("Client ".$id." updated successfully");
        } else {
            $output->writeln("Could not update client ".$id);
        }
        $outputFilename = $input->getArgument('outputFile');
        if ($outputFilename) {
            $file = fopen($outputFilename, 'w');
            fwrite($file, $response->getContent());
        } else {
            $output->writeln($response->getContent());
        }
    }
}
