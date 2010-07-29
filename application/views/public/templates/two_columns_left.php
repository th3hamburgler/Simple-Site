<?=$this->load->view('public/templates/common/header');?>
			
				<div class="grid_18 content" id="content">
				
					<?=$page->content()?>
					
				</div>
				
				<div class="grid_6 zone" class="zone-A">					
					
					<?=zones($zone_A)?>
					
				</div>	
				
				<div class="clear"></div>

<?=$this->load->view('public/templates/common/footer');?>
