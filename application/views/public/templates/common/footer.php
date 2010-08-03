    <div class="clear"></div>
    <div id="tail">
    	<div class="grid_6 tail-nav">
        	<dl>
            	<dt>About Liberty</dt>
                <dd><a href="<?=site_url('pages/about');?>">The Company</a></dd>
                <dd><a href="<?=site_url('pages/people');?>">The People At The Wheel</a></dd>
                <dd><a href="#">Exciting Cars For Private Customers</a></dd>
                <dd><a href="#">Efficient Fleet Management</a></dd>
                <dd><a href="#">Your Personal Consultancy</a></dd>
                <dd><a href="#">FAQs</a></dd>
			</dl>
			<dl>
             	<dt>Hot News</dt>
            </dl>
        </div>
    	<div class="grid_6 tail-nav">
        	<dl>
            	<dt>Exciting Electric Cars</dt>
                <dd><a href="#">Specialising in 4x4, SUVs</a></dd>
                <dd><a href="#">E-Range</a></dd>
                <dd><a href="#">Bug &ldquo;e&rdquo;</a></dd>
                <dd><a href="#">Discovery</a></dd>
			</dl>
			<dl>
             	<dt>Technology</dt>
                <dd><a href="#">Innovative Electric Drive Trains</a></dd>
                <dd><a href="#">Highly Skilled Engineers</a></dd>
                <dd><a href="#">Unique &amp; Safe Energy Storage</a></dd>
            </dl>
        </div>
    	<div class="grid_6 tail-nav">
        	<dl>
            	<dt>Pure Electric</dt>
                <dd><a href="#">Better For Environment &amp; Health</a></dd>
                <dd><a href="#">Government Incentives</a></dd>
                <dd><a href="#">Easy Charging</a></dd>
                <dd><a href="#">Better For Your Pocket</a></dd>
			</dl>
			<dl>
             	<dt>Convinced?</dt>
                <dd><a href="#">The Ultimate Driving Experience</a></dd>
                <dd><a href="#">Book A Test Drive</a></dd>
                <dd><a href="#">Looking For A Distributor?</a></dd>
                <dd><a href="#">Interested In Selling Electric Cars?</a></dd>
                <dd><a href="#">Need Assistance?</a></dd>
            </dl>
        </div>
		<div class="grid_6 contact-nav">
			<p><img src="<?=base_url();?>img/public/furniture/contact-icon-email.png" alt="Email icon" /><?=safe_mailto('info@liberty-ecars.com');?></p>
			<p><img src="<?=base_url();?>img/public/furniture/contact-icon-facebook.png" alt="Facebook icon" /><a href="#">Find us on Facebook</a></p>
			<p><img src="<?=base_url();?>img/public/furniture/contact-icon-twitter.png" alt="Twitter icon" /><a href="#">Follow us on Facebook</a></p>
			<p>UK: <span class="white">+44 1865 784474</span><br />
			   US: <span class="white">+1 312-283-5460</span></p>
			
		</div>
		<div class="clear"></div>
    </div>
</div>
<p class="attribution">&copy; <?=date('Y');?> Liberty Electric Cars &middot; <a href="http://www.artandsoul.co.uk">Web design</a> by Art &amp; Soul</p>
<?= $this->wrapup->inline(); ?>
</body>
</html>