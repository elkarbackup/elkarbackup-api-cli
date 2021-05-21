<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BaseCommand extends Command
{
    const SUCCESS = 0;
    const ERROR = 1;
    const UNAUTHORIZED = 2;
    const INVALID_ARGUMENT = 3;
    const NOT_FOUND = 4;
    const COMMUNICATION_ERROR = 5;
    
    protected function checkRequiredOptionsAreNotEmpty(InputInterface $input): void
    {
        $options = $this->getDefinition()->getOptions();
        foreach ($options as $option) {
            $name  = $option->getName();
            $value = $input->getOption($name);
            if ($option->isValueRequired() && (null == $value || '' == $value)) {
                throw new \InvalidArgumentException(sprintf('The required option %s is not set', $name));
            }
        }
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
            ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
            ->addOption('apiUrl', null, InputOption::VALUE_OPTIONAL, "Url of the api", "http://127.0.0.1")
        ;
    }

    protected function getIsActive(string $isActive): bool
    {
        if ("false" === $isActive) {
            return false;
        }
        return true;
    }

    protected function getScripts(array $scripts): array
    {
        $return = [];
        foreach ($scripts as $script){
            $return[] = $this->parseInt($script);
        }
        return $return;
    }

    protected function parseInt(string $string): int
    {
        if (is_numeric($string)) {
            return (int) $string;
        }
        throw new \InvalidArgumentException("Parameter must be integer");
    }

    protected function manageError(ResponseInterface $response, OutputInterface $output): int
    {
        $status  = $response->getStatusCode();
        try {
            $response->getContent();
        } catch (\Exception $e) {
            $message = $e->getMessage();
            switch ($status) {
                case 404:
                    $output->writeln($message);
                    return self::NOT_FOUND;
                case 422:
                    $output->writeln($message);
                    return self::INVALID_ARGUMENT;
                default:
                    $output->writeln($message);
                    return self::ERROR;
            }
        }
        return self::ERROR;
    }
}

