<?php
namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GetClientCommand extends BaseCommand
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('client:details')
            ->setDescription('Get a client')
            ->addArgument('id', InputArgument::REQUIRED, "Client's id")
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, "Output file to save client")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $url = $input->getOption('apiUrl');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $id = $this->parseInt($input->getArgument('id'));
        $response = $httpClient->request('GET', $url.'/api/clients/'.$id, ['auth_basic' => [$username, $password],]);
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
