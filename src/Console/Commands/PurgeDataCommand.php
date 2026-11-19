<?php

    namespace Softinline\SfwComponent\Console\Commands;

    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\DB;
    use Softinline\SfwComponent\Models\SfwConfig;
    use Softinline\SfwComponent\Models\SfwEmailLog;
    use Softinline\SfwComponent\Models\SfwJobHistory;

    class PurgeDataCommand extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'sfw:purge:data';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Purge old SFW data (job history, email logs, failed jobs) based on sfw_configs retention settings';

        /**
         * Execute the console command.
         */
        public function handle()
        {
            $results = [];

            $historyDays = SfwConfig::getInt('sfw_jobs.retention.history.days', 0);
            $results['sfw_job_history'] = $this->purgeJobHistory($historyDays);

            $emailDays = SfwConfig::getInt('sfw_email_logs.retention.days', 0);
            $results['sfw_email_logs'] = $this->purgeEmailLogs($emailDays);

            $failedDays = SfwConfig::getInt('sfw_jobs.retention.failed_jobs.days', 0);
            $results['failed_jobs'] = $this->purgeFailedJobs($failedDays);

            foreach ($results as $table => $count) {
                $this->info($table.': deleted '.$count);
            }

            return 0;
        }

        /**
         * Purge old job history records.
         *
         * @param int $days
         * @return int
         */
        protected function purgeJobHistory($days)
        {
            if ($days <= 0) {
                return 0;
            }

            $threshold = now()->subDays($days);

            return SfwJobHistory::where('created_at', '<', $threshold)->delete();
        }

        /**
         * Purge old email logs.
         *
         * @param int $days
         * @return int
         */
        protected function purgeEmailLogs($days)
        {
            if ($days <= 0) {
                return 0;
            }

            $threshold = now()->subDays($days);

            return SfwEmailLog::where('created_at', '<', $threshold)->delete();
        }

        /**
         * Purge old failed jobs.
         *
         * @param int $days
         * @return int
         */
        protected function purgeFailedJobs($days)
        {
            if ($days <= 0) {
                return 0;
            }

            $threshold = now()->subDays($days);

            return DB::table('failed_jobs')->where('failed_at', '<', $threshold)->delete();
        }
    }
