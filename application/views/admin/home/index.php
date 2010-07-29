<? $this->load->view('admin/common/header')?>

	<div class="grid_4 sub-nav home-nav" id="home-nav">
		<? $this->load->view('admin/home/nav')?>
	</div>

	<div class="grid_20">
	
		<h1>Welcome</h1>
		
		<div class="grid_10 alpha">
		
			<h4>Updates 0.11</h4>
			
			<h6>29/07/10</h6>
		
			<ol>
				<li>Multiple <b>Pages</b> can now be deleted from <?=anchor('admin/pages/', 'List Page')?></b></li>
			</ol>
				
			<h4>Updates 0.1</h4>
			
			<h6>23/07/10</h6>
		
			<ol>
				<li>Update <b>Site</b> details including
					<ul>
						<li>Name</li>
						<li>URL</li>
						<li>Designated Homepage</li>
					</ul>
				</li>
				<li>
					Add &amp update <b>Navigation Menus</b> including:
					<ul>
						<li>Name</li>
						<li>Description</li>
					</ul>
				</li>
				<li>Add <b>Page Items</b> to <b>Navigation Menus</b> and order them</li>
				<li>Add &amp update <b>Pages</b> including:
					<ul>
						<li>Title</li>
						<li>Slug</li>
						<li>Content (with MarkItUp)</li>
						<li>Parent Page</li>
						<li>Template</li>
					</ul>
				</li>
				<li>Add &amp update <b>Partials</b> including:
					<ul>
						<li>Name</li>
						<li>Content (with MarkItUp)</li>
					</ul>
				</li>
				<li>
					Add <b>Partials</b> to <b>Pages</b>:
					<ul>
						<li>Assign to specific <b>zone</b> on the <b>template</b></li>
						<li>Order multiple <b>partials</b> in one <b>zone</b></li>
					</ul>
				</li>
				<li>Add &amp update <b>Templates</b> including:
					<ul>
						<li>Name</li>
						<li>Description</li>
						<li>Number of <b>zones</b> (for <b>partials</b>)</li>
					</ul>
				</li>
			</ol>
		
		</div>
		
		<div class="grid_10 omega">
			
			<h3>To Do</h3>
			
			<h6>In order of when i typed them</h6>
			
			<ul>
				<li>Define <b>Navigation lists</b> in Template.</li>
				<li>Add external links to <b>navigation Lists</b>.</li>
				<li>Make changes to <b>page</b> without appearing live until ready.</li>
				<li>Cache <b>pages/site</b></li>
				<li>Present <b>site</b> structure in a visual way</li>
				<li>Present <b>page</b> structure in a visual way</li>
				<li>Present <b>navigation</b> structure in a visual way</li>
				<li>Add meta data to site elements including:
					<ul>
						<li><b>Site</b></li>
						<li><b>Page</b></li>
						<li><b>Partial</b></li>
						<li><b>Navigation</b></li>
					</ul>
				</li>
				<li>Image and Media Manager:
					<ul>
						<li>Images (Keep large size and make a thumb)</li>
						<li>Videos</li>
						<li>Sounds</li>
						<li>Images are resized to</li>
					</ul>
				</li>
			</ul>
			
		</div>
		
	</div>
	
	<div class="clear"></div>

<? $this->load->view('admin/common/footer')?>