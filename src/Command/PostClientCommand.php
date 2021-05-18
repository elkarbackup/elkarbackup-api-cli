<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PostClientCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('client:create:manual')
            ->setDescription('Create client inserting parameters manually')
            ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
            ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
            ->addOption('name', null, InputOption::VALUE_REQUIRED, "Client's name")
            ->addOption('description', null, InputOption::VALUE_OPTIONAL)
            ->addOption('isActive', null, InputOption::VALUE_OPTIONAL, "Client is active", true)
            ->addOption('maxParallelJobs', null, InputOption::VALUE_OPTIONAL, '', 1)
            ->addOption('owner', null, InputOption::VALUE_REQUIRED, "Client's owner")
            ->addOption('postScript', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, '', [])
            ->addOption('preScript', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, '', [])
            ->addOption('quota', null, InputOption::VALUE_OPTIONAL, '', - 1)
            ->addOption('rsyncLongArgs', null, InputOption::VALUE_OPTIONAL)
            ->addOption('rsyncShortArgs', null, InputOption::VALUE_OPTIONAL)
            ->addOption('sshArgs', null, InputOption::VALUE_OPTIONAL)
            ->addOption('url', null, InputOption::VALUE_OPTIONAL)
            ->addArgument('outputFile', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkRequiredOptionsAreNotEmpty($input);
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $json = [
            'description' => $input->getOption('description'),
            'isActive' => $this->getIsActive($input->getOption('isActive')),
            'maxParallelJobs' => $this->parseInt($input->getOption('maxParallelJobs')),
            'name' => $input->getOption('name'),
            'owner' => $this->parseInt($input->getOption('owner')),
            'postScripts' => $this->getScripts($input->getOption('postScript')),
            'preScripts' => $this->getScripts($input->getOption('preScript')),
            'quota' => $this->parseInt($input->getOption('quota')),
            'rsyncLongArgs' => $input->getOption('rsyncLongArgs'),
            'rsyncShortArgs' => $input->getOption('rsyncShortArgs'),
            'sshArgs' => $input->getOption('sshArgs'),
            'url' => $input->getOption('url')
        ];
        $response = $httpClient->request('POST', 'http://127.0.0.1/api/clients', [
            'auth_basic' => [
                $username,
                $password
            ],
            'json' => $json
        ]);
        if (201 == $response->getStatusCode()) {
            $output->writeln("Client created successfully");
        } else {
            $output->writeln("Could not create client");
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

