<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class BaseCommand extends Command
{
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

    protected function configure()
    {
        $this
        ->addArgument('username', InputArgument::REQUIRED, "Username for authentication")
        ->addArgument('password', InputArgument::REQUIRED, "Password for authentication")
        ->addOption('apiUrl', null, InputOption::VALUE_OPTIONAL, "Url of the api", "http://127.0.0.1");
        return $this;
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
}

