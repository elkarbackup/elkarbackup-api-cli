<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class PostJobFromFileCommand extends Command
{
    protected function configure()
    {
        $this->setName('job:create:file')
        ->setDescription('Create job from json file')
        ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
        ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
        ->addArgument('inputFile', InputArgument::REQUIRED)
        ->addArgument('url', InputArgument::OPTIONAL, "Url of the api", "http://127.0.0.1")
        ->addArgument('outputFile', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $url = $input->getArgument('url');
        $inputFilename = $input->getArgument('inputFile');
        $inputFile = fopen($inputFilename, 'r');
        $json = fread($inputFile, filesize($inputFilename));
        fclose($inputFile);
        $response = $httpClient->request('POST', $url.'/api/jobs', [
            'auth_basic' => [
                $username,
                $password
            ],
            'json' => json_decode($json, true)
        ]);
        if (201 == $response->getStatusCode()) {
            $output->writeln("Job created successfully");
        } else {
            $output->writeln("Could not create job");
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
