<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 28.02.13
 * Time: 10:11
 * To change this template use File | Settings | File Templates.
 */

Yii::app()->getClientScript()->registerCssFile('/css/comments.css');
Yii::app()->getClientScript()->registerScriptFile('/js/comments.js');

?>
<a class="comment_show_more">Показать предыдущие 100500 комментариев</a>
<div id="hoop<?php echo $this->hoop_id ?>_comments" class="comments_list">
  <div id="comment_68876" class="comment_block clearfix">
    <div class="left photo">
      <?php echo ActiveHtml::link('<img src="http://cs1.spmix.ru/v1000/13a/HSzTMh0ZaN-.jpg" />', '/id1') ?>
    </div>
    <div class="left comment_data">
      <?php echo ActiveHtml::link('Денис Сирашев (6akcuk)', '/id1', array('class' => 'comment_author')) ?>
      <div class="comment_text">
        Убыток из-за нештатной ситуации с запуском “Ямал-402” оценен почти в 2 миллиарда рублей
        <br><br>
        Страховая компания СОГАЗ оценила размер ущерба в связи с нештатным запуском спутника связи "Ямал-402" в
        конце 2012 года почти в 2 миллиарда рублей, сказал журналистам глава страховщика Сергей Иванов.
        <br><br>
        Запуск ракеты-носителя "Протон-М" с разгонным блоком "Бриз-М" и спутником "Ямал-402" состоялся в ночь на 9
        декабря 2012 года с космодрома Байконур. Спутник не удалось штатно вывести на геопереходную орбиту из-за того,
        что разгонный блок отработал на четыре минуты меньше положенного времени. С помощью собственных двигателей
        "Ямала" спутник в четыре этапа довывели на геостационарную орбиту, он успешно прошел проверки и готов к
        эксплуатации.
        <br><br>
        "(Ущерб) составляет около 2 миллиардов рублей - 74 миллиона евро", - сказал Иванов.
        <br><br>
        Как уточнил замглавы СОГАЗа Николай Галушин, ущерб связан с "деградацией жизни" (сокращением срока службы)
        спутника на орбите.
        <br><br>
        Спутник застрахован в СОГАЗе на 309 миллионов евро на случай полной или частичной гибели во время запуска
        и нахождения на орбите в течение года. Соответствующий договор страхования был заключен с владельцем спутника,
        ОАО "Газпром космические системы", в марте 2012 года. Основная часть риска перестрахована на международном
        перестраховочном рынке, передает Прайм.
      </div>
      <div class="comment_control">
        <span class="comment_date">сегодня в 12:11 |</span>
        <a onclick="Comment.delete(68876)">Удалить</a>
      </div>
    </div>
  </div>
</div>
<div class="comment_reply">
  <form id="hoop<?php echo $this->hoop_id ?>_form" action="comment/add?hoop_id=<?php echo $this->hoop_id ?>&hoop_type=<?php echo $this->hoop_type ?>" method="post">
  <h6>Ваш комментарий</h6>
  <?php echo ActiveHtml::smartTextarea('Comment[text]', '', array('placeholder' => 'Комментировать..')) ?>
  <div id="hoop<?php echo $this->hoop_id ?>_attaches" class="comment_post_attaches clearfix">
  </div>
  <div class="comment_post clearfix">
    <div class="left">
      <a class="button" onclick="Comment.add(<?php echo $this->hoop_id ?>)">Отправить</a>
    </div>
    <div class="right">
      <?php echo ActiveHtml::upload('photo', '', 'Прикрепить фотографию', array('onchange' => 'Comment.attachPhoto('. $this->hoop_id .', {id})')) ?>
    </div>
  </div>
  </form>
  <script>
  $.extend(A, {
    commentPhotoAttaches: 0,
    commentHoop: {
      <?php echo $this->hoop_id ?>: [null, 0, 68876]
    }
  });
  </script>
</div>