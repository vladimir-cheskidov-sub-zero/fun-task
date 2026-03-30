<?php

declare(strict_types=1);

namespace FunTask\Bridge\Console\Command;

use FunTask\Application\Category\BuildMenu;
use FunTask\Application\Category\BuildMenuService;
use FunTask\Application\Dto\Menu;
use FunTask\Application\Dto\MenuItem;
use FunTask\Application\Vo\BuildMenuRegion;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;

final class CategoriesMenuCommand extends Command
{
    protected static $defaultName = 'categories:menu';
    private BuildMenuService $buildMenuUseCase;
    public function __construct(BuildMenuService $buildMenuUseCase)
    {
        parent::__construct();
        $this->buildMenuUseCase = $buildMenuUseCase;
    }
    protected function configure(): void
    {
        $this
            ->setDescription('Builds a public menu from a category tree JSON file.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to a category tree JSON file.')
            ->addOption('adult', null, InputOption::VALUE_REQUIRED, 'Include adult-only categories.', 'false')
            ->addOption('region', null, InputOption::VALUE_REQUIRED, 'Region code: kg or ru.', BuildMenuRegion::UNSPECIFIED)
            ->addOption('staff', null, InputOption::VALUE_REQUIRED, 'Include staff-only categories.', 'false');
    }
    /**
     * @throws InvalidArgumentException
     * @throws InvalidOptionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        if (!is_string($path)) {
            throw new InvalidArgumentException('Argument "path" must be a string.');
        }
        $query = new BuildMenu(
            $path,
            $this->normalizeBooleanOption('adult', $input->getOption('adult')),
            $this->normalizeRegionOption($input->getOption('region')),
            $this->normalizeBooleanOption('staff', $input->getOption('staff'))
        );
        $menu = $this->buildMenuUseCase->execute($query);
        foreach ($this->renderMenu($menu) as $line) {
            $output->writeln($line);
        }
        return Command::SUCCESS;
    }
    /**
     * @param mixed $value
     *
     * @throws InvalidOptionException
     */
    private function normalizeBooleanOption(string $optionName, $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (!is_string($value)) {
            throw new InvalidOptionException(sprintf('Option "--%s" must be a boolean-like value.', $optionName));
        }
        $normalizedValue = strtolower(trim($value));
        $allowedValues = [
            '1' => true,
            '0' => false,
            'true' => true,
            'false' => false,
        ];
        if (!array_key_exists($normalizedValue, $allowedValues)) {
            throw new InvalidOptionException(
                sprintf('Option "--%s" must be one of: true, false, 1, 0.', $optionName)
            );
        }
        return $allowedValues[$normalizedValue];
    }
    /**
     * @param mixed $value
     *
     * @throws InvalidOptionException
     */
    private function normalizeRegionOption($value): BuildMenuRegion
    {
        if (null === $value) {
            return new BuildMenuRegion(BuildMenuRegion::UNSPECIFIED);
        }
        if (!is_string($value)) {
            throw new InvalidOptionException('Option "--region" must be one of: kg, ru.');
        }
        $normalizedValue = strtolower(trim($value));
        if ($normalizedValue === '') {
            return new BuildMenuRegion(BuildMenuRegion::UNSPECIFIED);
        }
        try {
            return new BuildMenuRegion($normalizedValue);
        } catch (UnexpectedValueException $exception) {
            throw new InvalidOptionException('Option "--region" must be one of: kg, ru.', 0, $exception);
        }
    }
    /**
     * @return string[]
     */
    private function renderMenu(Menu $menu): array
    {
        $lines = [];
        foreach ($menu->items() as $item) {
            foreach ($this->renderMenuItem($item, 0) as $line) {
                $lines[] = $line;
            }
        }
        return $lines;
    }
    /**
     * @return string[]
     */
    private function renderMenuItem(MenuItem $item, int $depth): array
    {
        $lines = [sprintf('%s- %s', str_repeat('  ', $depth), $item->name())];
        foreach ($item->children() as $child) {
            foreach ($this->renderMenuItem($child, $depth + 1) as $line) {
                $lines[] = $line;
            }
        }
        return $lines;
    }
}
