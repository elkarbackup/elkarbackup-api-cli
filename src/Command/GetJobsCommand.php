<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;

class GetJobsCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure()
        ->setName('job:list')
        ->setDescription('List jobs')
        ->addOption('name', null, InputOption::VALUE_REQUIRED, "Filter job list by name")
        ->addOption('output', 'o', InputOption::VALUE_REQUIRED, "Output file to save job list");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $url = $input->getOption('apiUrl');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $name = $input->getOption('name');
        if ($name) {
            $filter = "?name=".$name;
        } else {
            $filter = null;
        }
        $response = $httpClient->request('GET', $url.'/api/jobs'.$filter, [
            'auth_basic' => [$username, $password],
        ]);
        $output->writeln("Get jobs");
        $filename = $input->getOption('output');
        if ($filename) {
            $file = fopen($filename, 'w');
            fwrite($file, $response->getContent());
            fclose($file);
        } else {
            $output->writeln($response->getContent());
        }
    }
}
