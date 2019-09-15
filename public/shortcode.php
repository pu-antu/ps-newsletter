<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class psNwsLetterShortCode
 */
class psNwsLetterShortCode
{

    /**
     * @var
     */
    public static $_instance;

    /**
     * @var string
     */
    public $plugin_version = '1.0.0';

    /**
     * @var
     */
    public $base;

    /**
     * @var string
     */
    public $file = __FILE__;


    /**
     * psNwsLetterShortCode constructor.
     */
    public function __construct()
    {
        $this->psInitShortCode();
    }

    /**
     *
     */
    public function psInitShortCode()
    {
        add_shortcode('ps_newsletter', array($this, 'psShowShortCode'));
    }

    /**
     * @param $atts
     * @param null $content
     * @return false|string
     */
    public function psShowShortCode($atts, $content = NULL)
    {

        ob_start();
        ?>
        <form action="#" method="POST" class="ps-newsletter">
            <label for="ps-newsletter-email"></label>
            <input type="email" name="email" id="ps-newsletter-email" class="ps-newsletter-email"
                   placeholder="Enter your email....">
            <input type="text" name="name" id="ps-newsletter-name" class="ps-newsletter-name"
                   placeholder="Enter your name....">
            <input type="submit" value="subscribe" class="ps_newsletter_submit">
        </form>
        <?php
        return ob_get_clean();
    }

    /**
     * @return psNwsLetterShortCode
     */

    public static function psInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new psNwsLetterShortCode();
        }
        return self::$_instance;
    }

}

$env_instance = psNwsLetterShortCode::psInstance();

?>