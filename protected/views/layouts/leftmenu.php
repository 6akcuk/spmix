<ul>
  <li>
    <?php echo ActiveHtml::link('Моя страница', '/id'. Yii::app()->user->getId()) ?>
    <?php echo ActiveHtml::link('ред.', '/edit', array('class' => 'right')) ?>
  </li>
  <li id="friends_link">
    <?php echo ActiveHtml::link('Мои друзья', '/friends') ?>
    <?php
    if ($this->pageCounters['friends'])
      echo ActiveHtml::link('+'. $this->pageCounters['friends'], '/friends?section=requests', array('class' => 'right lm-counter'))
    ?>
  </li>
  <li id="pm_link">
    <?php echo ActiveHtml::link('Мои сообщения', '/mail') ?>
    <?php
    if ($this->pageCounters['pm'])
      echo ActiveHtml::link('+'. $this->pageCounters['pm'], '/mail', array('class' => 'right lm-counter'))
    ?>
  </li>
  <li id="orders_link">
    <?php echo ActiveHtml::link('Мои покупки', '/orders') ?>
    <?php
    if ($this->pageCounters['orders'])
      echo ActiveHtml::link('+'. $this->pageCounters['orders'], '/orders', array('class' => 'right lm-counter'))
    ?>
  </li>
  <li>
    <?php echo ActiveHtml::link('Мои настройки', '/settings') ?>
  </li>
</ul>
<?php if (in_array(Yii::app()->user->model->role->itemname, array('Администратор', 'Модератор'))): ?>
<ul>
<?php endif; ?>
<?php if(Yii::app()->user->checkAccess('users.users.index')): ?>
  <li>
    <?php echo ActiveHtml::link('Пользователи', '/users') ?>
  </li>
<?php endif; ?>
<?php if (Yii::app()->user->checkAccess('purchases.purchases.sitelist')): ?>
  <li>
    <?php echo ActiveHtml::link('Список сайтов', '/purchases/sitelist') ?>
  </li>
<?php endif; ?>
<?php if (Yii::app()->user->checkAccess('purchases.purchases.acquire')): ?>
  <li id="ac_purchase_link">
    <?php echo ActiveHtml::link('Одобрить закупки', '/purchases/acquire') ?>
    <?php
    if ($this->pageCounters['purchases'])
      echo ActiveHtml::link('+'. $this->pageCounters['purchases'], '/purchases/acquire', array('class' => 'right lm-counter'))
    ?>
  </li>
<?php endif; ?>
<?php if (in_array(Yii::app()->user->model->role->itemname, array('Администратор', 'Модератор'))): ?>
</ul>
<?php endif; ?>
