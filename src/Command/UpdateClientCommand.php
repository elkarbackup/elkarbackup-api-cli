<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class UpdateClientCommand extends Command
{
    
    protected function configure()
    {
        $this->setName('client:update')
        ->setDescription('Create client')
        ->addArgument('id', InputArgument::REQUIRED, "Id of the client to update")
        ->addOption('name', null, InputOption::VALUE_REQUIRED, "Client's name")
        ->addOption('description', null, InputOption::VALUE_OPTIONAL)
        ->addOption('isActive', null, InputOption::VALUE_OPTIONAL, "Client is active", true)
        ->addOption('maxParallelJobs', null, InputOption::VALUE_OPTIONAL, '', 1)
        ->addOption('owner', null, InputOption::VALUE_REQUIRED, "Client's owner")
        ->addOption('postScripts', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, '', [])
        ->addOption('preScripts', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, '', [])
        ->addOption('quota', null, InputOption::VALUE_OPTIONAL, '', - 1)
        ->addOption('rsyncLongArgs', null, InputOption::VALUE_OPTIONAL)
        ->addOption('rsyncShortArgs', null, InputOption::VALUE_OPTIONAL)
        ->addOption('sshArgs', null, InputOption::VALUE_OPTIONAL)
        ->addOption('url', null, InputOption::VALUE_OPTIONAL)
        ->addArgument('outputFile', InputArgument::OPTIONAL);
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $id = $input->getArgument('id');
        $json = [
            'description' => $input->getOption('description'),
            'isActive' => $input->getOption('isActive'),
            'maxParallelJobs' => $input->getOption('maxParallelJobs'),
            'name' => $input->getOption('name'),
            'owner' => (int)$input->getOption('owner'),
            'postScripts' => $input->getOption('postScripts'),
            'preScripts' => $input->getOption('preScripts'),
            'quota' => $input->getOption('quota'),
            'rsyncLongArgs' => $input->getOption('rsyncLongArgs'),
            'rsyncShortArgs' => $input->getOption('rsyncShortArgs'),
            'sshArgs' => $input->getOption('sshArgs'),
            'url' => $input->getOption('url')
        ];
        $response = $httpClient->request('PUT', 'http://127.0.0.1/api/clients/'.$id, [
            'auth_basic' => [
                'root',
                'root'
            ],
            'json' => $json
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
