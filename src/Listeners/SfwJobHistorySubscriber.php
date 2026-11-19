<?php

    namespace Softinline\SfwComponent\Listeners;

    use Illuminate\Queue\Events\JobFailed;
    use Illuminate\Queue\Events\JobProcessed;
    use Illuminate\Queue\Events\JobProcessing;
    use Softinline\SfwComponent\Models\SfwJobHistory;

    class SfwJobHistorySubscriber
    {
        /**
         * Handle the job processing event.
         *
         * @param \Illuminate\Queue\Events\JobProcessing $event
         * @return void
         */
        public function handleJobProcessing($event)
        {
            $uuid = $this->getUuid($event);
            if ($uuid === '') {
                return;
            }

            $history = $this->findOrCreate($uuid, $event);
            $history->status = 'processing';
            $history->attempts = $this->getAttempts($event);
            $history->started_at = now();
            $history->finished_at = null;
            $history->duration_ms = null;
            $history->exception = null;
            $history->save();
        }

        /**
         * Handle the job processed event.
         *
         * @param \Illuminate\Queue\Events\JobProcessed $event
         * @return void
         */
        public function handleJobProcessed($event)
        {
            $uuid = $this->getUuid($event);
            if ($uuid === '') {
                return;
            }

            $history = $this->findOrCreate($uuid, $event);
            $history->status = 'succeeded';
            $history->attempts = $this->getAttempts($event);
            $history->finished_at = now();
            if ($history->started_at) {
                $history->duration_ms = $history->started_at->diffInMilliseconds($history->finished_at);
            }
            $history->exception = null;
            $history->save();
        }

        /**
         * Handle the job failed event.
         *
         * @param \Illuminate\Queue\Events\JobFailed $event
         * @return void
         */
        public function handleJobFailed($event)
        {
            $uuid = $this->getUuid($event);
            if ($uuid === '') {
                return;
            }

            $history = $this->findOrCreate($uuid, $event);
            $history->status = 'failed';
            $history->attempts = $this->getAttempts($event);
            $history->finished_at = now();
            if ($history->started_at) {
                $history->duration_ms = $history->started_at->diffInMilliseconds($history->finished_at);
            }
            $history->exception = $event->exception ? (string) $event->exception : null;
            $history->save();
        }

        /**
         * Find or create history record.
         *
         * @param string $uuid
         * @param mixed $event
         * @return \Softinline\SfwComponent\Models\SfwJobHistory
         */
        protected function findOrCreate($uuid, $event)
        {
            $history = SfwJobHistory::where('uuid', $uuid)->orderByDesc('created_at')->first();
            if (!$history) {
                $history = new SfwJobHistory();
                $prefix = config('sfw.id_prefixes.sfw_job_history', 'JH');
                $history->id = uniqid($prefix);
                $history->uuid = $uuid;
                $history->started_at = now();
            }

            $history->connection = $event->connectionName ?? null;
            $history->queue = method_exists($event->job, 'getQueue') ? $event->job->getQueue() : null;
            $history->job_name = method_exists($event->job, 'resolveName') ? $event->job->resolveName() : null;
            $history->payload = $this->getPayload($event);

            return $history;
        }

        /**
         * Get job uuid.
         *
         * @param mixed $event
         * @return string
         */
        protected function getUuid($event)
        {
            try {
                if (isset($event->job) && method_exists($event->job, 'uuid')) {
                    $uuid = $event->job->uuid();
                    if ($uuid) {
                        return (string) $uuid;
                    }
                }
                if (isset($event->job) && method_exists($event->job, 'payload')) {
                    $payload = $event->job->payload();
                    if (is_array($payload) && isset($payload['uuid'])) {
                        return (string) $payload['uuid'];
                    }
                }
            } catch (\Throwable $e) {
                return '';
            }

            return '';
        }

        /**
         * Get attempts count.
         *
         * @param mixed $event
         * @return int
         */
        protected function getAttempts($event)
        {
            try {
                if (isset($event->job) && method_exists($event->job, 'attempts')) {
                    return (int) $event->job->attempts();
                }
            } catch (\Throwable $e) {
                return 0;
            }

            return 0;
        }

        /**
         * Get job payload as array.
         *
         * @param mixed $event
         * @return array|null
         */
        protected function getPayload($event)
        {
            try {
                if (isset($event->job) && method_exists($event->job, 'payload')) {
                    $payload = $event->job->payload();
                    if (is_array($payload)) {
                        return $payload;
                    }
                }
            } catch (\Throwable $e) {
                return null;
            }

            return null;
        }

        /**
         * Register the listeners for the subscriber.
         *
         * @param \Illuminate\Events\Dispatcher $events
         * @return array
         */
        public function subscribe($events)
        {
            return [
                JobProcessing::class => 'handleJobProcessing',
                JobProcessed::class => 'handleJobProcessed',
                JobFailed::class => 'handleJobFailed',
            ];
        }
    }
