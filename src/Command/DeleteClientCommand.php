<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;

class DeleteClientCommand extends Command
{
    public function __construct($apiUrl)
    {
        parent::__construct();
        $this->apiUrl = $apiUrl;
    }

    protected function configure()
    {
        $this
        ->setName('client:delete')
        ->setDescription('Delete a client')
        ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
        ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
        ->addArgument('id', InputArgument::REQUIRED, "Client's id")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $id = $input->getArgument('id');
        $output->writeln("Delete client ".$id);
        $response = $httpClient->request('DELETE', $this->apiUrl.'/api/clients/'.$id, ['auth_basic' => [$username, $password],]);
        if (204 == $response->getStatusCode()){
            $output->writeln("Client ".$id." successfully deleted");
        } else {
            $output->writeln($response->getContent());
        }
    }
}
