<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Zxin\Think\Symfony\Console\InputBridge;
use Zxin\Think\Symfony\Console\OutputBridge;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Definition;
use think\console\input\Option;

$count = 0;
function check($expected, $actual, string $message): void
{
    global $count;
    ++$count;
    if ($expected !== $actual) {
        throw new RuntimeException($message . ': ' . var_export($actual, true));
    }
}

// Loading concrete bridge classes against the installed interfaces catches fatal
// signature incompatibilities that isolated mocks cannot detect.
$native = new Input(['command', 'one', 'two words', '--name=value']);
$native->bind(new Definition([
    new Argument('command', Argument::REQUIRED),
    new Argument('items', Argument::IS_ARRAY),
    new Option('name', null, Option::VALUE_REQUIRED),
    new Option('optional', null, Option::VALUE_OPTIONAL),
]));
$input = new InputBridge($native);
check(true, $input instanceof InputInterface, 'input interface loads');
check('command', $input->getFirstArgument(), 'first argument');
check(true, $input->hasParameterOption('--name'), 'raw option exists');
check('value', $input->getParameterOption('--name'), 'raw option value');
check(['fallback'], $input->getParameterOption('--missing', ['fallback']), 'mixed default');
check((string) $native, (string) $input, 'escaped command line');
check(true, strpos((string) $input, "'two words'") !== false, 'spaces are escaped');
$input->bind(new InputDefinition()); // Deliberately retain ThinkPHP binding.
$input->validate();
check($native->getArguments(), $input->getArguments(), 'array arguments');
check(['one', 'two words'], $input->getArgument('items'), 'argument read');
$input->setArgument('items', ['changed']);
check(['changed'], $native->getArgument('items'), 'argument write');
check(true, $input->hasArgument('items'), 'argument existence');
check($native->getOptions(), $input->getOptions(), 'option read all');
check('value', $input->getOption('name'), 'option read');
$input->setOption('name', 'updated');
check('updated', $native->getOption('name'), 'option write');
check(true, $input->hasOption('name'), 'option existence');
$input->setInteractive(false);
check(false, $input->isInteractive(), 'interactivity');
$empty = new Input([]);
$empty->bind(new Definition([new Argument('optional', Argument::OPTIONAL)]));
check(['optional' => null], (new InputBridge($empty))->getArguments(), 'null argument default');

$outputNative = new class('buffer') extends Output {
    public array $types = [];
    public function write(string $messages, bool $newline = false, int $type = 0): void
    {
        $this->types[] = $type;
        parent::write($messages, $newline, $type);
    }
};
$output = new OutputBridge($outputNative);
check(true, $output instanceof OutputInterface, 'output interface loads');
foreach ([OutputInterface::VERBOSITY_QUIET, OutputInterface::VERBOSITY_NORMAL,
    OutputInterface::VERBOSITY_VERBOSE, OutputInterface::VERBOSITY_VERY_VERBOSE,
    OutputInterface::VERBOSITY_DEBUG] as $level) {
    $output->setVerbosity($level);
    check($level, $output->getVerbosity(), 'verbosity round trip');
    check($outputNative->isQuiet(), $output->isQuiet(), 'quiet delegation');
    check($outputNative->isVerbose(), $output->isVerbose(), 'verbose delegation');
    check($outputNative->isVeryVerbose(), $output->isVeryVerbose(), 'very verbose delegation');
    check($outputNative->isDebug(), $output->isDebug(), 'debug delegation');
}
$output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
$output->write('hidden', false, OutputInterface::VERBOSITY_DEBUG);
check('', $outputNative->fetch(), 'filter debug at normal verbosity');
$output->write('hello');
$output->writeln((static function () { yield ' one'; yield 'two'; })());
check('hello one' . PHP_EOL . 'two' . PHP_EOL, $outputNative->fetch(), 'iterable output');
$output->write('<info>raw</info>', false, OutputInterface::OUTPUT_RAW);
check('<info>raw</info>', $outputNative->fetch(), 'raw output');
check(Output::OUTPUT_RAW, end($outputNative->types), 'raw type mapping');
$output->write('<info>plain</info>', false, OutputInterface::OUTPUT_PLAIN);
check('<info>plain</info>', $outputNative->fetch(), 'buffer delegates message unchanged');
check(Output::OUTPUT_PLAIN, end($outputNative->types), 'plain type mapping');
$output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
$output->write('hidden');
check('', $outputNative->fetch(), 'quiet output');
$output->setVerbosity(OutputBridge::VERBOSITY_SILENT);
check(true, $output->isSilent(), 'silent mode');
check(false, $output->isQuiet(), 'silent is distinct from quiet');
check(OutputBridge::VERBOSITY_SILENT, $output->getVerbosity(), 'silent round trip');
$output->write('hidden', false, OutputInterface::VERBOSITY_QUIET);
check('', $outputNative->fetch(), 'silent even for quiet messages');
$output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
check(false, $output->isSilent(), 'leave silent mode');
$output->setDecorated(true);
check(false, $output->isDecorated(), 'existing undecorated bridge contract');
$output->setFormatter(new OutputFormatter());
check(true, $output->getFormatter() instanceof OutputFormatter, 'formatter interface');

printf("OK: %d assertions (PHP %s, Symfony Console %s, ThinkPHP %s)\n", $count, PHP_VERSION,
    Composer\InstalledVersions::getPrettyVersion('symfony/console'),
    Composer\InstalledVersions::getPrettyVersion('topthink/framework'));
