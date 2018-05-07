<?php if(!is_front_page() && strpos($_SERVER['REDIRECT_URL'],'/dashboard') === false):?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
	  	<a href="'<?php echo home_url('/');?>'">Mesa Profesional</a>
  	</li>
    <li class="breadcrumb-item active" aria-current="page">
    	<?php echo the_title();?>
    </li>
  </ol>
</nav>
<?php endif;?>