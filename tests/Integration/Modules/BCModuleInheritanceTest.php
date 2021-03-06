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
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

/**
 * Test, that the inheritance of modules and the shop works as expected.
 *
 * See also OxidEsales\EshopCommunity\Tests\Integration\Modules\ModuleInheritanceTest
 *
 * Below, there are listed all possible combinations which are possible. You have to read the tables as follows:
 * E.g. Test Case 1.1 is: A "plain module class" "extends via PHP" a "Plain shop class"
 *
 *
 * 1. Simple extending shop classes in modules
 * +-------------------------------+--------------------+-------------------------+---------------------------------+
 * |        extends via PHP        | plain module class | namespaced module class | virtual namespaced module class |
 * +-------------------------------+--------------------+-------------------------+---------------------------------+
 * | Plain shop class              |                1.1 |                     1.6 | not planned                     |
 * | Namespaced shop class         |                1.2 |                     1.7 | not planned                     |
 * | Virtual namespaced shop class |                1.5 |                    1.10 | not planned                     |
 * +-------------------------------+--------------------+-------------------------+---------------------------------+
 *
 *
 *
 * 2. Simple extending module classes from other modules
 * +--------------------------------------------------------------+--------------------+-------------------------+
 * |                       extends via PHP                        | plain module class | namespaced module class |
 * +--------------------------------------------------------------+--------------------+-------------------------+
 * | plain module class which extends an other class              |                2.1 |                     2.3 |
 * | namespaced module class which extends an other class         |                2.2 |                     2.4 |
 * | plain module class which chain extends a shop class          |                2.5 |                     2.7 |
 * | namespaced module class which does not extend an other class |                2.6 |                     2.8 |
 * +--------------------------------------------------------------+--------------------+-------------------------+
 *
 * Together with "2. Simple extending module classes from other modules" we implemented some other test cases.
 * These test cases should be already covered by the test cases in table 1 and 3.
 * If you remove these unnecessary test cases, there should be only 4 test cases left:
 * +--------------------------+--------------------+-------------------------+
 * |     extends via PHP      | plain module class | namespaced module class |
 * +--------------------------+--------------------+-------------------------+
 * | plain module class       |                    |                         |
 * | namespaced module class  |                    |                         |
 * +--------------------------+--------------------+-------------------------+
 *
 *
 *
 *  3. Chain extending shop classes in modules
 * +-------------------------------+--------------------+-------------------------+
 * |       extends via chain       | plain module class | namespaced module class |
 * +-------------------------------+--------------------+-------------------------+
 * | Plain shop class              | 3.1                | no need                 |
 * | Namespaced shop class         | no need            | 3.5                     |
 * | Virtual namespaced shop class | 3.3                | 3.6                     |
 * +-------------------------------+--------------------+-------------------------+
 *
 *
 *
 * 4. Chain extending module classes from other modules
 * +-------------------------+--------------------+-------------------------+
 * |    extends via chain    | plain module class | namespaced module class |
 * +-------------------------+--------------------+-------------------------+
 * | plain module class      |                4.1 |                     4.3 |
 * | namespaced module class |                4.2 |                     4.4 |
 * +-------------------------+--------------------+-------------------------+
 *
 * @group module
 */
class BCModuleInheritanceTest extends BaseModuleInheritanceTestCase
{
    /**
     * This test covers PHP inheritance between one module class and one shop class.
     *
     * The module class extends the PHP class directly like '<module class> extends <shop class>'
     * In this case the parent class of the module class must be the shop class as instantiated with oxNew
     *
     * @dataProvider dataProviderTestModuleInheritanceTestPhpInheritance
     *
     * @param array  $moduleToActivate The module we want to activate.
     * @param string $moduleClassName  The module class we want to instantiate.
     * @param array  $shopClassNames   The shop classes from which the module class should inherit.
     */
    public function testModuleInheritanceTestPhpInheritance($moduleToActivate, $moduleClassName, $shopClassNames)
    {
        parent::testModuleInheritanceTestPhpInheritance($moduleToActivate, $moduleClassName, $shopClassNames);
    }

