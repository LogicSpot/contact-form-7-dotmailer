<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 *  Admin.
 *
 * @class        N2DMCF7_Admin
 * @version        1.0.0
 * @category    Class
 * @author        n2 Digital Media
 */
class N2DMCF7_Admin
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Admin classes
        add_action('init', array($this, 'includes'));
    }

    /**
     * Include classes required within admin.
     */
    public function includes()
    {
        // Classes
        include_once('class-settings.php');
    }
}

return new N2DMCF7_Admin();