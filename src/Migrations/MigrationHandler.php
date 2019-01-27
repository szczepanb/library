<?php
namespace App\Migrations;

use Composer\Script\Event;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;
class MigrationHandler extends ScriptHandler
{
    /**
     * Runs the Doctrine migrations.
     *
     * @param Event $event
     */
    public static function runMigrations(Event $event)
    {
        $options = static::getOptions($event);
        $consoleDir = static::getConsoleDir($event, 'run database migrations');
        if (null === $consoleDir) {
            return;
        }
        static::executeCommand($event, $consoleDir, 'doctrine:migrations:migrate -n', $options['process-timeout']);
    }
}