    /**
     * This test covers PHP inheritance between module classes.
     *
     * The tested module class extends the other module class directly like '<module anotherclass> extends <module class>'
     * or '<moduleA class> extends <moduleB class>'
     * In this case the parent class of the module class must be the parent module class as instantiated with oxNew
     *
     * @dataProvider dataProviderTestMultiModuleInheritanceTestPhpInheritance
     *
     * @param array  $modulesToActivate The modules we want to activate.
     * @param string $moduleClassName   The module class we want to instantiate.
     * @param array  $shopClassNames    The shop class from which the module class sould inherit.
     */
    public function testMultiModuleInheritanceTestPhpInheritance($modulesToActivate, $moduleClassName, $shopClassNames)
    {
        parent::testModuleInheritanceTestPhpInheritance($modulesToActivate, $moduleClassName, $shopClassNames);
    }

    /**
     * DataProvider for the testModuleInheritanceTestPhpInheritance method.
     *
     * @return array The different test cases we execute.
     */
    public function dataProviderTestModuleInheritanceTestPhpInheritance()
    {
        return [
            'case_1_1' => [
                //Test case 1.1 plain module extends plain shop class
                'moduleToActivate'  => ['bc_module_inheritance_1_1'],
                'moduleClassName'   => 'vendor_1_module_1_myclass',
                'shopClassNames'    => ['oxArticle']
            ],
            'case_1_2' => [
                //Test case 1.2 plain module extends namespaced eShop Community class
                'moduleToActivate' => ['bc_module_inheritance_1_2'],
                'moduleClassName'  => 'vendor_1_module_2_myclass',
                'shopClassNames'    => ['OxidEsales\EshopCommunity\Application\Model\Article']
            ],
            'case_1_5' => [
                //Test case 1.5 plain module extends eShop virtual class
                'moduleToActivate' => ['bc_module_inheritance_1_5'],
                'moduleClassName'  => 'vendor_1_module_5_myclass',
                'shopClassNames'   => [\OxidEsales\Eshop\Application\Model\Article::class]
            ],
            'case_3_1' => [
                //Test case 3.1 plain module chain extends plain OXID eShop class
                'moduleToActivate' => ['module_chain_extension_3_1'],
                'moduleClassName'  => 'vendor_1_module_3_1_myclass',
                'shopClassNames'   => ['oxArticle']
            ],
            'case_3_2' => [
                //Test case 3.1 plain module chain extends namespaced OXID eShop Community class
                'moduleToActivate' => ['module_chain_extension_3_2'],
                'moduleClassName'  => 'vendor_1_module_3_2_myclass',
                'shopClassNames'   => [\OxidEsales\EshopCommunity\Application\Model\Article::class]
            ],
            'case_3_3' => [
                //Test case 3.3 plain module chain extends virtual OXID eShop class
                'moduleToActivate' => ['module_chain_extension_3_3'],
                'moduleClassName'  => 'vendor_1_module_3_3_myclass',
                'shopClassNames'   => [\OxidEsales\Eshop\Application\Model\Article::class]
            ]
        ];
    }

    /**
     * DataProvider for the testMultiModuleInheritanceTestPhpInheritance method.
     *
     * @return array The different test cases we execute.
     */
    public function dataProviderTestMultiModuleInheritanceTestPhpInheritance()
    {
        return [
            'case_2_1_1' => [
                //Test case 2.1 plain module class extends same module's extended plain module class
                'modulesToActivate' => ['bc_module_inheritance_1_1'],
                'moduleClassName'   => 'vendor_1_module_1_anotherclass',
                'shopClassNames'    => ['vendor_1_module_1_myclass', \OxidEsales\Eshop\Application\Model\Article::class]
            ],
            'case_2_1_2' => [
                //Test case 2.1 plain module class extends an other modules extended plain module class
                'modulesToActivate' => ['bc_module_inheritance_1_1', 'bc_module_inheritance_2_1'],
                'moduleClassName'   => 'vendor_2_module_1_myclass',
                'shopClassNames'    => ['vendor_1_module_1_myclass', \OxidEsales\Eshop\Application\Model\Article::class]
            ],
            'case_2_5' => [
                //Test case 2.5 plain module_2 extends plain module_1
                'modulesToActivate' => ['bc_module_inheritance_1_1', 'bc_module_inheritance_2_5'],
                'moduleClassName'   => 'vendor_2_module_5_myclass',
                'shopClassNames'    => ['vendor_1_module_1_onemoreclass']
            ]
        ];
    }
}
