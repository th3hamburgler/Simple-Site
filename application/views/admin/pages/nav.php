<ul>
	<li><?=anchor('admin/pages/index', 	'List Pages')?></li>
	<li><?=anchor('admin/pages/create', 'New Page')?></li>
	<? if($this->uri->segment(4)):?>
	<li><?=anchor('admin/pages/update/'.$this->uri->segment(4), 'Update')?></li>
	<li><?=anchor('admin/pages/partials/'.$this->uri->segment(4), 'Partials')?></li>
	<? endif; ?>
</ul>