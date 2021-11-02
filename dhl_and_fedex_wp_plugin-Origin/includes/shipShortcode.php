
<?php add_shortcode('wpshipShortCode', 'wpShipFormShortCode');

function wpShipFormShortCode($atts=[], $content=null) {
   global $wpdb;
   $table_name='countries';
   $countrys=$wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name"));
   $form='
   
   <section class="shipping_home">
      <div class="shipping_home--bg">
      <div class="shipping_home--overlay"></div>
         <div class="shipping_home_content text-center">
            <div class="container">
               <h2>Pricing</h2>
               <p>
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Placeat eligendi et, voluptatem adipisci mollitia aliquid facilis dolore amet blanditiis cum impedit, repellendus pariatur assumenda debitis soluta quod fugit error! Nesciunt.
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Placeat eligendi et, voluptatem adipisci mollitia aliquid facilis dolore amet blanditiis cum impedit, repellendus pariatur assumenda debitis soluta quod fugit error! Nesciunt.
               </p>
            </div>
         </div>
      </div>
   
     
   
   <div class="wpship-bg text-center form-center">


<form style="max-width: 100%;" action="javascript:;"method="post"name="getCostForm" id="shipping_rate_form">
<div class="row">
<div class="title shipping_title_main">Where is your stock ?</div>
<div class="col-xs-12">
<div class="row">
<div class="col-sm-6">
<div class="dropdown-wrapper">
<div class="label_title">Source</div>
<select id="Source" name="source" aria-invalid="false" class="form-select form-select input_field">
<option selected>Select the Country</option>

';
foreach($countrys as $country)
   {
      $form .= '<option value="'.$country->id.'">'.$country->name.'</option>';
   }

$form .='
   
   </select>
</div>
</div>
<div class="col-sm-2 dropdown-wrapper">
<div class="label_title">States</div>
   <div>
   <select name="states" id="states" class="form-select input_field">
   <option value="">Select State</option>
   </select>
   </div>
</div>
<div class="col-sm-2 dropdown-wrapper">
<div class="label_title">Cities</div>
   <div>
   <select name="cities" id="cities" class="form-select input_field">
   <option value="">Select City</option>
   </select>
   </div>
   </div>
   <div class="col-sm-2 dropdown-wrapper">
   <div class="label_title">Postal</div>
   <div>
   <input type="text" name="postal" id="postal" class="form-select input_field" placeholder="Zip Code">
   </div>
   </div>
</div>
<div class="divider_wrapper">
<hr class="horizontal_divider">
</div>
</div>

<div class="col-xs-12">
<div class="title shipping_title_main">Where are you shipping to ?</div>

<div class="row">
   <div class="col-sm-6">
   <div class="dropdown-wrapper">
   <div class="label_title">Destination</div>
<select id="Destination"  name="destination" aria-invalid="false" class="form-select input_field">
<option selected>Select the Destination</option>';

   foreach($countrys as $country)
   {
      $form .= '<option value="'.$country->id.'">'.$country->name.'</option>';
   }

   $form .='
   
   </select>
   </div>
   </div>
   <div class="col-sm-2 dropdown-wrapper">
   <div class="label_title">States</div>
   <div>
   <select name="ship_states" id="ship_states" class="form-select input_field">
   <option value="">Select State</option>
   </select>
   </div>
   </div>
   <div class="col-sm-2 dropdown-wrapper">
   <div class="label_title">Cities</div>
   <div>
   <select name="ship_cities" id="ship_cities" class="form-select input_field">
   <option value="">Select City</option>
   </select>
   </div>
   </div>
   <div class="col-sm-2 dropdown-wrapper">
<div class="label_title">Postal</div>
   <div>
   <input type="text" name="ship_postal" id="ship_postal" class="form-select input_field" placeholder="Zip Code">
   </div>
   </div>
</div>

<div class="divider_wrapper">
   <hr class="horizontal_divider">
</div>



   </div><div class="clearfix xs-hide">&nbsp;
   </div><div class="col-sm-12 xs-hide col-xs-12"id="MidCaption"><div class="title title2">Specify us your package dimensions and weight..</div></div><div class="col-sm-3 col-xs-12"><div class="label_title">Weight (lbs)</div><div><input class="form-control text-center no-spinner form-select input_field form-select input_field_lg"value=""type="text"name="weight"id="Weight"></div></div><div class="col-sm-3 col-xs-12"><div class="label_title">Length (inch)</div><div><input class="form-control text-center no-spinner form-select input_field form-select input_field_lg"value=""name="length"type="text"id="Length"></div></div><div class="col-sm-3 col-xs-12"><div class="label_title">Width (inch)</div><div><input class="form-control text-center no-spinner form-select input_field form-select input_field_lg"value=""name="width"type="text"id="Width"></div></div><div class="col-sm-3 col-xs-12"><div class="label_title">Height (inch)</div><div><input class="form-control text-center no-spinner form-select input_field form-select input_field_lg"value=""name="height"type="text"id="Height"></div></div><div class="clearfix"></div><div class="col-sm-12 dim-warning"><i class="fa fa-exclamation-triangle"aria-hidden="true"></i>Why dimensions matter? Learn more about "Volumetric"weight</div><div class="clearfix">&nbsp;
   <div class="alert alert-danger" id="error_message">
   Please enter all input fields.
   </div>
   </div><div class="text-center col-sm-12"><button id="Rates"type="submit"class="btn btn-warning btn-lg getCost text-uppercase shipping_form_button">Get Shipping Rates</button></div><div class="clearfix">&nbsp;
   </div><div class="error dark hidden"></div></div></form>
   
   <div id="loader" class="hidden">
      <h3 class="title shipping_title_main">Calculating Price</h3>
      <div class="spinner-border spinner_lg" role="status">
         <span class="sr-only">Loading...</span>
      </div>
   </div>

   <div id="response_error" class="hidden">
      <div class="alert alert-danger m-0">
         <h3>Error Calculating the Price</h3>
         <p class="mt-5">An error occured while calculating the price... <a href="" class="alert-link">Try again</a> </p>
      </div>
   </div>
   
   </div><div class="renderDiv"></div> 
   
   
   
   </section>
   
   
  
   
   ';


   return $form;
}
?>