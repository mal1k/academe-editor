<?php /* Template Name: Lesson Editor */ 
//echo 'test'
?>

<?php get_template_part( 'templates/partials/document-open' ); ?>
    <div id="app" class="main">
        <?php //$wpnonce = wp_create_nonce(); ?>
        <lesson-editor-layout>
            <template v-slot:header>
                <header-component :post="123"></header-component>
            </template>
            <template v-slot:editor>
                <lesson-editor v-slot="editor" :post="123"></lesson-editor>
            </template>

        </lesson-editor-layout>

    </div>
<?php get_template_part( 'templates/partials/document-close' ); ?>