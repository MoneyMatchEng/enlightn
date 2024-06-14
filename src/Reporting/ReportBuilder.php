<?php

namespace Enlightn\Enlightn\Reporting;

interface ReportBuilder
{
    /**
     * @param array $analyzerResults
     * @param array $analyzerStats
     * @param array $additionalData
     * @return string
     */
    public function buildReport(array $analyzerResults, array $analyzerStats, array $additionalData = []);

    /**
     * Get the project metadata for the JSON report.
     *
     * @return array
     */
    public function metadata();

    public function getAppName();

    public function getAppEnv();

    public function getAppUrl();

    public function getGithubRepo();

    public function getCommitId();

    public function getTrigger();
}
