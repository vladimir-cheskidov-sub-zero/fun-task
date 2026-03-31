<?php

declare(strict_types=1);

namespace FunTask\Bridge\Symfony\Console\Command;

use FunTask\Application\Dto\SearchIndexDocumentDto;
use FunTask\Application\Dto\SearchIndexExportDto;
use FunTask\Application\Exception\CategoryTreePathCannotBeEmpty;
use FunTask\Application\Exception\SearchIndexExportFailed;
use FunTask\Application\Service\SearchIndexExport;
use FunTask\Application\Service\SearchIndexExportService;
use JsonException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CategoriesSearchExportCommand extends Command
{
    protected static $defaultName = 'categories:search-export';
    private SearchIndexExportService $searchIndexExportUseCase;
    public function __construct(SearchIndexExportService $searchIndexExportUseCase)
    {
        parent::__construct();
        $this->searchIndexExportUseCase = $searchIndexExportUseCase;
    }
    protected function configure(): void
    {
        $this
            ->setDescription('Exports searchable categories to a JSON search index file.')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to a category tree JSON file.');
    }
    /**
     * @throws CategoryTreePathCannotBeEmpty
     * @throws InvalidArgumentException
     * @throws JsonException
     * @throws SearchIndexExportFailed
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $path = $input->getArgument('path');
        if (!is_string($path)) {
            throw new InvalidArgumentException('Argument "path" must be a string.');
        }
        $outputPath = $this->writeExport(
            $this->searchIndexExportUseCase->execute(new SearchIndexExport($path))
        );
        $output->writeln($outputPath);
        return Command::SUCCESS;
    }
    /**
     * @throws JsonException
     * @throws \RuntimeException
     */
    private function writeExport(SearchIndexExportDto $export): string
    {
        $directory = 'export';
        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Export directory "%s" could not be created.', $directory));
        }
        $outputPath = sprintf('%s/%s-index.json', $directory, date('YmdHis'));
        $payload = json_encode(
            $this->toPayload($export),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
        if (file_put_contents($outputPath, $payload . PHP_EOL) === false) {
            throw new \RuntimeException(sprintf('Search index export could not be written to "%s".', $outputPath));
        }
        return $outputPath;
    }
    /**
     * @return array<int, array<string, mixed>>
     */
    private function toPayload(SearchIndexExportDto $export): array
    {
        $documents = [];
        foreach ($export->documents as $document) {
            $documents[] = $this->toDocumentPayload($document);
        }
        return $documents;
    }
    /**
     * @return array<string, mixed>
     */
    private function toDocumentPayload(SearchIndexDocumentDto $document): array
    {
        return [
            'id' => $document->id,
            'name' => $document->name,
            'adult' => $document->adult,
            'regions' => $document->regions,
        ];
    }
}
