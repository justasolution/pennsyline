<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( $files ) : ?>
    <table class="table table-condensed">
        <tbody>
        <?php foreach ( $files as $file ) : ?>
            <?php printf( '<tr><td>%s</td><td><a href="%s&slug=%s" target="_blank">%s</td></tr>',
                $file['label'],
                $download_url,
                $file['slug'],
                $file['name']
                ) ?>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>