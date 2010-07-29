<?=$this->load->view('admin/common/head')?>

	<body class="no-js">
			
		<div class="page-wrapper">
		
			<div class="container_24">
		
				<? if($this->oi->messages(NULL, TRUE)): ?>
				<div class="grid_24 oi-wrapper">
					<?=$this->oi->messages()?>
				</div>
				<? endif; ?>
				
			    <div class="header grid_24" id="header">
			    
					<h1>Simple Site</h1>
													
			    </div> <!-- end header -->
			    
			    <div class="clear"></div>
			    
			    <div class="main-nav grid_24" id="main-nav">
			    
			    	<?=$this->load->view('admin/common/main-nav')?>
			    
			    </div>