<? $this->load->view('admin/common/header')?>

	<div class="grid_4 sub-nav page-nav" id="page-nav">
		<? $this->load->view('admin/pages/nav')?>
	</div>
	
	<div class="grid_20">

		<div class="grid_20 alpha omega">
			<h2>Update Page Zones</h2>
		</div>
		
		<div class="clear"></div>
	
		<div class="grid_10 alpha">
					
			<?=$update_form?>
			
		</div>
		
		<div class="grid_10 omega">
			
			<?=$page_form?>
			
			<?=$new_form?>
			
		</div>
		
		<div class="clear"></div>
		
	</div>


<? $this->load->view('admin/common/footer')?>