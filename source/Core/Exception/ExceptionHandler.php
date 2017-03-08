<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Core\Exception;

use oxRegistry;
use Exception;
use oxException;

/**
 * Exception handler, deals with all high level exceptions (caught in oxShopControl)
 */
class ExceptionHandler
{
    /**
     * Log file path/name
     *
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will change in the future.
     *
     * @var string
     */
    protected $_sFileName = OX_LOG_FILE;

    /**
     * Shop debug
     *
     * @var integer
     */
    protected $_iDebug = 0;

    /**
     * Class constructor
     *
     * @param integer $iDebug debug level
     */
    public function __construct($iDebug = 0)
    {
        $this->_iDebug = (int) $iDebug;
    }

    /**
     * Set the debug level
     *
     * @param int $iDebug debug level (0== no debug)
     */
    public function setIDebug($iDebug)
    {
        $this->_iDebug = $iDebug;
    }

    /**
     * Set log file name. The file will always be created in the same directory as OX_LOG_FILE
     *
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will change in the future.
     *
     * @param string $fileName file name
     */
    public function setLogFileName($fileName)
    {
        /**
         *  If $fileName !== basename($fileName) throw exception
         */
        $fileName = dirname(OX_LOG_FILE) . DIRECTORY_SEPARATOR . basename($fileName);

        $this->_sFileName = $fileName;
    }

    /**
     * Get log file path/name
     *
     * @deprecated since v5.3 (2016-06-17); Logging mechanism will change in the future.
     *
     * @return string
     */
    public function getLogFileName()
    {
        return basename($this->_sFileName);
    }

    /**
     * Uncaught exception handler, deals with uncaught exceptions (global)
     *
     * @param \Exception $exception exception object
     */
    public function handleUncaughtException(\Exception $exception)
    {
        /**
         * Report the exception
         */
        $logWritten = $this->writeExceptionToLog($exception);

        /**
         * Render an error message.
         */

        if ($this->_iDebug) {
            /** @todo */
            $this->displayDebugMessage($exception, $logWritten);
        } else {
            $this->displayOfflinePage();
        }

        /**
         * Do not exit the application in UNIT tests
         */
        if (defined('OXID_PHP_UNIT')) {
            return;
        }
        $this->exitApplication();
    }

    /**
     * Uncaught exception handler, deals with uncaught exceptions (global)
     *
     * @param Exception $oEx exception object
     *
     * @return null
     */
    public function _handleUncaughtException($oEx)
    {
        // split between php or shop exception
        if (!($oEx instanceof \OxidEsales\Eshop\Core\Exception\StandardException)) {
            $this->_dealWithNoOxException($oEx);

            return; // Return straight away ! (in case of unit testing)
        }

        $oEx->setLogFileName($this->_sFileName); // set common log file ...

        $this->_uncaughtException($oEx); // Return straight away ! (in case of unit testing)
    }

    /**
     * Deal with uncaught oxException exceptions.
     *
     * @param StandardException $oEx Exception to handle
     *
     * @return null
     */
    protected function _uncaughtException($oEx)
    {
        // exception occurred in function processing
        $oEx->setNotCaught();
        // general log entry for all exceptions here
        $oEx->debugOut();

        if (defined('OXID_PHP_UNIT')) {
            return $oEx->getString();
        } elseif (0 != $this->_iDebug) {
            /** Added try catch block as showMessageAndExit may throw and exception, if there is a database connection problem */
            try {
                oxRegistry::getUtils()->showMessageAndExit($oEx->getString());
            } catch (Exception $oException) {
                oxRegistry::getUtils()->redirectOffline(500);
            }
        }

        try {
            oxRegistry::getUtils()->redirectOffline(500);
        } catch (Exception $oException) {
        }

        exit();
    }

