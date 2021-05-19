<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GetClientCommand extends BaseCommand
{
    public function __construct($apiUrl)
    {
        parent::__construct();
        $this->apiUrl = $apiUrl;
    }

    protected function configure()
    {
        $this
        ->setName('client:details')
        ->setDescription('Get a client')
        ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
        ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
        ->addArgument('id', InputArgument::REQUIRED, "Client's id")
        ->addOption('output', 'o', InputOption::VALUE_OPTIONAL, "Output file to save client")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $id = $input->getArgument('id');
        $response = $httpClient->request('GET', $this->apiUrl.'/api/clients/'.$id, ['auth_basic' => [$username, $password],]);
        $output->writeln("Get client ".$id);
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
