<?php

namespace App\Libraries;

use CodeIgniter\CodeIgniter as BaseCodeIgniter;

class CodeIgniter extends BaseCodeIgniter
{
    public function initialize()
    {
        parent::initialize();

        // HACK: We extend the CodeIgniter instance to bootstrap Sentry at
        //       startup after the main app object has been initialized. This
        //       needs to happen _after_ CodeIgniter::initialize has been
        //       called, because CodeIgniter::initialize calls
        //       Exceptions::initialize which overwrites the current exception
        //       and error handlers with its own.
        static::setup_sentry();
    }

    // FIXME: Find a better place to put this!
    /**
     * Setup Sentry integration.
     */
    private static function setup_sentry()
    {
        $config = config("Sentry");

        \Sentry\init([
            "dsn" => $config->dsn,
            "release" => $config->release,
            "environment" => $config->environment,
            "sample_rate" => (float) $config->sample_rate,
            "send_default_pii" => true,
            "in_app_include" => ["App"],
            "max_request_body_size" => "medium",
        ]);
    }
}