    /**
     * No oxException, just write log file.
     *
     * @param Exception $oEx exception object
     *
     * @return null
     */
    protected function _dealWithNoOxException($oEx)
    {
        if (0 != $this->_iDebug) {
            $sLogMsg = date('Y-m-d H:i:s') . $oEx . "\n---------------------------------------------\n";
            //deprecated since v5.3 (2016-06-17); Logging mechanism will change in the future.
            oxRegistry::getUtils()->writeToLog($sLogMsg, $this->getLogFileName());
            //end deprecated
            if (defined('OXID_PHP_UNIT')) {
                return;
            } elseif (0 != $this->_iDebug) {
                oxRegistry::getUtils()->showMessageAndExit($sLogMsg);
            }
        }

        try {
            oxRegistry::getUtils()->redirectOffline(500);
        } catch (Exception $oException) {
        }

        exit();
    }

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $sMethod Methods name
     * @param array  $aArgs   Argument array
     *
     * @throws SystemComponentException Throws an exception if the called method does not exist or is not accessible in current class
     *
     * @return string
     */
    public function __call($sMethod, $aArgs)
    {
        if (defined('OXID_PHP_UNIT')) {
            if (substr($sMethod, 0, 4) == "UNIT") {
                $sMethod = str_replace("UNIT", "_", $sMethod);
            }
            if (method_exists($this, $sMethod)) {
                return call_user_func_array(array(& $this, $sMethod), $aArgs);
            }
        }

        throw new SystemComponentException("Function '$sMethod' does not exist or is not accessible! (" . __CLASS__ . ")" . PHP_EOL);
    }

    /**
     * Report the exception and in case that iDebug is not set, redirect to maintenance page.
     * Special methods are used here as the normal exception handling routines always need a database connection and
     * this would create a loop.
     *
     * @param \OxidEsales\Eshop\Core\Exception\DatabaseException $exception Exception to handle
     */
    public function handleDatabaseException(\OxidEsales\Eshop\Core\Exception\DatabaseException $exception)
    {
        $this->handleUncaughtException($exception);
    }

    /**
     * @param Exception $exception
     *
     * @return int|false
     */
    public function writeExceptionToLog(\Exception $exception)
    {
        /**
         * @deprecated since v5.3 (2016-06-17); Logging mechanism will be changed in 6.0.
         */
        $logFile = $this->_sFileName;
        $logMessage = $this->getFormattedException($exception);

        return file_put_contents($logFile, $logMessage, FILE_APPEND) !== false ? true : false;
    }

    /**
     * Render an error message.
     * If offline.html exists its content is displayed.
     * Like this the error message is overridable within that file.
     * Do not display an error message, if this file is included during a CLI command
     */
    public function displayOfflinePage()
    {
        if ('cli' !== strtolower(php_sapi_name())) {
            $displayMessage = '';
            if (file_exists(OX_OFFLINE_FILE) && is_readable(OX_OFFLINE_FILE)) {
                $displayMessage = file_get_contents(OX_OFFLINE_FILE);
            };

            header("HTTP/1.1 500 Internal Server Error");
            header("Connection: close");
            echo $displayMessage;
        }
    }

    /**
     * Exit the application with error status 1
     */
    protected function exitApplication()
    {
        exit(1);
    }

    /**
     * @param Exception $exception
     *
     * @return string
     */
    protected function getFormattedException(\Exception $exception)
    {
        $time = microtime(true);
        $micro = sprintf("%06d", ($time - floor($time)) * 1000000);
        $date = new \DateTime(date('Y-m-d H:i:s.' . $micro, $time));
        $timestamp = $date->format('D M H:i:s.u Y');

        $class = get_class($exception);

        /** report the error */
        $trace = $exception->getTraceAsString();
        $lines = explode(PHP_EOL, $trace);
        $logMessage = "[$timestamp] [exception] [type {$class}] [code {$exception->getCode()}] [file {$exception->getFile()}] [line {$exception->getLine()}] [message {$exception->getMessage()}]" . PHP_EOL;
        foreach ($lines as $line) {
            $logMessage .= "[$timestamp] [exception] [stacktrace] " . $line . PHP_EOL;
        }

        return $logMessage;
    }

    /**
     * @param Exception $exception
     */
    protected function displayDebugMessage(\Exception $exception)
    {
        if ('cli' !== strtolower(php_sapi_name())) {
            echo 'Uncaught exception. See exception log'. PHP_EOL;
            return;
        }
        if (method_exists($exception, 'getString')) {
            $displayMessage = $exception->getString();
        } else {
            $displayMessage = $this->getFormattedException($exception);
        }
        echo '<pre>' . $displayMessage . '</pre>';
    }
}
