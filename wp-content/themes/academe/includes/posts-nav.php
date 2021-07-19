<?php
function numeric_posts_nav() {

    if( is_singular() )
        return;

    global $wp_query;

    /** Stop execution if there's only 1 page */
    if( $wp_query->max_num_pages <= 1 )
        return;

    $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
    $max   = intval( $wp_query->max_num_pages );

    /** Add current page to the array */
    if ( $paged >= 1 )
        $links[] = $paged;

    /** Add the pages around the current page to the array */
    if ( $paged >= 3 ) {
        $links[] = $paged - 1;
    }

    if ( ( $paged + 2 ) <= $max ) {
        $links[] = $paged + 1;
    }

    echo '<section class="pagination">
		<div class="">
			<div class="pagination-row">
				 ' . "\n";

    /** Previous Post Link */
    if ( get_previous_posts_link() )
        printf( '<div class="prev">%s</div>' . "\n", get_previous_posts_link() );

    /** Link to first page, plus ellipses if necessary */
    if ( ! in_array( 1, $links ) ) {
        $class = 1 == $paged ? ' class="active"' : '';

        printf( '<div%s ><a href="%s">%s</a></div>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

        if ( ! in_array( 2, $links ) )
            echo '<div>…</div>';
    }

    /** Link to current page, plus 2 pages in either direction if necessary */
    sort( $links );
    foreach ( (array) $links as $link ) {
        $class = $paged == $link ? ' class="item active"' : '';
        printf( '<div%s class="item"><a href="%s">%s</a></div>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
    }

    /** Link to last page, plus ellipses if necessary */
    if ( ! in_array( $max, $links ) ) {
        if ( ! in_array( $max - 1, $links ) )
            echo '<div>…</div>' . "\n";

        $class = $paged == $max ? ' class="active"' : '';
        printf( '<div%s><a href="%s">%s</a></div>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
    }

    /** Next Post Link */
    if ( get_next_posts_link() )
        printf( '<div class="next">%s</div>' . "\n", get_next_posts_link() );

    echo ' </div></div></section>' . "\n";
}