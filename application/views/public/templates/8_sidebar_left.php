<? $this->load->view('public/templates/common/header')?>

<div id="feature-image">
	<img src="<?= base_url(); ?>img/public/feature_images/enjoy-your-freedom.jpg" alt="" width="960" height="240" />
</div>

<div class="grid_8 news">
	<!-- ZONE A -->
	<?=zones($zone_A)?>
</div>

<div class="grid_16">
	
	<div class="content">
		<!-- ZONE B -->
		<?=zones($zone_B)?>
	</div>
</div>

<? $this->load->view('public/templates/common/footer')?>