<div id="access">
    <div class="skip-link screen-reader-text">
        <a <?php atts(array('href' => "#content", 'title' => esc_attr_e( 'Skip to content', 'twentyten' ))); ?>><?php echo _e( 'Skip to content', 'twentyten' ) ?></a>
    </div>
    <?php wp_nav_menu( 'sort_column=menu_order' ) ?>
</div>
