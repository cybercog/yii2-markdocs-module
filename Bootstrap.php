<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2014 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace schmunk42\markdocs;

use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package schmunk42\packaii
 * @author Tobias Munk <tobias@diemeisterei.de>
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * Register module as `packaii`
     *
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        if (!$app->hasModule('markdocs')) {
            $app->setModule(
                'docs',
                [
                    'class' => 'schmunk42\markdocs\Module'
                ]
            );
        }
    }
}