<?php require '../config.php'; ?>
<?php

/*********************************************************************************
 *
 * I N I T I A L I Z A T I O N
 *
 *********************************************************************************/


/*************************************************************
 * Get availability
 *************************************************************/
$avail_php		= loadClass('Php')->isAvailable();
$avail_dns		= loadClass('Dns')->isAvailable();
$avail_httpd	= loadClass('Httpd')->isAvailable();
$avail_mysql	= loadClass('Mysql')->isAvailable();
$avail_pgsql	= loadClass('Pgsql')->isAvailable();
$avail_redis	= loadClass('Redis')->isAvailable();
$avail_memcd	= loadClass('Memcd')->isAvailable();


/*************************************************************
 * Test Connectivity
 *************************************************************/

$connection = array();
$error	= null;

// ---- HTTPD (required) ----
$host	= $GLOBALS['HTTPD_HOST_NAME'];
$succ	= loadClass('Httpd')->canConnect($error, $host);
$connection['Httpd'][$host] = array(
	'error' => $error,
	'host' => $host,
	'succ' => $succ
);
$host	= loadClass('Httpd')->getIpAddress();
$succ	= loadClass('Httpd')->canConnect($error, $host);
$connection['Httpd'][$host] = array(
	'error' => $error,
	'host' => $host,
	'succ' => $succ
);
$host	= 'random.'.loadClass('Httpd')->getTldSuffix();
$succ	= loadClass('Httpd')->canConnect($error, $host);
$connection['Httpd'][$host] = array(
	'error' => $error,
	'host' => $host,
	'succ' => $succ
);

