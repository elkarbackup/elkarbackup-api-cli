<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;

class PostJobFromFileCommand extends BaseCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('job:create:file')
            ->setDescription('Create job from json file')
            ->addArgument('inputFile', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $url = $input->getOption('apiUrl');
        $inputFilename = $input->getArgument('inputFile');
        $inputFile = fopen($inputFilename, 'r');
        if ($inputFile){
            $json = fread($inputFile, filesize($inputFilename));
            fclose($inputFile);
        } else {
            $output->writeln("Error with the file");
            return self::INVALID_ARGUMENT;
        }
        $response = $httpClient->request('POST', $url.'/api/jobs', [
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
            $output->writeln("Job ".$id." successfully created");
            return self::SUCCESS;
        }
        return $this->manageError($response, $output);
    }
}
