<?php
declare(strict_types=1);

namespace Zxin\Think\Symfony\Console;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use think\console\Input;
use think\console\input\Argument;
use function is_scalar;

class InputBridge implements InputInterface
{

    /**
     * @var Input
     */
    private $input;

    public function __construct(Input $input)
    {
        $this->input = $input;
    }

    /**
     * @inheritDoc
     */
    public function getFirstArgument(): ?string
    {
        return $this->input->getFirstArgument();
    }

    /**
     * @inheritDoc
     */
    public function hasParameterOption($values, bool $onlyParams = false): bool
    {
        return $this->input->hasParameterOption($values);
    }

    /**
     * @inheritDoc
     */
    public function getParameterOption($values, $default = false, bool $onlyParams = false)
    {
        return $this->input->getParameterOption($values, $default);
    }

    /**
     * @inheritDoc
     */
    public function bind(InputDefinition $definition)
    {
        // ignore
    }

    /**
     * @inheritDoc
     */
    public function validate()
    {
        $this->input->validate();
    }

    /**
     * @inheritDoc
     */
    public function getArguments(): array
    {
        // 可能是冗余代码，TP注解错误，先观察
        $values = [];
        foreach ($this->input->getArguments() as $name => $argument) {
            if ($argument instanceof Argument) {
                $values[$name] = $argument->getDefault();
            } elseif (is_scalar($argument)) {
                $values[$name] = $argument;
            } else {
                throw new \Error("getArguments convert fail {$name}");
            }
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function getArgument(string $name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * @inheritDoc
     */
    public function setArgument(string $name, $value)
    {
        $this->input->setArgument($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function hasArgument($name): bool
    {
        return $this->input->hasArgument($name);
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->input->getOptions();
    }

    /**
     * @inheritDoc
     */
    public function getOption(string $name)
    {
        return $this->input->getOption($name);
    }

    /**
     * @inheritDoc
     */
    public function setOption(string $name, $value)
    {
        $this->input->setOption($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function hasOption(string $name): bool
    {
        return $this->input->hasOption($name);
    }

    /**
     * @inheritDoc
     */
    public function isInteractive(): bool
    {
        return $this->input->isInteractive();
    }

    /**
     * @inheritDoc
     */
    public function setInteractive(bool $interactive)
    {
        $this->input->setInteractive($interactive);
    }
}
