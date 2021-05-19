<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PostClientFromFileCommand extends Command
{
    
    protected function configure()
    {
        $this->setName('client:create:file')
        ->setDescription('Create client from json file')
        ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
        ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
        ->addArgument('inputFile', InputArgument::REQUIRED)
        ->addArgument('url', InputArgument::OPTIONAL, "Url of the api", "http://127.0.0.1")
        ->addOption('output', 'o', InputOption::VALUE_REQUIRED, "Output file to save client");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $url = $input->getArgument('url');
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
        if (201 == $response->getStatusCode()) {
            $output->writeln("Client created successfully");
        } else {
            $output->writeln("Could not create client");
        }
        $outputFilename = $input->getOption('output');
        if ($outputFilename) {
            $file = fopen($outputFilename, 'w');
            fwrite($file, $response->getContent());
        } else {
            $output->writeln($response->getContent());
        }
    }
}
