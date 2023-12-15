<?php
	return [
		'mpdb_core:index' => [
			'class' => Slub\MpdbCore\Command\IndexCommand::class
		],
		'mpdb_core:completegndworks' => [
			'class' => Slub\MpdbCore\Command\CompleteGndWorksCommand::class
		],
		'mpdb_core:healthcheck' => [
			'class' => Slub\MpdbCore\Command\HealthCheckCommand::class
		]
	];
