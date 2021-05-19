<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;

class GetJobsCommand extends Command
{
    protected function configure()
    {
        $this
        ->setName('job:list')
        ->setDescription('List jobs')
        ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
        ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
        ->addOption('name', null, InputOption::VALUE_REQUIRED, "Filter job list by name")
        ->addArgument('url', InputArgument::OPTIONAL, "Url of the api", "http://127.0.0.1")
        ->addArgument('file', InputArgument::OPTIONAL, "Output file for the jobs' list");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $url = $input->getArgument('url');
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
        $filename = $input->getArgument('file');
        if ($filename) {
            $file = fopen($filename, 'w');
            fwrite($file, $response->getContent());
            fclose($file);
        } else {
            $output->writeln($response->getContent());
        }
    }
}
