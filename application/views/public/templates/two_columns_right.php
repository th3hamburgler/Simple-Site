<?=$this->load->view('public/templates/common/header');?>
			
				<div class="grid_6 content" id="content">					
					
					<?=zones($zone_A)?>
					
				</div>
				
				<div class="grid_18 zone" id="zone-A">
				
					<?=$page->content()?>
					
				</div>
				
				<div class="clear"></div>

<?=$this->load->view('public/templates/common/footer');?>
