<?php
namespace Fab\MediaUpload\Command;

use Fab\MediaUpload\FileUpload\UploadManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class TemporaryFileCommand
 * @author Jörg Velletti <typo3@velletti.de>
 * @package Fab\MediaUpload\Command
 */
class TemporaryFileCommand extends Command
{
    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure(): void
    {
        $this->setDescription('Remove temporary files from Media Upload.')
            ->setHelp('Get list of Options: ' . PHP_EOL . 'use the --help option.')
            ->addArgument(
                'rundry',
                InputArgument::OPTIONAL,
                'if rundry is given, will only List files ',
                '0'
            );
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $structure = $this->getStructureOfFiles();

        if ($input->getArgument('rundry')) {
            $io->writeln('Argument rundry given. Only list temp files');
            $io->writeln('');
            if (is_array($structure['files'])) {
                $io->writeln(implode(PHP_EOL, $structure['files']));
                $io->writeln('');
            }
            $io->writeln(sprintf('%s temporary file(s).', $structure['numberOfFiles']));
            return Command::SUCCESS;
        } else {
            GeneralUtility::rmdir(GeneralUtility::getFileAbsFileName(UploadManager::UPLOAD_FOLDER), true);
            GeneralUtility::mkdir_deep(GeneralUtility::getFileAbsFileName(UploadManager::UPLOAD_FOLDER));
            $io->writeln(sprintf('I have removed %s file(s).', $structure['numberOfFiles']));
            return Command::SUCCESS;
        }
    }

    /**
     * @return array
     */
    protected function getStructureOfFiles(): array
    {
        $uploadFolderPath = GeneralUtility::getFileAbsFileName(UploadManager::UPLOAD_FOLDER);

        if (!is_dir($uploadFolderPath)) {
            mkdir($uploadFolderPath, 0755, true);
        }

        $Directory = new RecursiveDirectoryIterator($uploadFolderPath);
        $iterator = new RecursiveIteratorIterator($Directory);

        $counter = 0;
        $structure = [];

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $counter++;
                $structure['files'][] = $file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename();
            }
        }

        $structure['numberOfFiles'] = $counter;
        return $structure;
    }

}
