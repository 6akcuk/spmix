<?php
/** @var $site SiteList */

Yii::app()->getClientScript()->registerCssFile('/css/purchases.css');
Yii::app()->getClientScript()->registerScriptFile('/js/purchase.js');

Yii::app()->getClientScript()->registerCssFile('/css/pagination.css');
Yii::app()->getClientScript()->registerScriptFile('/js/pagination.js');

$this->pageTitle = Yii::app()->name .' - Список сайтов';
$delta = Yii::app()->controller->module->sitesPerPage;
?>

<div id="_box_hidden_add" style="display: none">
  <form id="add_site_form">
  <?php if (!Yii::app()->user->checkAccess('purchases.purchases.siteListMyCity')): ?>
  <div class="clearfix row">
    <?php echo ActiveHtml::activeDropdown($model, 'city_id', City::getDataArray()) ?>
  </div>
  <?php endif; ?>
  <div class="row">
    <?php echo ActiveHtml::activeFieldPlaceholder('text', $model, 'site') ?>
  </div>
  <div class="row">
    <?php echo ActiveHtml::activeSmartTextarea($model, 'shortstory') ?>
  </div>
  </form>
</div>

<h1 id="prep_pos">
  Список сайтов
  <div class="right">
    <a class="button" onclick="addSite()">Добавить еще сайт</a>
  </div>
</h1>

<div rel="pagination">
  <?php $this->renderPartial('_sitelist', array('sites' => $sites, 'offset' => $offset)) ?>
</div>

<script>
function addSite() {
  var box = new Box({
    title: 'Добавить сайт',
    buttons: [{title: 'Добавить', onclick: function() {
      var ip = box.bodyNode.find('#SiteList_site'),
          site = $.trim(ip.val());
      if (!site) {
        ip.focus();
        return;
      }
      if (A.addSiteProgress) return;
      A.addSiteProgress = true;

      box.showProgress();
      ajax.post('/purchases/sitelist', $('#add_site_form').serialize(), function(r) {
        A.addSiteProgress = false;
        box.hideProgress();
        box.hide();
        msi.show(r.msg);
      }, function(xhr) {
        A.addSiteProgress = false;
        box.hideProgress();
        box.hide();
      });
    }
    }],
    onHide: function() {
      $('#_box_hidden_add').insertBefore('#prep_pos').hide();
    }
  });
  //box.content($('#_box_hidden_add').html());
  $('#_box_hidden_add').appendTo(box.bodyNode).show();
  box.show();
}

function saveSite() {

}
</script>