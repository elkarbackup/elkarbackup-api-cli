<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

class UpdateJobFromFileCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure()
        ->setName('job:update:file')
        ->setDescription('Update job from json file')
        ->addArgument('id', InputArgument::REQUIRED, "Id of the job to update")
        ->addArgument('inputFile', InputArgument::REQUIRED, "Json file with the job data")
        ->addOption('output', 'o', InputOption::VALUE_REQUIRED, "Output file to save job");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $id = $this->parseInt($input->getArgument('id'));
        $url = $input->getOption('apiUrl');
        $inputFilename = $input->getArgument('inputFile');
        $inputFile = fopen($inputFilename, 'r');
        $json = fread($inputFile, filesize($inputFilename));
        fclose($inputFile);
        $response = $httpClient->request('PUT', $url.'/api/jobs/'.$id, [
            'auth_basic' => [
                $username,
                $password
            ],
            'json' => json_decode($json, true)
        ]);
        if (201 == $response->getStatusCode()) {
            $output->writeln("Job updated successfully");
        } else {
            $output->writeln("Could not update job");
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