// ---- MYSQL ----
if ($avail_mysql) {
	$host	= $GLOBALS['MYSQL_HOST_NAME'];
	$succ	= loadClass('Mysql')->canConnect($error, $host, array('user' => 'root', 'pass' => loadClass('Helper')->getEnv('MYSQL_ROOT_PASSWORD')));
	$connection['MySQL'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
	$host	= loadClass('Mysql')->getIpAddress();
	$succ	= loadClass('Mysql')->canConnect($error, $host, array('user' => 'root', 'pass' => loadClass('Helper')->getEnv('MYSQL_ROOT_PASSWORD')));
	$connection['MySQL'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
	$host	= '127.0.0.1';
	$succ	= loadClass('Mysql')->canConnect($error, $host, array('user' => 'root', 'pass' => loadClass('Helper')->getEnv('MYSQL_ROOT_PASSWORD')));
	$connection['MySQL'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
}

// ---- PGSQL ----
if ($avail_pgsql) {
	$host	= $GLOBALS['PGSQL_HOST_NAME'];
	$succ	= loadClass('Pgsql')->canConnect($error, $host, array('user' => loadClass('Helper')->getEnv('PGSQL_ROOT_USER'), 'pass' => loadClass('Helper')->getEnv('PGSQL_ROOT_PASSWORD')));
	$connection['PgSQL'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
	$host	= loadClass('Pgsql')->getIpAddress();
	$succ	= loadClass('Pgsql')->canConnect($error, $host, array('user' => loadClass('Helper')->getEnv('PGSQL_ROOT_USER'), 'pass' => loadClass('Helper')->getEnv('PGSQL_ROOT_PASSWORD')));
	$connection['PgSQL'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
	$host	= '127.0.0.1';
	$succ	= loadClass('Pgsql')->canConnect($error, $host, array('user' => loadClass('Helper')->getEnv('PGSQL_ROOT_USER'), 'pass' => loadClass('Helper')->getEnv('PGSQL_ROOT_PASSWORD')));
	$connection['PgSQL'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
}

// ---- REDIS ----
if ($avail_redis) {
	$host	= $GLOBALS['REDIS_HOST_NAME'];
	$succ	= loadClass('Redis')->canConnect($error, $host);
	$connection['Redis'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
	$host	= loadClass('Redis')->getIpAddress();
	$succ	= loadClass('Redis')->canConnect($error, $host);
	$connection['Redis'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
	$host	= '127.0.0.1';
	$succ	= loadClass('Redis')->canConnect($error, $host);
	$connection['Redis'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
}

// ---- MEMCACHED ----
if ($avail_memcd) {
	$host	= $GLOBALS['MEMCD_HOST_NAME'];
	$succ	= loadClass('Memcd')->canConnect($error, $host);
	$connection['Memcached'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
	$host	= loadClass('Memcd')->getIpAddress();
	$succ	= loadClass('Memcd')->canConnect($error, $host);
	$connection['Memcached'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
	$host	= '127.0.0.1';
	$succ	= loadClass('Memcd')->canConnect($error, $host);
	$connection['Memcached'][$host] = array(
		'error' => $error,
		'host' => $host,
		'succ' => $succ
	);
}

// ---- BIND (required)----
$host	= $GLOBALS['DNS_HOST_NAME'];
$succ	= loadClass('Dns')->canConnect($error, $host);
$connection['Bind'][$host] = array(
	'error' => $error,
	'host' => $host,
	'succ' => $succ
);
$host	= loadClass('Dns')->getIpAddress();
$succ	= loadClass('Dns')->canConnect($error, $host);
$connection['Bind'][$host] = array(
	'error' => $error,
	'host' => $host,
	'succ' => $succ
);


/*************************************************************
 * Test Health
 *************************************************************/
$HEALTH_TOTAL = 0;
$HEALTH_FAILS = 0;

foreach ($connection as $docker) {
	foreach ($docker as $conn) {
		if (!$conn['succ']) {
			$HEALTH_FAILS++;
		}
		$HEALTH_TOTAL++;
	}
}
$HEALTH_PERCENT = 100 - ceil(100 * $HEALTH_FAILS / $HEALTH_TOTAL);


/*********************************************************************************
 *
 * H T M L
 *
 *********************************************************************************/
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<?php echo loadClass('Html')->getHead(true); ?>
	</head>

	<body style="background: #1f1f1f;">
		<?php echo loadClass('Html')->getNavbar(); ?>


		<div class="container">


			<!-- ############################################################ -->
			<!-- Version/Health -->
			<!-- ############################################################ -->
			<div class="row">

				<div class="col-md-4 col-sm-4 col-xs-12 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-hashtag"></i> Version</div>
						<div class="dash-box-body">
							<strong>Devilbox</strong> <?php echo $GLOBALS['DEVILBOX_VERSION']; ?> <small>(<?php echo $GLOBALS['DEVILBOX_DATE']; ?>)</small>
						</div>
					</div>
				</div>

				<div class="col-md-4 col-sm-4 col-xs-12 col-margin">
					<img src="/assets/img/devilbox_80.png" style="width:100%;" />
				</div>

				<div class="col-md-4 col-sm-4 col-xs-12 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-bug" aria-hidden="true"></i> Health</div>
						<div class="dash-box-body">
							<div class="meter">
							  <span style="color:black; width: <?php echo $HEALTH_PERCENT; ?>%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $HEALTH_PERCENT; ?>%</span>
							</div>
						</div>
					</div>
				</div>

			</div><!-- /row -->



			<!-- ############################################################ -->
			<!-- DASH -->
			<!-- ############################################################ -->
			<div class="row">

				<div class="col-md-4 col-sm-4 col-xs-12 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-cog" aria-hidden="true"></i> Base Stack</div>
						<div class="dash-box-body">
							<div class="row">
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-4" style="margin-bottom:15px;">
									<?php echo loadClass('Html')->getCirle('dns'); ?>
								</div>
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-4" style="margin-bottom:15px;">
									<?php echo loadClass('Html')->getCirle('php'); ?>
								</div>
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-4" style="margin-bottom:15px;">
									<?php echo loadClass('Html')->getCirle('httpd'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4 col-sm-4 col-xs-12 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-database" aria-hidden="true"></i> SQL Stack</div>
						<div class="dash-box-body">
							<div class="row">
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-4" style="margin-bottom:15px;">
									<?php echo loadClass('Html')->getCirle('mysql'); ?>
								</div>
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-4" style="margin-bottom:15px;">
									<?php echo loadClass('Html')->getCirle('pgsql'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4 col-sm-4 col-xs-12 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-file-o" aria-hidden="true"></i> NoSQL Stack</div>
						<div class="dash-box-body">
							<div class="row">
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-4" style="margin-bottom:15px;">
									<?php echo loadClass('Html')->getCirle('redis'); ?>
								</div>
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-4" style="margin-bottom:15px;">
									<?php echo loadClass('Html')->getCirle('memcd'); ?>
								</div>
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-xs-4" style="margin-bottom:15px;">
									<?php echo loadClass('Html')->getCirle('mongodb'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div><!-- /row -->


			<!-- ############################################################ -->
			<!-- Settings / Status -->
			<!-- ############################################################ -->

			<div class="row">
				<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-info-circle" aria-hidden="true"></i> PHP Container Setup</div>
						<div class="dash-box-body">
							<table class="table table-striped table-hover table-bordered table-sm font-small">
								<p><small>You can also enter the php container and work from inside. The following is available inside the container:</small></p>
								<thead class="thead-inverse">
									<tr>
										<th colspan="2">Settings</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th>uid</th>
										<td><?php echo loadClass('Php')->getUid(); ?></td>
									</tr>
									<tr>
										<th>gid</th>
										<td><?php echo loadClass('Php')->getGid(); ?></td>
									</tr>
									<tr>
										<th>vHost TLD</th>
										<td>*.<?php echo loadClass('Httpd')->getTldSuffix(); ?></td>
									</tr>
									<tr>
										<th>DNS</th>
										<td><?php if ($avail_dns): ?>Enabled<?php else: ?><span class="text-danger">Offline</span><?php endif;?></td>
									</tr>
									<tr>
										<th>Postfix</th>
										<td><?php echo loadClass('Helper')->getEnv('ENABLE_MAIL') ? 'Enabled'  : '<span class="bg-danger">No</span> Disabled';?></td>
									</tr>
									<tr>
										<th>Xdebug</th>
										<td>
											<?php $Xdebug = (loadClass('Helper')->getEnv('PHP_XDEBUG_ENABLE') == 0) ? '' : loadClass('Helper')->getEnv('PHP_XDEBUG_ENABLE'); ?>
											<?php if ($Xdebug == loadClass('Php')->getConfig('xdebug.remote_enable')): ?>
												<?php echo loadClass('Php')->getConfig('xdebug.remote_enable') == 1 ? 'Yes' : 'No'; ?>
											<?php else: ?>
												<?php echo '<span class="text-danger">not installed</span>.env file setting differs from custom php .ini file</span><br/>'; ?>
												<?php echo 'Effective setting: '.loadClass('Php')->getConfig('xdebug.remote_enable'); ?>
											<?php endif; ?>
										</td>
									</tr>
									<tr>
										<th>Xdebug Remote</th>
										<td>
											<?php if (loadClass('Helper')->getEnv('PHP_XDEBUG_REMOTE_HOST') == loadClass('Php')->getConfig('xdebug.remote_host')): ?>
												<?php echo loadClass('Php')->getConfig('xdebug.remote_host'); ?>
											<?php else: ?>
												<?php echo '<span class="text-danger">not installed</span>.env file setting differs from custom php .ini file</span><br/>'; ?>
												<?php echo 'Effective setting: '.loadClass('Php')->getConfig('xdebug.remote_host'); ?>
											<?php endif; ?>
										</td>
									</tr>
									<tr>
										<th>Xdebug Port</th>
										<td>
											<?php if (loadClass('Helper')->getEnv('PHP_XDEBUG_REMOTE_PORT') == loadClass('Php')->getConfig('xdebug.remote_port')): ?>
												<?php echo loadClass('Php')->getConfig('xdebug.remote_port'); ?>
											<?php else: ?>
												<?php echo '<span class="text-danger">not installed</span>.env file setting differs from custom php .ini file</span><br/>'; ?>
												<?php echo 'Effective setting: '.loadClass('Php')->getConfig('xdebug.remote_port'); ?>
											<?php endif; ?>
										</td>
									</tr>
								</tbody>
							</table>

							<table class="table table-striped table-hover table-bordered table-sm font-small">
								<thead class="thead-inverse">
									<tr>
										<th colspan="2">Tools</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<th>composer</th>
										<td><?php if (($version = loadClass('Php')->getComposerVersion()) === false) {echo '<span class="text-danger">not installed</span>';}else{echo $version;}; ?></td>
									</tr>
									<tr>
										<th>drush</th>
										<td><?php if (($version = loadClass('Php')->getDrushVersion()) === false) {echo '<span class="text-danger">not installed</span>';}else{echo $version;}; ?></td>
									</tr>
									<tr>
										<th>drush-console</th>
										<td><?php if (($version = loadClass('Php')->getDrushConsoleVersion()) === false) {echo '<span class="text-danger">not installed</span>';}else{echo $version;}; ?></td>
									</tr>
									<tr>
										<th>git</th>
										<td><?php if (($version = loadClass('Php')->getGitVersion()) === false) {echo '<span class="text-danger">not installed</span>';}else{echo $version;}; ?></td>
									</tr>
									<tr>
										<th>node</th>
										<td><?php if (($version = loadClass('Php')->getNodeVersion()) === false) {echo '<span class="text-danger">not installed</span>';}else{echo $version;}; ?></td>
									</tr>
									<tr>
										<th>npm</th>
										<td><?php if (($version = loadClass('Php')->getNpmVersion()) === false) {echo '<span class="text-danger">not installed</span>';}else{echo $version;}; ?></td>
									</tr>
								</tbody>
							</table>

						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 offset-lg-4 offset-md-0 offset-sm-0 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-info-circle" aria-hidden="true"></i> PHP Container Status</div>
						<div class="dash-box-body">
							<p><small>The PHP Docker can connect to the following services via the specified hostnames and IP addresses.</small></p>
							<table class="table table-striped table-hover table-bordered table-sm font-small">
								<thead class="thead-inverse">
									<tr>
										<th>Service</th>
										<th>Hostname / IP</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($connection as $name => $docker): ?>
										<tr>
											<th rowspan="<?php echo count($docker);?>" class="align-middle"><?php echo $name; ?> connect</th>
											<?php $i=1; foreach ($docker as $conn): ?>

											<?php if ($conn['succ']): ?>
												<?php $text = '<span class="text-success dvlbox-ok"><i class="fa fa-check-square"></i></span> '.$conn['host']; ?>
											<?php else: ?>
												<?php $text = '<span class="text-danger dvlbox-err"><i class="fa fa-exclamation-triangle"></i></span> '.$conn['host'].'<br/>'.$conn['error']; ?>
											<?php endif; ?>

												<?php if ($i == 1): $i++;?>
													<td>
														<?php echo $text; ?>
													</td>
													</tr>
												<?php else: $i++;?>
													<tr>
														<td>
															<?php echo $text; ?>
														</td>
													</tr>
												<?php endif; ?>
											<?php endforeach; ?>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>


			</div><!-- /row -->


			<!-- ############################################################ -->
			<!-- TABLES -->
			<!-- ############################################################ -->
			<div class="row">

				<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-share-alt" aria-hidden="true"></i> Networking</div>
						<div class="dash-box-body">
							<div class="row">
								<div class="container">
									<table class="table table-striped table-hover table-bordered table-sm font-small">
										<thead class="thead-inverse">
											<tr>
												<th>Docker</th>
												<th>Hostname</th>
												<th>IP</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th>php</th>
												<td><?php echo $GLOBALS['PHP_HOST_NAME']; ?></td>
												<td><?php echo loadClass('Php')->getIpAddress(); ?></td>
											</tr>
											<tr>
												<th>httpd</th>
												<td><?php echo $GLOBALS['HTTPD_HOST_NAME']; ?></td>
												<td><?php echo loadClass('Httpd')->getIpAddress(); ?></td>
											</tr>
											<?php if ($avail_mysql): ?>
												<tr>
													<th>mysql</th>
													<td><?php echo $GLOBALS['MYSQL_HOST_NAME']; ?></td>
													<td><?php echo loadClass('Mysql')->getIpAddress(); ?></td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_pgsql): ?>
												<tr>
													<th>pgsql</th>
													<td><?php echo $GLOBALS['PGSQL_HOST_NAME']; ?></td>
													<td><?php echo loadClass('Pgsql')->getIpAddress(); ?></td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_redis): ?>
												<tr>
													<th>redis</th>
													<td><?php echo $GLOBALS['REDIS_HOST_NAME']; ?></td>
													<td><?php echo loadClass('Redis')->getIpAddress(); ?></td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_memcd): ?>
												<tr>
													<th>memcached</th>
													<td><?php echo $GLOBALS['MEMCD_HOST_NAME']; ?></td>
													<td><?php echo loadClass('Memcd')->getIpAddress(); ?></td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_dns): ?>
												<tr>
													<th>bind</th>
													<td><?php echo $GLOBALS['DNS_HOST_NAME']; ?></td>
													<td><?php echo loadClass('Dns')->getIpAddress(); ?></td>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 offset-lg-4 offset-md-0 offset-sm-0 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-wrench" aria-hidden="true"></i> Ports</div>
						<div class="dash-box-body">
							<div class="row">
								<div class="container">
									<table class="table table-striped table-hover table-bordered table-sm font-small">
										<thead class="thead-inverse">
											<tr>
												<th>Docker</th>
												<th>Host port</th>
												<th>Docker port</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th>php</th>
												<td>-</td>
												<td>9000</td>
											</tr>
											<tr>
												<th>httpd</th>
												<td><?php echo loadClass('Helper')->getEnv('LOCAL_LISTEN_ADDR').loadClass('Helper')->getEnv('HOST_PORT_HTTPD');?></td>
												<td>80</td>
											</tr>
											<?php if ($avail_mysql): ?>
												<tr>
													<th>mysql</th>
													<td><?php echo loadClass('Helper')->getEnv('LOCAL_LISTEN_ADDR').loadClass('Helper')->getEnv('HOST_PORT_MYSQL');?></td>
													<td>3306</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_pgsql): ?>
												<tr>
													<th>pgsql</th>
													<td><?php echo loadClass('Helper')->getEnv('LOCAL_LISTEN_ADDR').loadClass('Helper')->getEnv('HOST_PORT_PGSQL');?></td>
													<td>5432</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_redis): ?>
												<tr>
													<th>redis</th>
													<td><?php echo loadClass('Helper')->getEnv('LOCAL_LISTEN_ADDR').loadClass('Helper')->getEnv('HOST_PORT_REDIS');?></td>
													<td>6379</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_memcd): ?>
												<tr>
													<th>memcached</th>
													<td><?php echo loadClass('Helper')->getEnv('LOCAL_LISTEN_ADDR').loadClass('Helper')->getEnv('HOST_PORT_MEMCACHED');?></td>
													<td>11211</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_dns): ?>
												<tr>
													<th>bind</th>
													<td>
														<?php echo loadClass('Helper')->getEnv('LOCAL_LISTEN_ADDR').loadClass('Helper')->getEnv('HOST_PORT_BIND');?>/tcp<br/>
														<?php echo loadClass('Helper')->getEnv('LOCAL_LISTEN_ADDR').loadClass('Helper')->getEnv('HOST_PORT_BIND');?>/udp
														</td>
													<td>53/tcp<br/>53/udp</td>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-hdd-o" aria-hidden="true"></i> Data mounts</div>
						<div class="dash-box-body">
							<div class="row">
								<div class="container">
									<table class="table table-striped table-hover table-bordered table-sm font-small" style="word-break: break-word;">
										<thead class="thead-inverse">
											<tr>
												<th>Docker</th>
												<th>Host path</th>
												<th>Docker path</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th>php</th>
													<td><?php echo loadClass('Helper')->getEnv('HOST_PATH_HTTPD_DATADIR'); ?></td>
												<td>/shared/httpd</td>
											</tr>
											<tr>
												<th>httpd</th>
													<td><?php echo loadClass('Helper')->getEnv('HOST_PATH_HTTPD_DATADIR'); ?></td>
												<td>/shared/httpd</td>
											</tr>
											<?php if ($avail_mysql): ?>
												<tr>
													<th>mysql</th>
													<td><?php echo loadClass('Helper')->getEnv('HOST_PATH_MYSQL_DATADIR').'/'.loadClass('Helper')->getEnv('MYSQL_SERVER'); ?></td>
													<td>/var/lib/mysql</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_pgsql): ?>
												<tr>
													<th>pgsql</th>
													<td><?php echo loadClass('Helper')->getEnv('HOST_PATH_PGSQL_DATADIR').'/'.loadClass('Helper')->getEnv('PGSQL_SERVER'); ?></td>
													<td>/var/lib/postgresql/data/pgdata</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_redis): ?>
												<tr>
													<th>redis</th>
													<td>-</td>
													<td>-</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_memcd): ?>
												<tr>
													<th>memcached</th>
													<td>-</td>
													<td>-</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_dns): ?>
												<tr>
													<th>bind</th>
													<td>-</td>
													<td>-</td>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-cogs" aria-hidden="true"></i> Config mounts</div>
						<div class="dash-box-body">
							<div class="row">
								<div class="container">
									<table class="table table-striped table-hover table-bordered table-sm font-small">
										<thead class="thead-inverse">
											<tr>
												<th>Docker</th>
												<th>Host path</th>
												<th>Docker path</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th>php</th>
												<td>./cfg/<?php echo loadClass('Helper')->getEnv('PHP_SERVER'); ?></td>
												<td>/etc/php-custom.d</td>
											</tr>
											<tr>
												<th>httpd</th>
												<td>-</td>
												<td>-</td>
											</tr>
											<?php if ($avail_mysql): ?>
												<tr>
													<th>mysql</th>
													<td>./cfg/<?php echo loadClass('Helper')->getEnv('MYSQL_SERVER'); ?></td>
													<td>/etc/mysql/conf.d</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_pgsql): ?>
												<tr>
													<th>pgsql</th>
													<td>-</td>
													<td>-</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_redis): ?>
												<tr>
													<th>redis</th>
													<td>-</td>
													<td>-</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_memcd): ?>
												<tr>
													<th>memcached</th>
													<td>-</td>
													<td>-</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_dns): ?>
												<tr>
													<th>bind</th>
													<td>-</td>
													<td>-</td>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 col-margin">
					<div class="dash-box">
						<div class="dash-box-head"><i class="fa fa-bar-chart" aria-hidden="true"></i> Log mounts</div>
						<div class="dash-box-body">
							<div class="row">
								<div class="container">
									<table class="table table-striped table-hover table-bordered table-sm font-small" style="word-break: break-word;">
										<thead class="thead-inverse">
											<tr>
												<th>Docker</th>
												<th>Host path</th>
												<th>Docker path</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<th>php</th>
												<td>./log/<?php echo loadClass('Helper')->getEnv('PHP_SERVER'); ?></td>
												<td>/var/log/php</td>
											</tr>
											<tr>
												<th>httpd</th>
												<td>./log/<?php echo loadClass('Helper')->getEnv('HTTPD_SERVER'); ?></td>
												<td>/var/log/<?php echo loadClass('Helper')->getEnv('HTTPD_SERVER'); ?></td>
											</tr>
											<?php if ($avail_mysql): ?>
												<tr>
													<th>mysql</th>
													<td>./log/<?php echo loadClass('Helper')->getEnv('MYSQL_SERVER'); ?></td>
													<td>/var/log/mysql</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_pgsql): ?>
												<tr>
													<th>pgsql</th>
													<td>./log/pgsql-<?php echo loadClass('Helper')->getEnv('PGSQL_SERVER'); ?></td>
													<td>/var/log/postgresql</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_redis): ?>
												<tr>
													<th>redis</th>
													<td>./log/redis-<?php echo loadClass('Helper')->getEnv('REDIS_SERVER'); ?></td>
													<td>/var/log/redis</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_memcd): ?>
												<tr>
													<th>memcached</th>
													<td>./log/memcached-<?php echo loadClass('Helper')->getEnv('MEMCACHED_SERVER'); ?></td>
													<td>/var/log/memcached</td>
												</tr>
											<?php endif; ?>
											<?php if ($avail_dns): ?>
												<tr>
													<th>bind</th>
													<td>-</td>
													<td>-</td>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>


			</div><!-- /row -->


		</div><!-- /.container -->

		<?php echo loadClass('Html')->getFooter(); ?>
		<script>
		// self executing function here
		(function() {
			// your page initialization code here
			// the DOM will be available here
		})();
		</script>
	</body>
</html>
