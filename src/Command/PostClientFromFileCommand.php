<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
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
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, "Output file to save client")
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
        return $this->returnCode($response, $output);
    }
}
