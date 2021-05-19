<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;

class GetClientsCommand extends BaseCommand
{
    public function __construct($apiUrl)
    {
        parent::__construct();
        $this->apiUrl = $apiUrl;
    }

    protected function configure()
    {
        $this
        ->setName('client:list')
        ->setDescription('Gets client list')
        ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
        ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
        ->addOption('name', null, InputOption::VALUE_REQUIRED, "Filter client list by name")
        ->addArgument('file', InputArgument::OPTIONAL, "Output file for the clients' list")
        ;   
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $name = $input->getOption('name');
        if ($name) {
            $filter = "?name=".$name;
        } else {
            $filter = null;
        }
        $response = $httpClient->request('GET', $this->apiUrl.'/api/clients'.$filter, [
            'auth_basic' => [$username, $password],
        ]);
        $output->writeln("Get clients");
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
