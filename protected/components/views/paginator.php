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
            <? //$this->url['offset'] = $prevoffset; ?>
            <? echo ActiveHtml::link('&laquo;', $this->url .'?offset='. $prevoffset); ?>
        </li>
        <? endif; ?>
        <? for ($i=$minpage; $i<=$maxpage; $i++): ?>
        <li<? if ($page == $i): ?> class="active"<? endif; ?>>
            <? $offset = ($i * $this->delta) - $this->delta; ?>
            <? echo ActiveHtml::link($i, $this->url .'?offset='. $offset); ?>
        </li>
        <? endfor; ?>
        <? if ($pages > $maxpage): ?>
        <li>
            <? echo ActiveHtml::link('&raquo;', $this->url .'?offset='. $nextoffset); ?>
        </li>
        <? endif; ?>
    </ul>
</div>