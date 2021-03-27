<?php
namespace Fab\MediaUpload\Command;

use Fab\MediaUpload\FileUpload\UploadManager;
use Symfony\Component\Console\Input\InputOption;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Exception;
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
 * @package Fab\MediaUpload\Command;
 */
class TemporaryFileCommand extends Command {

    /**
     * @var array
     */
    private $allowedTables = [] ;

    /**
     * @var array
     */
    private $extConf = [] ;



    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Remove temporarely files from Media Upload.')
            ->setHelp('Get list of Options: .' . LF . 'use the --help option.')
            ->addArgument(
                'rundry',
                InputArgument::OPTIONAL,
                'if rundry is given, will only List files ',
                '0' );
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int 0 if everything went fine, or an exit code
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        // Bootstrap::initializeBackendAuthentication();
        $structure = $this->getStructureOfFiles();


        if ($input->getArgument('rundry') ) {
            $io->writeln('Argument rundry given. Only list temp files '  );
            $io->writeln(''  );
            if( is_array($structure['files'])) {
                $io->writeln( implode(PHP_EOL, $structure['files']) );
                $io->writeln(''  );
            }
            $io->writeln(sprintf('%s temporary file(s).', $structure['numberOfFiles'])  );
            return 0 ;
        } else {
            GeneralUtility::rmdir(GeneralUtility::getFileAbsFileName(UploadManager::UPLOAD_FOLDER), true);
            GeneralUtility::mkdir_deep(GeneralUtility::getFileAbsFileName(UploadManager::UPLOAD_FOLDER));
            $io->writeln(sprintf(sprintf('I have removed %s file(s).', $structure['numberOfFiles']) ) );
            return 0 ;
        }

    }

    /**
     * @return array
     */
    protected function getStructureOfFiles()
    {
        if( !is_dir(GeneralUtility::getFileAbsFileName(UploadManager::UPLOAD_FOLDER))) {
            mkdir(GeneralUtility::getFileAbsFileName(UploadManager::UPLOAD_FOLDER)) ;
        }
        $Directory = new RecursiveDirectoryIterator(GeneralUtility::getFileAbsFileName(UploadManager::UPLOAD_FOLDER));
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