<? $this->load->view('admin/common/header')?>

	<div class="grid_4 sub-nav page-nav" id="page-nav">
		<? $this->load->view('admin/pages/nav')?>
	</div>

	<div class="grid_10">
		
		<h2>Update Page Zones</h2>
		
		<?=$update_form?>
		
	</div>
	
	<div class="grid_10">
		
		<h2>Add New Partial to Zone</h2>
		
		<?=$new_form?>
		
	</div>
	
	<div class="clear"></div>

<? $this->load->view('admin/common/footer')?>