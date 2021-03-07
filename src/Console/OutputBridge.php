<?php
declare(strict_types=1);

namespace Zxin\Think\Symfony\Console;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;
use think\console\Output;

class OutputBridge implements OutputInterface
{
    const OPTIONS_MAPPING = [
        self::OUTPUT_NORMAL => Output::OUTPUT_NORMAL,
        self::OUTPUT_RAW => Output::OUTPUT_RAW,
        self::OUTPUT_PLAIN => Output::OUTPUT_PLAIN,

        self::VERBOSITY_QUIET => Output::VERBOSITY_QUIET,
        self::VERBOSITY_NORMAL => Output::VERBOSITY_NORMAL,
        self::VERBOSITY_VERBOSE => Output::VERBOSITY_VERBOSE,
        self::VERBOSITY_VERY_VERBOSE => Output::VERBOSITY_VERY_VERBOSE,
        self::VERBOSITY_DEBUG => Output::VERBOSITY_DEBUG,
    ];

    const VERBOSITY_MAPPING = [
        Output::VERBOSITY_QUIET => self::VERBOSITY_QUIET,
        Output::VERBOSITY_NORMAL => self::VERBOSITY_NORMAL ,
        Output::VERBOSITY_VERBOSE => self::VERBOSITY_VERBOSE,
        Output::VERBOSITY_VERY_VERBOSE => self::VERBOSITY_VERY_VERBOSE,
        Output::VERBOSITY_DEBUG => self::VERBOSITY_DEBUG,
    ];

    /**
     * @var Output
     */
    private $output;

    public function __construct(Output $output)
    {
        $this->output = $output;
    }

    /**
     * @inheritDoc
     */
    public function write($messages, bool $newline = false, int $options = 0)
    {
        $types = self::OUTPUT_NORMAL | self::OUTPUT_RAW | self::OUTPUT_PLAIN;
        $type = $types & $options ?: self::OUTPUT_NORMAL;

        $verbosities = self::VERBOSITY_QUIET | self::VERBOSITY_NORMAL | self::VERBOSITY_VERBOSE | self::VERBOSITY_VERY_VERBOSE | self::VERBOSITY_DEBUG;
        $verbosity = $verbosities & $options ?: self::VERBOSITY_NORMAL;
        $verbosity = self::OPTIONS_MAPPING[$verbosity];

        if ($verbosity > $this->getVerbosity()) {
            return;
        }

        if (!is_iterable($messages)) {
            $messages = [$messages];
        }
        foreach ($messages as $message) {
            $this->output->write($message, $newline, self::OPTIONS_MAPPING[$type]);
        }
    }

    /**
     * @inheritDoc
     */
    public function writeln($messages, int $options = 0)
    {
        $this->write($messages, true, $options);
    }

    /**
     * @inheritDoc
     */
    public function setVerbosity(int $level)
    {
        $this->output->setVerbosity(self::OPTIONS_MAPPING[$level]);
    }

    /**
     * @inheritDoc
     */
    public function getVerbosity()
    {
        return self::VERBOSITY_MAPPING[$this->output->getVerbosity()];
    }

    /**
     * @inheritDoc
     */
    public function isQuiet()
    {
        return $this->output->isQuiet();
    }

    /**
     * @inheritDoc
     */
    public function isVerbose()
    {
        return $this->output->isVerbose();
    }

    /**
     * @inheritDoc
     */
    public function isVeryVerbose()
    {
        return $this->output->isVeryVerbose();
    }

    /**
     * @inheritDoc
     */
    public function isDebug()
    {
        return $this->output->isDebug();
    }

    /**
     * @inheritDoc
     */
    public function setDecorated(bool $decorated)
    {
        // $this->output->setDecorated($decorated);
    }

    /**
     * @inheritDoc
     */
    public function isDecorated()
    {
        // ignore
    }

    public function setFormatter(OutputFormatterInterface $formatter)
    {
        // ignore
    }

    /**
     * @inheritDoc
     */
    public function getFormatter()
    {
        // ignore
    }
}
