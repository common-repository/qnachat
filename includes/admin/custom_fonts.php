<?php
/**
 * Add custom fonts (JOST).
 */
if (!defined('ABSPATH')) {
    exit;
} 
?>
<style>
@font-face {
    font-family: 'Jost';
    src: url('<?php echo esc_url(QNAC_URL . 'assets/fonts/jost-regular.woff2'); ?>') format('woff2'),
         url('<?php echo esc_url(QNAC_URL . 'assets/fonts/jost-regular.woff'); ?>') format('woff');
    font-weight: 400;
    font-style: normal;
}
@font-face {
    font-family: 'Jost';
    src: url('<?php echo esc_url(QNAC_URL . 'assets/fonts/jost-italic.woff2'); ?>') format('woff2'),
         url('<?php echo esc_url(QNAC_URL . 'assets/fonts/jost-italic.woff'); ?>') format('woff');
    font-weight: 400;
    font-style: italic;
}
@font-face {
    font-family: 'Jost';
    src: url('<?php echo esc_url(QNAC_URL . 'assets/fonts/jost-600.woff2'); ?>') format('woff2'),
         url('<?php echo esc_url(QNAC_URL . 'assets/fonts/jost-600.woff'); ?>') format('woff');
    font-weight: 600;
    font-style: normal;
}
</style>