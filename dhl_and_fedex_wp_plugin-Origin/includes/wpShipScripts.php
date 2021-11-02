<?php

if(!function_exists("wpship_plugin_scripts")){
	function wpship_plugin_scripts(){
		wp_enqueue_style("wpship-css",WPSHIP_PLUGIN_DIR."assets/css/forms.css");
		wp_enqueue_style("wpship2-css","https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css");
		wp_enqueue_script("wpship-jQuery","https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js");
		wp_enqueue_script('jQuery-data','https://code.jquery.com/jquery-2.2.4.min.js','','2.2.4');
		//wp_enqueue_script("wpship-jQuery-Validation","https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js");
		wp_enqueue_script("wpship2-jQuery-Validation","https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js");

		//JS
		// wp_register_script('wpship_bootstrap_js', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js','jQuery','1.9.1');
	 //    wp_enqueue_script('wpship_bootstrap_js');

	    // CSS
	    // wp_register_style('wpship_bootstrap_css', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
	    // wp_enqueue_style('wpship_bootstrap_css');
	    wp_enqueue_style("wpship--raleway-fonts","https://fonts.googleapis.com/css2?family=Raleway:wght@600&display=swap");
	    wp_enqueue_style("wpship-work-sans-fonts","https://fonts.googleapis.com/css2?family=Work+Sans&display=swap");
	    wp_enqueue_style("wpship-font-awesome","https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
		wp_enqueue_style("wpship_bootstrap_css","//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css");	
		// wp_deregister_style("prefix_bootstrap");
		//wp_enqueue_script("wpship-js",WPSHIP_PLUGIN_DIR."assets/js/validation.js",'','2.2.1');
		wp_enqueue_script("wpship-bootstrap-js","//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js");

		wp_enqueue_script("wpship-ajax-js",WPSHIP_PLUGIN_DIR."assets/js/ajax.js");

		wp_localize_script("wpship-ajax-js","wpship_ajax_url", array(
			"ajax_url" => admin_url("admin-ajax.php")
			));

	}
	
		add_action("wp_enqueue_scripts","wpship_plugin_scripts");
		add_action("admin_enqueue_scripts","wpship_plugin_scripts");

}

?>