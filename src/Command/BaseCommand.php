<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

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

