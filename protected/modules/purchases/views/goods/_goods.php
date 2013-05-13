<?php $page = ($offset + $c['limit']) / $c['limit'] ?>
<?php $added = false; ?>
<?php foreach ($goods as $good): ?>
  <tr<?php if(!$added) { echo ' rel="page-'. $page .'"'; $added = true; } ?> >
    <td><?php echo $good->good_id ?></td>
    <td><?php echo ActiveHtml::showUploadImage(($good->image) ? $good->image->image : '', 'b') ?></td>
    <td><?php echo $good->artikul ?></td>
    <td><?php echo ActiveHtml::link($good->name, '/good'. $good->purchase_id .'_'. $good->good_id) ?></td>
    <td><?php echo ActiveHtml::price($good->price) ?></td>
    <td><?php echo ($good->is_range) ? 'Да' : 'Нет' ; ?></td>
    <td><?php echo Yii::t('purchase', '{n} заказ|{n} заказа|{n} заказов', $good->ordersNum) ?></td>
    <td><?php echo ActiveHtml::price($good->ordersSum) ?></td>
  </tr>
<?php endforeach; ?>