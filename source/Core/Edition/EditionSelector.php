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

namespace OxidEsales\EshopCommunity\Core\Edition;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Autoload\VirtualNameSpaceClassMapProvider;

/**
 * Class is responsible for returning edition.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class EditionSelector
{
    const ENTERPRISE = 'EE';

    const PROFESSIONAL = 'PE';

    const COMMUNITY = 'CE';

    /** @var string Edition abbreviation  */
    private $edition = null;

    /**
     * Virtual class map provider.
     *
     * @var OxidEsales\Eshop\Core\Autoload\VirtualNameSpaceClassMapProvider
     */
    private $virtualClassMapProvider = null;

    /**
     * EditionSelector constructor.
     *
     * @param string|null                           $edition                 to force edition.
     * @param VirtualNameSpaceClassMapProvider|null $virtualClassMapProvider Optional classMapProviderr
     */
    public function __construct($edition = null, $virtualClassMapProvider = null)
    {
        $this->edition = $edition ?: $this->findEdition();
        $this->virtualClassMapProvider = $virtualClassMapProvider;
    }

    /**
     * Method returns edition.
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * @return bool
     */
    public function isEnterprise()
    {
        return $this->getEdition() === static::ENTERPRISE;
    }

    /**
     * @return bool
     */
    public function isProfessional()
    {
        return $this->getEdition() === static::PROFESSIONAL;
    }

    /**
     * @return bool
     */
    public function isCommunity()
    {
        return $this->getEdition() === static::COMMUNITY;
    }

    /**
     * Check for forced edition in config file. If edition is not specified,
     * determine it by ClassMap existence.
     *
     * @return string
     */
    protected function findEdition()
    {
        if (!class_exists('OxidEsales\EshopCommunity\Core\Registry') || !Registry::instanceExists('oxConfigFile')) {
            $configFile = new ConfigFile(OX_BASE_PATH . DIRECTORY_SEPARATOR . "config.inc.php");
        }
        $configFile = isset($configFile) ? $configFile : Registry::get('oxConfigFile');
        $edition = $configFile->getVar('edition') ?: $this->getVirtualClassMapProvider()->getEdition();
        $configFile->setVar('edition', $edition);

        return strtoupper($edition);
    }

    /**
     * Getter for virtualClassMap Provider.
     *
     * @return |OxidEsales\Eshop\Core\Autoload\VirtualNameSpaceClassMapProvider
     */
    protected function getVirtualClassMapProvider()
    {
        if (is_null($this->virtualClassMapProvider)) {
            $this->virtualClassMapProvider = new \OxidEsales\Eshop\Core\Autoload\VirtualNameSpaceClassMapProvider();
        }
        return $this->virtualClassMapProvider;
    }
}
