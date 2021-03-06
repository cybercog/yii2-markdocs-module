<?php
/**
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2014 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace schmunk42\markdocs\controllers;

use yii\helpers\Markdown;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Class DefaultController
 * @author Tobias Munk <tobias@diemeisterei.de>
 */
class DefaultController extends Controller
{
    /**
     * Renders the documentation pages from github.com
     * @return string
     */
    public function actionIndex($file = '10-about.md')
    {
        // TOOD: DRY(!)
        $cacheKey = 'github-markdown/toc';
        $toc      = \Yii::$app->cache->get($cacheKey);
        if (!$toc) {
            $toc = $this->createHtml('README.md');
            \Yii::$app->cache->set($cacheKey, $toc, 300);
        }

        $cacheKey = 'github-markdown/' . $file;
        $html     = \Yii::$app->cache->get($cacheKey);
        if (!$html) {
            $html = $this->createHtml($file);
            \Yii::$app->cache->set($cacheKey, $html, 1);
        }

        // exract headline - TODO: this should be done with the Markdown parser
        preg_match("/\<h[1-3]\>(.*)\<\/h[1-3]\>/", $html, $matches);
        if (isset($matches[1])) {
            $headline = $matches[1];
        } else {
            $headline = $file;
        }

        return $this->render(
            'docs',
            [
                'html'     => $html,
                'toc'      => $toc,
                'headline' => $headline,
                'forkUrl'  => $this->module->forkUrl . $file
            ]
        );
    }

    /**
     * Helper function for the docs action
     * @return string
     */
    private function createHtml($file)
    {
        \Yii::trace("Creating HTML for '{$file}'", __METHOD__);
        $markdown = file_get_contents($this->module->markdownUrl . '/' . $file);
        $html     = Markdown::process($markdown, 'gfm');
        $html     = preg_replace('/<a href="(?!http)(.+\.md)">/U', '<a href="__INTERNAL_URL__$1">', $html);

        $dummyUrl = Url::to(['/docs', 'file' => '__PLACEHOLDER__']);
        $html     = strtr($html, ['__INTERNAL_URL__' => $dummyUrl]);
        $html     = strtr($html, ['__PLACEHOLDER__' => '']);

        return $html;
    }

}