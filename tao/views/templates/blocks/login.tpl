<div id="login-box">
    <?php if (has_data('msg')) : ?>
        <span class="loginHeader">
		    <span class="hintMsg"><?= get_data('msg') ?></span>
		</span>
    <?php endif; ?>
    <? if (get_data('errorMessage')): ?>
        <div class="feedback-error" display>
            <?= urldecode(get_data('errorMessage')) ?>
        </div>
    <? endif ?>
    <?= get_data('form') ?>
</div>
<script>
    requirejs.config({
        config: {
            'login': {
                'info': <?=json_encode(get_data('msg'))?>,
                'error': <?=json_encode(urldecode(get_data('errorMessage')))?>
            }
        }
    });
</script>