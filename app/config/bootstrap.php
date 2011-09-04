<?php
/* SVN FILE: $Id$ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $Date$
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */

/**
 * Debug and show SQL
 */
function ds($var = 'x') {
	d($var, false, true);
}

function d($var = 'x', $skipDebugMode = false, $showSql = false) {
	if (Configure::read() > 0 || $skipDebugMode === true) {
			$calledFrom = debug_backtrace();
			echo '<strong>' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
			echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
			echo "\n<pre class=\"cake-debug\">\n";
			$var = print_r($var, true);
			if ($showSql) {
				dsql();
			}
			echo $var . "\n</pre>\n";
			die;
	}
}


function dsql() {
    $sources = ConnectionManager::sourceList();
    if (!isset($logs)):
        $logs = array();
        foreach ($sources as $source):
            $db =& ConnectionManager::getDataSource($source);
            if (!$db->isInterfaceSupported('getLog')):
                continue;
            endif;
            $logs[$source] = $db->getLog();
        endforeach;
    endif;


    foreach ($logs as $source => $logInfo) {
        $text = $logInfo['count'] > 1 ? 'queries' : 'query';
        printf(
            '<table class="cake-sql-log" id="cakeSqlLog_%s" summary="Cake SQL Log" cellspacing="0" border = "0">',
            preg_replace('/[^A-Za-z0-9_]/', '_', uniqid(time(), true))
        );
        printf('<caption>(%s) %s %s took %s ms</caption>', $source, $logInfo['count'], $text, $logInfo['time']);
        echo '
            <thead>
                <tr><th>Nr</th><th>Query</th><th>Error</th><th>Affected</th><th>Num. rows</th><th>Took (ms)</th></tr>
            </thead>
            <tbody>
            ';
        foreach ($logInfo['log'] as $k => $i) {
            echo "<tr><td>" . ($k + 1) . "</td><td>" . $i['query'] . "</td><td>{$i['error']}</td><td style = \"text-align: right\">{$i['affected']}</td><td style = \"text-align: right\">{$i['numRows']}</td><td style = \"text-align: right\">{$i['took']}</td></tr>\n";
        }
        echo '</tbody></table>';
        }
}


?>