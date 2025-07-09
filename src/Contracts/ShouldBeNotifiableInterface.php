<?php 

namespace Kodventure\LaravelDataExporter\Contracts;

interface ShouldBeNotifiableInterface
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $instance
     * @return void
     */   
    public function notify($instance);
}
