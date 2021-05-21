<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PostClientCommand extends BaseCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('client:create:manual')
            ->setDescription('Create client inserting parameters manually')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, "Client's name")
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, "Description of the client")
            ->addOption('isActive', null, InputOption::VALUE_OPTIONAL, "No snapshots will be taken if false", true)
            ->addOption('maxParallelJobs', null, InputOption::VALUE_OPTIONAL, "Maximum parallel jobs that are allowed to be executed", 1)
            ->addOption('owner', null, InputOption::VALUE_REQUIRED, "Client's owner")
            ->addOption('postScript', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, "PostScripts for this client", [])
            ->addOption('preScript', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, "PreScripts for this client", [])
            ->addOption('quota', null, InputOption::VALUE_OPTIONAL, "Amount space in GB is allowed to use. -1 means 'no limit'", - 1)
            ->addOption('rsyncLongArgs', null, InputOption::VALUE_OPTIONAL, "Custom Rsync long args")
            ->addOption('rsyncShortArgs', null, InputOption::VALUE_OPTIONAL, "Custom Rsync short args")
            ->addOption('sshArgs', null, InputOption::VALUE_OPTIONAL, "Custom ssh args")
            ->addOption('url', null, InputOption::VALUE_OPTIONAL, "Connection string for the client")
            ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, "Output file to save client")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->checkRequiredOptionsAreNotEmpty($input);
        $httpClient = HttpClient::create();
        $url = $input->getOption('apiUrl');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        try {
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
        } catch (\InvalidArgumentException $e){
            $output->writeln($e->getMessage());
            return self::INVALID_ARGUMENT;
        }

        $response = $httpClient->request('POST', $url.'/api/clients', [
            'auth_basic' => [
                $username,
                $password
            ],
            'json' => $json
        ]);
        return $this->returnCode($response, $output);
    }
}
