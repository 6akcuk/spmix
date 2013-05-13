<?php
$page = ($this->offset + $this->delta) / $this->delta;
$pages = ceil($this->offsets / $this->delta);
$minpage = ($page > 3 && $pages > 5) ? $page - 2 : 1;
$maxpage = ($page < $pages - 2) ? $page + 2 : $pages;
$prevoffset = ($page > 1) ? $this->offset - $this->delta : 0;
$nextoffset = ($page < $maxpage) ? $this->offset + $this->delta : $this->offsets - $this->delta;

$delim = (stristr($this->url, '?')) ? '&' : '?';
?>
<div class="pagination clearfix">
<?php if ($pages > 1): ?>
  <?php if (!$this->nopages): ?>
    <? if ($page > 3 && $pages > 5) :?>
    <? //$this->url['offset'] = $prevoffset; ?>
      <? echo ActiveHtml::link('<div class="pg_in">&laquo;</div>', $this->url . $delim .'offset=0', array('class' => 'pg_lnk left', 'nav' => array('search' => true, 'paginator' => true))); ?>
    <? endif; ?>
    <? for ($i=$minpage; $i<=$maxpage; $i++): ?>
    <? $offset = ($i * $this->delta) - $this->delta; ?>
      <? echo  ActiveHtml::link('<div class="pg_in">'. $i .'</div>', $this->url . $delim .'offset='. $offset, array('class' => ($page == $i) ? 'pg_lnk_sel left' : 'pg_lnk left', 'nav' => array('search' => true, 'paginator' => true))); ?>
    <? endfor; ?>
    <? if ($pages > $maxpage): ?>
      <? echo ActiveHtml::link('<div class="pg_in">&raquo;</div>', $this->url . $delim .'offset='. (($pages * $this->delta) - $this->delta), array('class' => 'pg_lnk left', 'nav' => array('search' => true, 'paginator' => true))); ?>
    <? endif; ?>
  </ul>
  <?php endif; ?>
  <script type="text/javascript">
  Paginator.init({
    target: '[rel="pagination"]',
    delta: <?php echo $this->delta ?>,
    offset: <? echo $this->offset; ?>,
    pages: <? echo $pages; ?>,
    url: '<?php echo $this->url ?>',
    forceUrl: <?php echo ($this->forceUrl) ? $this->forceUrl : 'false' ?>,
    nopages: <?php echo ($this->nopages) ? $this->nopages : 'false' ?>
  });
  </script>
<?php endif; ?>
</div>