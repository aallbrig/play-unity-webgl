<?php

function play_unity_webgl_page_html()
{
  ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="<?php menu_page_url('unity-webgl-games') ?>" method="post">
            <button type="submit">Submit</button>
        </form>
    </div>
  <?php
}
