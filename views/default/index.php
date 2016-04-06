<div class="social-auth-default-index">
    <h1><?= $this->context->action->uniqueId ?></h1>

    <p>
<?= \yii\authclient\widgets\AuthChoice::widget([
     'baseAuthUrl' => ['/social-auth/default/auth'],
     'popupMode' => false,
]) ?>
    </p>
</div>
