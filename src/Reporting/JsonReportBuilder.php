<?php

namespace Enlightn\Enlightn\Reporting;

use Enlightn\Enlightn\CommitHash;
use Enlightn\Enlightn\Composer;
use Illuminate\Container\Container;
use Throwable;

class JsonReportBuilder implements ReportBuilder
{
    /**
     * @param array $analyzerResults
     * @param array $analyzerStats
     * @param array $additionalData
     * @return array
     */
    public function buildReport(array $analyzerResults, array $analyzerStats, array $additionalData = [])
    {
        return [
            'metadata' => array_merge($this->metadata(), $additionalData),
            'analyzer_results' => $analyzerResults,
            'analyzer_stats' => $analyzerStats,
        ];
    }

    /**
     * Get the project metadata for the JSON report.
     *
     * @return array
     */
    public function metadata()
    {
        return [
            'app_name' => $this->getAppName(),
            'app_env' => $this->getAppEnv(),
            'app_url' => $this->getAppUrl(),
            'project_name' => $this->getProjectName(),
            'github_repo' => $this->getGithubRepo(),
            'commit_id' => $this->getCommitId(),
            'trigger' => $this->getTrigger(),
        ];
    }

    public function getAppName(){
        return config('app.name');
    }

    public function getAppEnv(){
        return config('app.env');
    }

    public function getAppUrl(){
        return config('app.url');
    }

    /**
     *
     * @return string|null
     *
     */
    protected function getProjectName()
    {
        try {
            $composer = Container::getInstance()->make(Composer::class);

            $json = $composer->getJson();
        } catch (Throwable $throwable) {
            // Ignore any exceptions such as file not found.
            $json = [];
        }

        return $json['name'] ?? null;
    }

    public function getGithubRepo(){
        return config('enlightn.github_repo');
    }

    public function getCommitId(){
        return CommitHash::get();
    }

    public function getTrigger(){
        return 'command';
    }

}
