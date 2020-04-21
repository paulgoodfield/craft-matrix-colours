<?php
/**
 * Craft Matrix Colours plugin for Craft CMS 3.x
 *
 * Define colours for your matrix field blocks to help visually separate them
 *
 * @link      https://paulgoodfield.com
 * @copyright Copyright (c) 2020 Paul Goodfield
 */

namespace paulgoodfield\matrixcolours;

use Craft;
use craft\base\Plugin;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Bluegg
 * @package   MatrixColours
 * @since     1.0.0
 *
 */
class MatrixColours extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * MatrixColours::$plugin
     *
     * @var MatrixColours
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * MatrixColours::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // If a control panel request...
        if (Craft::$app->getRequest()->getIsCpRequest())
        {
            // Get colour settings from config file
            $settings = Craft::$app->config->getConfigFromFile('matrix-colours');
            $strCss   = '';
            
            // Loop through settings array and generate css for matrix blocks
            foreach ($settings as $fieldHandle => $blocks)
            {
                $strCss .= "#fields-{$fieldHandle} .matrixblock {
                    border-top-left-radius: 0;
                    border-bottom-left-radius: 0;
                }
                #fields-{$fieldHandle} .matrixblock:after {
                    content: '';
                    position: absolute;
                    top: -1px;
                    left: -3px;
                    width: 3px;
                    height: calc(100% + 2px);
                }
                @media only screen and (min-width: 1000px) {
                    #fields-{$fieldHandle} .matrixblock:after {
                        width: 5px;
                        left: -5px;
                    }
                }
                #fields-{$fieldHandle} .buttons .btn {
                    position: relative;
                    border-bottom-right-radius: 0;
                    border-bottom-left-radius: 0;
                }
                #fields-{$fieldHandle} .buttons .btngroup .btn:first-child:after {
                    left: -1px;
                    width: calc(100% + 1px);
                }
                #fields-{$fieldHandle} .buttons .btngroup .btn:last-child:after {
                    left: auto;
                    right: -1px;
                    width: calc(100% + 1px);
                }
                #fields-{$fieldHandle} .buttons .btn:after {
                    content: '';
                    position: absolute;
                    bottom: -3px;
                    left: 0;
                    width: 100%;
                    height: 3px;
                }
                ";

                foreach ($blocks as $blockHandle => $colour)
                {
                    $strCss .= "#fields-{$fieldHandle} .matrixblock[data-type='{$blockHandle}']:after,
                    #fields-{$fieldHandle} .buttons .btn[data-type='{$blockHandle}']:after {
                        background-color: {$colour};
                    }
                    ul[role='listbox'] a[data-type='{$blockHandle}'] {
                        position: relative;
                    }
                    ul[role='listbox'] a[data-type='{$blockHandle}']:after {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 5px;
                        height: 100%;
                        background-color: {$colour};
                    }
                    ";
                }
            }

            // Add css to control panel page
            Craft::$app->getView()->registerCss($strCss);
        }

        Craft::info(
            Craft::t(
                'matrix-colours',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

}
