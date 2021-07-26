<?php
    get_header();
    ?>
    <main class="main">
        <section id="guidesFilters" class="filters-section">
            <h1>
                <?php _e('Search', 'academe-theme'); ?>
            </h1>
            <div class="free-search-wrap">
                <form class="free-search">
                    <?php icon('search', 'search-btn'); ?>
                    <input class="search-input" type="search" name="s" placeholder="<?php _e('Search', 'academe-theme'); ?>" value="<?php echo isset($_GET['s']) ? $_GET['s'] : ''; ?>" />
                </form>
            </div>
            <?php get_template_part('templates/partials/filter-button', 'null', [
                'filters' => ['faculty', 'subject', 'topic'],
                'button_text' => __('Advanced<br> Search', 'academe-theme'),
                'apply_async' => true,
            ]); ?>
        </section>
        <section id="searchResults" style="color: #fff">

        </section>

        <div class="ui active dimmer loading">
            <div class="ui large text loader"><?php _e('Fetching search results', 'academe-theme'); ?></div>
        </div>


    </main>
<?php get_footer(); ?>