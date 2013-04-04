<?php
$page = ($this->offset + $this->delta) / $this->delta;
$pages = ceil($this->offsets / $this->delta);
$minpage = ($page > 3) ? $page - 2 : 1;
$maxpage = ($page < $pages - 2) ? $page + 2 : $pages;
$prevoffset = ($page > 1) ? $this->offset - $this->delta : 0;
$nextoffset = ($page < $maxpage) ? $this->offset + $this->delta : $this->offsets - $this->delta;
?>
<div class="pagination clearfix">
<?php if ($pages > 1): ?>
    <?php if (!$this->nopages): ?>
    <ul>
        <? if ($page > 4) :?>
        <li class="disabled">
            <? //$this->url['offset'] = $prevoffset; ?>
            <? echo ActiveHtml::link('&laquo;', $this->url .'?offset='. $prevoffset, array('nav' => array('search' => true, 'paginator' => true))); ?>
        </li>
        <? endif; ?>
        <? for ($i=$minpage; $i<=$maxpage; $i++): ?>
        <li<? if ($page == $i): ?> class="active"<? endif; ?>>
            <? $offset = ($i * $this->delta) - $this->delta; ?>
            <? echo ($page == $i) ? '<a>'. $i .'</a>' : ActiveHtml::link($i, $this->url .'?offset='. $offset, array('nav' => array('search' => true, 'paginator' => true))); ?>
        </li>
        <? endfor; ?>
        <? if ($pages > $maxpage): ?>
        <li>
            <? echo ActiveHtml::link('&raquo;', $this->url .'?offset='. $nextoffset, array('nav' => array('search' => true, 'paginator' => true))); ?>
        </li>
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