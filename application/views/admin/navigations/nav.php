<ul>
	<li><?=anchor('admin/navigations/index', 	'List Navigation menus')?></li>
	<li><?=anchor('admin/navigations/create', 	'Add Navigation menu')?></li>
	<? if($this->uri->segment(4)):?>
	<li><?=anchor('admin/navigations/update/'.$this->uri->segment(4), 'Update')?></li>
	<li><?=anchor('admin/navigations/items/'.$this->uri->segment(4), 'Items')?></li>
	<? endif; ?>
</ul>