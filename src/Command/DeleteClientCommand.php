<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Input\InputArgument;

class DeleteClientCommand extends Command
{
    protected function configure()
    {
        $this
        ->setName('client:delete')
        ->setDescription('Delete a client')
        ->addArgument('id', InputArgument::REQUIRED, "Client's id")
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $httpClient = HttpClient::create();
        $id = $input->getArgument('id');
        $output->writeln("Delete client ".$id);
        $response = $httpClient->request('DELETE', 'http://127.0.0.1/api/clients/'.$id, ['auth_basic' => ['root', 'root'],]);
        if (204 == $response->getStatusCode()){
            $output->writeln("Client ".$id." successfully deleted");
        } else {
            $output->writeln($response->getContent());
        }
    }
}
