<?=$this->load->view('public/templates/common/header');?>
			
				<div class="grid_12 content" id="content">
				
					<?=$page->content()?>
					
				</div>
				
				<div class="grid_6 zone zone-A" class="zone-A">					
					
					<?=zones($zone_A)?>
					
				</div>
				
				<div class="grid_6 zone zone-B" class="zone-B">					
					
					<?=zones($zone_B)?>
					
				</div>	
				
				<div class="clear"></div>

<?=$this->load->view('public/templates/common/footer');?>
