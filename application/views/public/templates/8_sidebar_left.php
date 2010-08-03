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
		<h1>Liberty Electric Cars</h1>
		<p class="large">Pioneer of the automotive industry, the UK-based re-engineering company has developed, tested and proven world-beating technology that is set to revolutionise the automotive market, providing for the first time large, luxury, Pure Electric 4x4s that perform as well as, if not better than, any internal combustion engine equivalent on the market today.</p>
	</div>
	
	<div class="content">
		<!-- ZONE B -->
		<?=zones($zone_B)?>
	</div>
</div>

<? $this->load->view('public/templates/common/footer')?>