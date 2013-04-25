<?php
/**
 * @var $userinfo User
 * @var $friend ProfileRelationship
 */
$title = Yii::app()->name;

Yii::app()->getClientScript()->registerCssFile('/css/profile.css');
Yii::app()->getClientScript()->registerScriptFile('/js/profile.js');

Yii::app()->getClientScript()->registerCssFile('/css/photoview.css');
Yii::app()->getClientScript()->registerScriptFile('/js/photoview.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

if ($userinfo)
    $title .= ' - ' .
        ((Yii::app()->user->checkAccess('global.fullnameView'))
            ? $userinfo->profile->lastname .' '. $userinfo->profile->firstname .' '. $userinfo->profile->middlename
            : $userinfo->login .' '. $userinfo->profile->firstname);

$this->pageTitle = $title;
?>
<div class="profile-header clearfix">
    <span class="left username">
        <?php if (Yii::app()->user->checkAccess('global.fullnameView')): ?>
        <?php echo $userinfo->profile->firstname ?> <?php echo $userinfo->profile->lastname ?>
        <?php else: ?>
        <?php echo $userinfo->login ?> <?php echo $userinfo->profile->firstname ?>
        <?php endif; ?>
    </span>
    <?php if ($userinfo->role->itemname == 'Организатор'): ?><span class="left userspecial">(Организатор)</span><?php endif; ?>
    <?php if ($userinfo->role->itemname == 'Модератор'): ?><span class="left userspecial">(Модератор)</span><?php endif; ?>
    <?php if ($userinfo->role->itemname == 'Администратор'): ?><span class="left userspecial">(Администратор)</span><?php endif; ?>
    <span class="right useronline">
        <?php
        if ($userinfo->isOnline()) {
            echo "Online";
        }
        else {
            echo "был". (($userinfo->profile->gender == 'Female') ? "а" : "") ." в сети ". ActiveHtml::timeback($userinfo->lastvisit);
        }
        ?>
    </span>
</div>
<div class="profile-columns clearfix">
  <div class="left profile-left">
    <div class="profile-photo">
        <?php $photo = json_decode($userinfo->profile->photo, true); ?>
        <?php if (is_array($photo) && sizeof($photo)): ?>
        <?php echo ActiveHtml::showUploadImage($userinfo->profile->photo, 'a') ?>
        <?php else: ?>
        <img src="/images/camera_a.gif" width="250" alt="" />
        <?php endif; ?>
    </div>
    <?php if ($userinfo->id != Yii::app()->user->getId()): ?>
    <div class="module profile-socials">
        <?php echo ActiveHtml::link('Отправить сообщение', '/write'. $userinfo->id, array('class' => 'button', 'nav' => array('box' => 1))) ?>
    <?php $relationship = $userinfo->profile->getProfileRelation(); ?>
        <?php
            if ($relationship == null ||
                ($relationship->rel_type == ProfileRelationship::TYPE_INCOME && $relationship->from_id == Yii::app()->user->getId()) ||
                ($relationship->rel_type == ProfileRelationship::TYPE_OUTCOME && $relationship->from_id != Yii::app()->user->getId())): ?>
        <a class="button" onclick="return Profile.addFriend(this, <?php echo $userinfo->id ?>)">Добавить в друзья</a>
        <?php endif; ?>
        <?php
        if ($relationship != null) {
            if ($relationship->rel_type == ProfileRelationship::TYPE_FRIENDS) {
                ?>
        <div class="social-status"><?php echo $userinfo->getDisplayName() ?> у Вас в друзьях</div>
                <?php
            }
            elseif (Yii::app()->user->model->profile->isProfileRelationIncome($relationship)) {
                ?>
        <div class="social-status"><?php echo $userinfo->getDisplayName() ?> подписан<?php echo ($userinfo->profile->gender == 'Female') ? "а" : "" ?> на Вас</div>
                <?php
            }
            elseif (Yii::app()->user->model->profile->isProfileRelationOutcome($relationship)) {
                ?>
        <div class="social-status">Вы отправили заявку</div>
                <?php
            }
        }
        ?>
    </div>
    <?php endif; ?>
    <div class="module profile-num-menu">
      <div>
        <?php echo ActiveHtml::link(
          '<span class="right iconify_cart_a"></span><span class="right">'. $purchasesNum .'</span> Закупки '. ActiveHtml::lex(2, $userinfo->profile->firstname),
          '/purchases'. $userinfo->id
        ) ?>
      </div>
    </div>
      <div class="module">
          <a href="/friends?id=<?php echo $userinfo->id ?>" onclick="return nav.go(this, event, {noback: false})" class="module-header">
              <div class="header-top">
                  Друзья
              </div>
              <div class="header-bottom">
                  <?php echo Yii::t('user', '{n} друг|{n} друга|{n} друзей', $friendsNum) ?>
              </div>
          </a>
      </div>
      <div class="module-body">
      <?php if ($friends): ?>
      <?php $fcnt = 0; ?>
      <?php foreach ($friends as $friend): ?>
      <?php $fcnt++; ?>
      <?php if ($fcnt > 6) break; ?>
      <?php if ($fcnt == 1 || $fcnt == 4): ?>
      <div class="clearfix people_row">
      <?php endif; ?>
          <div class="left people_cell">
              <?php echo ActiveHtml::link($friend->friend->profile->getProfileImage('c'), '/id'. $friend->friend->id, array('class' => 'ava')) ?>
              <div class="people_name">
              <?php echo ActiveHtml::link($friend->friend->login, '/id'. $friend->friend->id) ?>
              </div>
          </div>
      <?php if ($fcnt == 3 || $fcnt == 6): ?>
      </div>
      <?php endif; ?>
      <?php endforeach; ?>
      <?php if ($fcnt < 3 || ($fcnt > 3 && $fcnt < 6)): ?></div><?php endif; ?>
      <?php endif; ?>
      </div>
    <?php if ($friendsOnlineNum > 0): ?>
      <div class="module">
        <a href="/friends?id=<?php echo $userinfo->id ?>&section=online" onclick="return nav.go(this, event, {noback: false})" class="module-header">
          <div class="header-top">
            Друзья онлайн
          </div>
          <div class="header-bottom">
            <?php echo Yii::t('user', '{n} друг|{n} друга|{n} друзей', $friendsOnlineNum) ?>
          </div>
        </a>
      </div>
      <div class="module-body">
        <?php if ($friendsOnline): ?>
        <?php $fcnt = 0; ?>
        <?php foreach ($friendsOnline as $friend): ?>
          <?php $fcnt++; ?>
          <?php if ($fcnt > 6) break; ?>
          <?php if ($fcnt == 1 || $fcnt == 4): ?>
            <div class="clearfix people_row">
          <?php endif; ?>
          <div class="left people_cell">
            <?php echo ActiveHtml::link($friend->friend->profile->getProfileImage('c'), '/id'. $friend->friend->id, array('class' => 'ava')) ?>
            <div class="people_name">
              <?php echo ActiveHtml::link($friend->friend->login, '/id'. $friend->friend->id) ?>
            </div>
          </div>
          <?php if ($fcnt == 3 || $fcnt == 6): ?>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($fcnt < 3 || ($fcnt > 3 && $fcnt < 6)): ?></div><?php endif; ?>
      <?php endif; ?>
      </div>
    <?php endif; ?>
    </div>
    <div class="left profile-right">
      <?php if (Yii::app()->user->getId() == 1): ?>
        <?php if (($userinfo->id != Yii::app()->user->getId() && $userinfo->profile->status) || (Yii::app()->user->getId() == $userinfo->id)): ?>
      <div class="profile-info profile-status-container">
        <?php if (Yii::app()->user->getId() == $userinfo->id): ?>
        <a id="profile-status" class="profile-status-change" onclick="Profile.showStatusEditor(this)">
        <?php endif; ?>
          <?php echo ($userinfo->profile->status) ?: 'Изменить статус' ?>
        <?php if (Yii::app()->user->getId() == $userinfo->id): ?>
        </a>
        <div id="profile-status-editor">
          <input type="text" name="profile_status" value="" style="width:340px" />
          <a class="button" onclick="Profile.saveStatus()">Сохранить</a>
        </div>
        <?php endif; ?>
      </div>
        <?php endif; ?>
      <?php endif; ?>
        <div class="profile-info">
            <div class="clearfix">
                <div class="label left">
                    Город:
                </div>
                <div class="labeled left">
                    <?php echo ActiveHtml::link($userinfo->profile->city->name, '/search?c[section]=people&c[city_id]='. $userinfo->profile->city_id) ?>
                </div>
            </div>
          <?php if (Yii::app()->user->checkAccess('global.phoneView')): ?>
          <div class="clearfix miniblock">
            <div class="label left">
              Телефон:
            </div>
            <div class="labeled left">
              <?php echo $userinfo->profile->phone ?>
            </div>
          </div>
          <?php endif; ?>
            <div class="clearfix miniblock">
                <div class="label left">
                    Зарегистрирован<?php echo (($userinfo->profile->gender == 'Female') ? "а" : "") ?>:
                </div>
                <div class="labeled left">
                    <?php echo ActiveHtml::link(ActiveHtml::date($userinfo->regdate), '/search?c[section]=people&c[regdate]='. $userinfo->regdate) ?>
                </div>
            </div>
            <div class="clearfix miniblock">
                <div class="label left">
                    <?php echo ActiveHtml::link('Репутация', '/reputation'. $userinfo->id) ?>:
                </div>
                <div class="labeled left">
                    <span class="profile-positive-rep">
                        <div id="rep_pos_box" class="reputation_box" style="display:none">
                            <div class="row">
                            <?php if (!Yii::app()->user->checkAccess('users.profiles.increaseReputationAny')): ?>
                                <input type="radio" id="rep_value_1" name="rep_value" value="1" />
                                <label for="rep_value_1">+1</label>
                                <input type="radio" id="rep_value_5" name="rep_value" value="5" />
                                <label for="rep_value_5">+5</label>
                            <?php else: ?>
                                <span class="input_placeholder">
                                    <input type="text" id="rep_value" name="rep_value" value="" />
                                    <label for="rep_value">Кол-во голосов</label>
                                </span>
                            <?php endif; ?>
                            </div>
                            <div class="row">
                                <span class="input_placeholder">
                                    <textarea id="rep_comment" name="rep_comment"></textarea>
                                    <label for="rep_comment">Комментарий</label>
                                </span>
                            </div>
                            <a class="button" onclick="return Profile.doIncReputation(<?php echo $userinfo->id ?>)">Поднять репутацию</a>
                        </div>
                        <a class="iconify_plus_a" onclick="return Profile.incReputation(this, <?php echo $userinfo->id ?>)"></a>
                        <em id="pos_rep_value"><?php echo $userinfo->profile->positive_rep ?></em>
                    </span>
                    <span class="profile-negative-rep" onclick="return Profile.decReputation(this, <?php echo $userinfo->id ?>)">
                        <div id="rep_neg_box" class="reputation_box" style="display:none">
                            <div class="row">
                                <?php if (!Yii::app()->user->checkAccess('users.profiles.decreaseReputationAny')): ?>
                                <input type="radio" id="rep_neg_value_1" name="rep_value" value="1" />
                                <label for="rep_neg_value_1">-1</label>
                                <input type="radio" id="rep_neg_value_5" name="rep_value" value="5" />
                                <label for="rep_neg_value_5">-5</label>
                                <?php else: ?>
                                <span class="input_placeholder">
                                    <input type="text" id="rep_neg_value" name="rep_value" value="" />
                                    <label for="rep_value">Кол-во голосов</label>
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="row">
                                <span class="input_placeholder">
                                    <textarea id="rep_neg_comment" name="rep_comment"></textarea>
                                    <label for="rep_neg_comment">Комментарий</label>
                                </span>
                            </div>
                            <a class="button" onclick="return Profile.doDecReputation(<?php echo $userinfo->id ?>)">Понизить репутацию</a>
                        </div>
                        <em id="neg_rep_value"><?php echo $userinfo->profile->negative_rep ?></em>
                        <?php if (!Yii::app()->user->checkAccess('users.profiles.decreaseReputationUser')): ?><a class="iconify_dash_a" onclick="return Profile.decReputation(this, <?php echo $userinfo->id ?>)"></a><?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
      <?php if ($userinfo->id == Yii::app()->user->getId()): ?>
        <div class="module">
          <a class="module-header">
            <div class="header-top">
              Пригласить друзей на сайт
            </div>
          </a>
          <div class="module-body">
            <small class="profile-invite-info">За каждого зарегистрированного пользователя вы получите +1 балл в репутацию.</small>
            <div class="profile-invite">
              <div class="clearfix">
                <span class="left label">Ваш номер для приглашений</span>
                <span class="left labeled"><b><?php echo $userinfo->id ?></b></span>
              </div>
              <div class="clearfix">
                <span class="left label">Ваша ссылка для приглашений</span>
                <span class="left labeled"><a>http://spmix.ru/invite<?php echo $userinfo->id ?></a></span>
              </div>
            </div>
            <p>
              Вы можете пригласить своих коллег, друзей, знакомых на наш сайт сообщив им свой номер для
              приглашений или отправив ссылку (ссылку можно разместит, например на вашей странице в
              социальных сетях), также вы можете отправить им СМС сообщение. Отправка сообщения
              бесплатная.
            </p>
            <?php $invite = new InviteBySMS(); ?>
            <?php
              $form = $this->beginWidget('ext.ActiveHtml.ActiveForm', array(
                'id' => 'profile-invite-form',
                'action' => $this->createUrl('/inviteBySMS'),
                'method' => 'post',
              ));
            ?>
              <div class="row">
                <?php echo $form->inputPlaceholder($invite, 'phone') ?>
              </div>
              <div class="row">
                <?php echo $form->inputPlaceholder($invite, 'name') ?>
              </div>
              <div class="row profile-invite-text">
                <h6>Текст СМСки</h6>
                <p>
                  <?php echo $userinfo->profile->firstname ?>, удобный сайт оптовых закупок SPMIX.ru. Приглашение №<?php echo $userinfo->id ?>
                </p>
              </div>
              <div class="row">
                <a class="button" onclick="Profile.sendInvite()">Отправить</a>
              </div>
            <?php $this->endWidget(); ?>
          </div>
        </div>
      <?php endif; ?>
        <div class="module">
            <a class="module-header">
                <div class="header-top">
                    О себе
                </div>
            </a>
            <div class="module-body">
                <?php echo nl2br($userinfo->profile->about) ?>
            </div>
        </div>
      <?php if (Yii::app()->user->getId() == 1): ?>
      <?php

        ?>
      <div class="module">
        <div class="module-header">
          <div id="wall_header" class="header-top">
            <?php echo Yii::t('app', '{n} запись|{n} записи|{n} записей', $postsNum); ?>
          </div>
          <div class="header-bottom wall-post" onclick="event.cancelBubble=true;">
            <?php echo ActiveHtml::smartTextarea(
              'wall_post', '',
              array(
                'placeholder' => (Yii::app()->user->getId() == $userinfo->id) ? 'Что у Вас нового?' : 'Написать сообщение..',
                'onfocus' => 'Wall.showEditor()',
              )) ?>
            <div id="wall_post_btn" class="clearfix" style="display: none">
              <div id="wall_attaches" class="wall_post_attaches clearfix"></div>
              <a class="left button" onclick="Wall.doPost(<?php echo $userinfo->id ?>)">Отправить</a>
              <div id="wall_post_progress" class="left post_progress">
                <img src="/images/upload.gif" />
              </div>
              <div class="right">
                <?php echo ActiveHtml::upload('photo', '', 'Прикрепить фотографию', array('onchange' => 'Wall.attachPhoto({id})')) ?>
              </div>
            </div>
            <div id="reply_box" class="reply_box clearfix" onclick="event.cancelBubble=true;" style="display:none">
              <?php echo ActiveHtml::link(Yii::app()->user->model->profile->getProfileImage('c'), '/id'. Yii::app()->user->getId(), array('class' => 'reply_form_image')) ?>
              <div class="reply_form">
                <div class="reply_field_wrap clearfix">
                  <input type="hidden" id="reply_to" name="reply_to" />
                  <input type="hidden" id="reply_to_title" name="reply_to_title" />
                  <?php echo ActiveHtml::smartTextarea('reply_text', '', array('placeholder' => 'Комментировать..')) ?>
                </div>
                <div class="reply_attaches clearfix"></div>
                <div class="submit_reply clear">
                  <a class="button left" onclick="Wall.doReply(<?php echo $userinfo->id ?>)">Отправить</a>
                  <div class="left reply_to_title"></div>
                  <div class="right reply_attach_btn">
                    <?php echo ActiveHtml::upload('photo', '', 'Прикрепить фото', array('onchange' => 'Wall.replyAttachPhoto({id})')) ?>
                  </div>
                </div>
              </div>
            </div>
            <script type="text/javascript">
              $.extend(A, {
                wallLastID: <?php echo (isset($posts[0])) ? $posts[0]->post_id : 0 ?>,
                wallPhotoAttaches: 0,
                wallReplyPhotoAttaches: 0
              });
            </script>
          </div>
        </div>
        <div class="module-body">
          <div style="display: none">
            <?php $this->widget('Paginator', array(
              'url' => '/wall?id='. $userinfo->id,
              'forceUrl' => 1,
              'offset' => 0,
              'offsets' => $postsNum,
              'delta' => Yii::app()->getModule('users')->wallPostsPerPage,
              'nopages' => true,
            )); ?>
          </div>

          <div id="wall<?php echo $userinfo->id ?>" rel="pagination">
            <?php echo $this->renderPartial('_wall', array('posts' => $posts, 'offset' => 0)) ?>
          </div>
          <? if (0 + Yii::app()->getModule('users')->wallPostsPerPage < $postsNum && $postsNum > Yii::app()->getModule('users')->wallPostsPerPage): ?><a id="pg_more" class="pg_more" onclick="Paginator.showMore()">Еще записи</a><? endif; ?>
        </div>
      </div>

      <?php endif; ?>
    </div>
</div>