<?php

declare(strict_types=1);

namespace Kmchan\Sculpin\UpdateBundle\Source;

use Dflydev\Canal\Analyzer\Analyzer;
use dflydev\util\antPathMatcher\AntPathMatcher;
use Sculpin\Core\Source\DataSourceInterface;
use Sculpin\Core\Source\FilesystemDataSource;
use Sculpin\Core\Source\SourceSet;
use Sculpin\Core\Util\DirectorySeparatorNormalizer;

/**
 * This replacement to filesystem proxy is responsible to replace a filesystem
 * data source and alter its refresh logic.
 *
 * Sculpin expects the initial refresh of a data source to merge all sources
 * to the given source set, and subsequent refreshes to merge only new/updated
 * sources.
 *
 * The original class scans the file system for source file, with a constraint
 * that file modification time should be later than the `sinceTime` timestamp.
 * The timestamp is initialized to UNIX epoch and updated to current time after
 * each refresh. All merged sources are flagged as changed. This logic fulfills
 * the expectation, but has the side effect that all sources are considered
 * changed during the first refresh.
 *
 * To support incremental build, the data source should merge all sources, but
 * only flag those files that have a modification time later than the last
 * build time as changed.
 *
 * Changing the `sinceTime` timestamp of the original class alone is not enough
 * to support incremental build. While the patched class correctly merges
 * changed files in subsequent refreshes, the initial refresh will skip any
 * sources that have an older modification time than the timestamp. However, if
 * we augment the idea with another instance that merges all sources without
 * flagging them as changed during the initial refresh, then the whole scheme
 * will match the expectation as well as enabling incremental build.
 */
class ReplacementFilesystemDataSource implements DataSourceInterface
{
    /**
     * Source directory used to construct the filesystem data source.
     * @var string
     */
    private $sourceDir;

    /**
     * Excluded paths used to construct the filesystem data source.
     * @var string[]
     */
    private $excludedPaths;

    /**
     * Ignored paths used to construct the filesystem data source.
     * @var string[]
     */
    private $ignorePaths;

    /**
     * Raw paths used to construct the filesystem data source.
     * @var string[]
     */
    private $rawPaths;

    /**
     * Path pattern matcher used to construct the filesystem data source.
     * @var AntPathMatcher|null
     */
    private $matcher;

    /**
     * File type analyzer used to construct the filesystem data source.
     * @var Analyzer|null
     */
    private $analyzer;

    /**
     * Path normalizer used to construct the filesystem data source.
     * @var DirectorySeparatorNormalizer|null
     */
    private $normalizer;

    /**
     * Whether the refresh is called or not.
     * @var bool
     */
    private $first;

    /**
     * Whether special procedure is required during the first refresh call.
     * @var bool
     */
    private $bootstrap;

    /**
     * @var FilesystemDataSource
     */
    private $proxied;

    /**
     * Construct a new instance to this class.
     * @param string $sourceDir Path to the source directory.
     * @param string[] $excludedPaths Patterns of files that should be excluded.
     * @param string[] $ignorePaths Patterns of files that should be ignored.
     * @param string[] $rawPaths Patterns of files that should be copied directly.
     * @param AntPathMatcher $matcher Path pattern matcher.
     * @param Analyzer $analyzer File type analyzer.
     * @param DirectorySeparatorNormalizer $normalizer Path normalizer.
     */
    public function __construct(
        string $sourceDir,
        array $excludedPaths,
        array $ignorePaths,
        array $rawPaths,
        AntPathMatcher $matcher = null,
        Analyzer $analyzer = null,
        DirectorySeparatorNormalizer $normalizer = null
    ) {
        $this->sourceDir = $sourceDir;
        $this->excludedPaths = $excludedPaths;
        $this->ignorePaths = $ignorePaths;
        $this->rawPaths = $rawPaths;
        $this->matcher = $matcher;
        $this->analyzer = $analyzer;
        $this->normalizer = $normalizer;
        $this->first = true;
        $this->bootstrap = false;
        $this->proxied = $this->createDataSource();
    }

    /**
     * Set the initial value since time.
     * @param string $sinceTime
     */
    public function setInitialSinceTime($sinceTime): void
    {
        if ($this->first) {
            $field = new \ReflectionProperty('Sculpin\Core\Source\FilesystemDataSource', 'sinceTime');
            $field->setAccessible(true);
            $field->setValue($this->proxied, $sinceTime);
            $field->setAccessible(false);
            $this->bootstrap = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dataSourceId(): string
    {
        return $this->proxied->dataSourceId();
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(SourceSet $sourceSet): void
    {
        if ($this->first) {
            if ($this->bootstrap) {
                $temp1 = $this->createDataSource();
                $temp2 = new SourceSet();
                $temp1->refresh($temp2);

                foreach ($temp2->allSources() as $temp3) {
                    $temp3->setHasNotChanged();
                    $sourceSet->mergeSource($temp3);
                }
            }

            $this->bootstrap = false;
            $this->first = false;
        }

        $this->proxied->refresh($sourceSet);
    }

    private function createDataSource(): FilesystemDataSource
    {
        return new FilesystemDataSource(
            $this->sourceDir,
            $this->excludedPaths,
            $this->ignorePaths,
            $this->rawPaths,
            $this->matcher,
            $this->analyzer,
            $this->normalizer
        );
    }
}
