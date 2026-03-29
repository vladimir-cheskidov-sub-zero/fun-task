#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use FunTask\Bridge\Console\EntryPointRunner;

exit(EntryPointRunner::createDefault()->run());
