<?php

namespace Stylers\EmailVerification\Frameworks\Laravel;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Orchestra\Testbench\TestCase;
use Stylers\EmailVerification\Frameworks\Laravel\ServiceProvider as EmailVerificationServiceProvider;
use Themsaid\MailPreview\MailPreviewServiceProvider;

abstract class BaseTestCase extends TestCase
{
    use DatabaseTransactions;

    protected $consoleOutput;

    protected function setUp()
    {
        parent::setUp();
        $this->setUpFactory();
        $this->setUpDatabase();
    }

    public function tearDown()
    {
        $this->consoleOutput = '';
        $this->artisan('migrate:reset');

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            MailPreviewServiceProvider::class,
            EmailVerificationServiceProvider::class
        ];
    }

    public function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(Kernel::class, \Orchestra\Testbench\Console\Kernel::class);
    }

    public function getConsoleOutput()
    {
        return $this->consoleOutput ?: $this->consoleOutput = $this->app[Kernel::class]->output();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
        ]);
        $app['config']->set('mailpreview.path', __DIR__ . '/../../../build/email-previews');
        $app['config']->set('email-verification.expire', 60);
        $app['config']->set('email-verification.route', 'email.verification');
    }

    private function setUpFactory()
    {
        $this->withFactories(__DIR__ . '/Fixtures/database/factories');
    }

    private function setUpDatabase()
    {
        $this->artisan('migrate', ['--database' => 'testing',
            '--path'     => '../../../../tests/Frameworks/Laravel/Fixtures/database/migrations',
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/../../../src/Frameworks/Laravel/_publish/database/migrations');
        $this->artisan('migrate', ['--database' => 'testing']);
    }
}