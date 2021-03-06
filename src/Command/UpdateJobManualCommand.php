<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class UpdateJobManualCommand extends BaseCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('job:update:manual')
            ->setDescription('Update job inserting parameters manually')
            ->addArgument('id', InputArgument::REQUIRED, "Id of the job to update")
            ->addOption('backupLocation', null, InputOption::VALUE_OPTIONAL, "Location of job's backups", 1)
            ->addOption('client', null, InputOption::VALUE_REQUIRED, "Job's client id")
            ->addOption('description', null, InputOption::VALUE_OPTIONAL, "Description of the job")
            ->addOption('exclude', null, InputOption::VALUE_OPTIONAL, "Exclude pattern")
            ->addOption('include', null, InputOption::VALUE_OPTIONAL, "Include pattern")
            ->addOption('isActive', null, InputOption::VALUE_OPTIONAL, "No snapshots will be taken if false", true)
            ->addOption('minNotificationLevel', null, InputOption::VALUE_OPTIONAL, "Notify only of events over the priority threshold", 400)
            ->addOption('name', null, InputOption::VALUE_REQUIRED, "Job's name")
            ->addOption('notificationsEmail', null, InputOption::VALUE_OPTIONAL, "Notification's email address")
            ->addOption('notificationsTo', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, "Who notify the events", ['owner'])
            ->addOption('path', null, InputOption::VALUE_REQUIRED, "path or name of the rsync resource in the client")
            ->addOption('policy', null, InputOption::VALUE_OPTIONAL, "Id of the policy fot this job", 1)
            ->addOption('postScript', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, "PostScripts fot this job", [])
            ->addOption('preScript', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, "PreScripts fot this job", [])
            ->addOption('token', null, InputOption::VALUE_OPTIONAL, "Token to allow anonymous remote job executions")
            ->addOption('useLocalPermissions', null, InputOption::VALUE_OPTIONAL, "Keep permissions exactly as in the source files", true)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->checkRequiredOptionsAreNotEmpty($input);
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        try {
            $id = $this->parseInt($input->getArgument('id'));
        } catch (\InvalidArgumentException $e) {
            $output->writeln("Id of the job must be a integer");
            return self::INVALID_ARGUMENT;
        }
        $url = $input->getOption('apiUrl');
        try {
            $json = [
                'backupLocation' => $this->parseInt($input->getOption('backupLocation')),
                'client' => $this->parseInt($input->getOption('client')),
                'description' => $input->getOption('description'),
                'exclude' => $input->getOption('exclude'),
                'include' => $input->getOption('include'),
                'isActive' => $this->getBoolean($input->getOption('isActive')),
                'minNotificationLevel' => $this->parseInt($input->getOption('minNotificationLevel')),
                'name' => $input->getOption('name'),
                'notificationsEmail' => $input->getOption('notificationsEmail'),
                'notificationsTo' => $input->getOption('notificationsTo'),
                'path' => $input->getOption('path'),
                'policy' => $this->parseInt($input->getOption('policy')),
                'postScripts' => $this->getScripts($input->getOption('postScript')),
                'preScripts' => $this->getScripts($input->getOption('preScript')),
                'token' => $input->getOption('token'),
                'useLocalPermissions' => $this->getBoolean($input->getOption('useLocalPermissions'))
            ];
        } catch (\InvalidArgumentException $e){
            $output->writeln($e->getMessage());
            return self::INVALID_ARGUMENT;
        }
        $response = $httpClient->request('PUT', $url.'/api/jobs/'.$id, [
            'auth_basic' => [
                $username,
                $password
            ],
            'json' => $json
        ]);
        try {
            $status = $response->getStatusCode();
        } catch (TransportException $e) {
            $output->writeln($e->getMessage());
            return self::ERROR;
        }
        if (200 == $status) {
            $output->writeln("Job ".$id." successfully updated");
            return self::COMMUNICATION_ERROR;
        }
        return $this->manageError($response, $output);
    }
}
