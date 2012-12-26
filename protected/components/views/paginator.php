<?php
$page = ($this->offset + $this->delta) / $this->delta;
$pages = ceil($this->offsets / $this->delta);
$minpage = ($page > 3) ? $page - 2 : 1;
$maxpage = ($page < $pages - 2) ? $page + 2 : $pages;
$prevoffset = ($page > 1) ? $this->offset - $this->delta : 0;
$nextoffset = ($page < $maxpage) ? $this->offset + $this->delta : $this->offsets - $this->delta;
?>
<div class="pagination">
    <ul>
        <? if ($page > 4) :?>
        <li class="disabled">
            <? $this->url['offset'] = $prevoffset; ?>
            <? echo CHtml::link('&laquo;', $this->url, array('onclick' => 'return nav.go(this, event)')); ?>
        </li>
        <? endif; ?>
        <? for ($i=$minpage; $i<=$maxpage; $i++): ?>
        <li<? if ($page == $i): ?> class="active"<? endif; ?>>
            <? $this->url['offset'] = ($i * $this->delta) - $this->delta; ?>
            <? echo CHtml::link($i, $this->url, array('onclick' => 'return nav.go(this, event)')); ?>
        </li>
        <? endfor; ?>
        <? if ($pages > $maxpage): ?>
        <li>
            <? $this->url['offset'] = $nextoffset; ?>
            <? echo CHtml::link('&raquo;', $this->url, array('onclick' => 'return nav.go(this, event)')); ?>
        </li>
        <? endif; ?>
    </ul>
</div>