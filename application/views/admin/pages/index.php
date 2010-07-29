<? $this->load->view('admin/common/header')?>

	<div class="grid_4 sub-nav pages-nav" id="pages-nav">
		<? $this->load->view('admin/pages/nav')?>
	</div>

	<div class="grid_20 table">
		
		<form method="POST">
		
			<?=$table?>
			
			<button type="submit" value="submit" name="delete_all" class="button">Delete Checked</button>
			
		</form>
		
	</div>
	
	<div class="clear"></div>

<? $this->load->view('admin/common/footer')?>