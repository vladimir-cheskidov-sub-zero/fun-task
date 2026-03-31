<?php

declare(strict_types=1);

namespace FunTask\Bridge\Symfony\Console\Command;

use FunTask\Application\Exception\AuditVisibilityFailed;
use FunTask\Application\Exception\CategoryTreePathCannotBeEmpty;
use FunTask\Application\Dto\VisibilityAuditDto;
use FunTask\Application\Dto\VisibilityAuditItemDto;
use FunTask\Application\Service\AuditVisibility;
use FunTask\Application\Service\AuditVisibilityService;
use FunTask\Bridge\Symfony\Console\ConsoleOutputEscaper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CategoriesAuditCommand extends Command
{
    protected static $defaultName = 'categories:audit';

    private AuditVisibilityService $auditVisibilityUseCase;

    public function __construct(AuditVisibilityService $auditVisibilityUseCase)
    {
        parent::__construct();
        $this->auditVisibilityUseCase = $auditVisibilityUseCase;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Collects problematic categories from a category tree JSON file.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to a category tree JSON file.');
    }

    /**
     * @throws AuditVisibilityFailed
     * @throws CategoryTreePathCannotBeEmpty
     * @throws InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        if (!is_string($path)) {
            throw new InvalidArgumentException('Argument "path" must be a string.');
        }

        $audit = $this->auditVisibilityUseCase->execute(new AuditVisibility($path));
        foreach ($this->renderAudit($audit) as $line) {
            $output->writeln($line);
        }

        return Command::SUCCESS;
    }

    /**
     * @return string[]
     */
    private function renderAudit(VisibilityAuditDto $audit): array
    {
        $lines = [];

        foreach ($audit->items as $item) {
            $lines[] = $this->renderItem($item);
        }

        return $lines;
    }

    private function renderItem(VisibilityAuditItemDto $item): string
    {
        return sprintf(
            '%s [%s]: %s',
            implode(' > ', ConsoleOutputEscaper::escapeInlineCollection($item->path)),
            ConsoleOutputEscaper::escapeInline($item->id),
            implode(', ', ConsoleOutputEscaper::escapeInlineCollection($item->reasons))
        );
    }
}
