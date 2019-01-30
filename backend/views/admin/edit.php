<?php
use yii\helpers\Html;
use yii\helpers\Url;
use core\helpers\Form;
use backend\widgets\FormContainer;
use backend\widgets\ImageField;
use yii\bootstrap\ActiveForm;
?>
<?php
/**
 * 
 */
?>
<?php 
    $formid = 'edit_form';
    $container = FormContainer::begin([
       'tabs' => [
           [
              'title' => '用户信息',
              'target'   => 'user_base_info',
           ],
           [
              'title'    => '资料信息',
              'target'   => 'user_profile_info',
           ],       
       ],
       'form' => $formid,
    ]); 
    $form = ActiveForm::begin(['id' => $formid]);
?>
<div id="user_base_info" class="tab-target">
    <?= $form->field($user, 'username') ?>
    <?= $form->field($user, 'nickname') ?>
    <?= $form->field($user, 'email') ?>
    <?= $form->field($user, 'is_active')->dropDownList(Form::statusList()) ?>
    <?= $form->field($user, 'password')->passwordInput() ?>
    <?= $form->field($user, 'password_confirm')->passwordInput() ?>
    <?= $form->field($user, 'current_password')->passwordInput() ?>
</div>
<div id="user_profile_info" class="tab-target">
    <?= $form->field($profile, 'phone') ?>
    <?= $form->field($profile, 'email') ?>
    <?= $form->field($profile, 'wechat')?>
    <?= $form->field($profile, 'qq')?>
    <?= $form->field($profile, 'sex')->dropDownList(Form::sexList(), ['prompt' => ''])?>
    <?= $form->field($profile, 'avatorImage')->widget(ImageField::className(), [
        'url' => $profile->getAvatorUrl(true),
    ]) ?>
    <?= $form->field($profile, 'note')->textarea() ?>
</div>
<?php 
    ActiveForm::end();
    FormContainer::end();
 ?>
