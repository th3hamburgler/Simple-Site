<? $this->load->view('admin/common/header')?>

	<div class="grid_4 sub-nav navigations-nav" id="navigations-nav">
		<? $this->load->view('admin/navigations/nav')?>
	</div>

	<div class="grid_10">
		
		<h2>Update Navigation Items</h2>
		
		<?=$update_form?>
		
	</div>
	
	<div class="grid_10">
		
		<h2>Add New Item</h2>
		
		<?=$new_form?>
		
	</div>
	
	<div class="clear"></div>

<? $this->load->view('admin/common/footer')?>