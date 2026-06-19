<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('edubridge:status', function () {
    $this->info('EduBridgeBackend operativo.');
});
