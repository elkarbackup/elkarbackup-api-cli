<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;

class UpdateJobFromFileCommand extends BaseCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('job:update:file')
            ->setDescription('Update job from json file')
            ->addArgument('id', InputArgument::REQUIRED, "Id of the job to update")
            ->addArgument('inputFile', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $id = $input->getArgument('id');
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
        try {
            $status = $response->getStatusCode();
        } catch (TransportException $e) {
            $output->writeln($e->getMessage());
            return self::ERROR;
        }
        if (200 == $status) {
            $output->writeln("Job ".$id." successfully updated");
            return self::SUCCESS;
        }
        return $this->manageError($response, $output);
    }
}