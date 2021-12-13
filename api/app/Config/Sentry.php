<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Sentry extends BaseConfig
{
    /**
     * Whether Sentry should currently be enabled. This setting should be turned
     * off in development as it would clutter up the issue list with issues
     * encountered in development.
     *
     * @var bool
     */
    public $enabled = false;

    /**
     * The DSN of the Sentry instance that this server should connect to.
     * This should be only set in environment variables.
     *
     * @var string
     */
    public $dsn = "https://examplePublicKey@o0.ingest.sentry.io/0";

    /**
     * How much of the error events should be captured and sent to Sentry. 1.0
     * means sending all events, and 0.0 means sending nothing.
     *
     * @var float
     */
    public $sample_rate = 1.0;

    /**
     * The current release that will be reported to Sentry. Should be set via
     * environment variables during container build.
     *
     * @var string
     */
    public $release = "unknown-local";

    /**
     * The current environment that this API instance is running in. Should be
     * set via environment variables on the services that run.
     *
     * @var string
     */
    public $environment = "local";
}
