# pennsyline

Make sure the https is set in bokly appointment URL settings
Make sure the Rejected status is not considered is bokly settings since we create a appt with rejected status to generate invoice and master invoice
Make sure ID 1 developer.pennsyline@gmail.com staff user is present this helps generating generated and master invoices 
Don't update the plugins which are marked as dont update due to conflicts with gravity forms and form submissions
Drivers need to have to login to there accounts and create appointments to trigger emails to one driver
Confirmation step is missing and payment is not created if cash to be paid is 0 so always the service should have a price






=================+%%%%%%%%****************** Mady Notes ************%$%$^$%$$%$%$%$%$============================

Convey to client

Delete all old emails 
make sure old appointments are deleted and calendars are in sync

Drivers 

Pennsy Admin 

Administor -> Me 

Customer/Client -> Register

******** Mandatory ********

Create a new customer through Arrival and departures
Make sure ID 1 developer.pennsyline@gmail.com staff user is present this helps generating generated and master invoices 


TODO Items:
Penn URL to set to https to avoid signature form mixed content error


Pennsyline Bugs

Manual adjustment on invoice price is not updating - Done to test
Payments popup message about datatables - enabling Enable jQuery Migrate Helper fixed it
Processing popup over shows on gayments and mayments - Disabled processing to false on Datatables
 developer to add signature pending status as well 
Confirmation step is missing and payment is not created if cash to be paid is 0
Calendar view to show signature link token is missing - Done 
Fix client signature status is not changing pending to recieved gravity view forms —> Fixed issue was powerpack with elementor so using old 2.6.11 version only
Fix all errors in files dashboard logs 
Auto fill email address with name - Done
Client file number is missing some times ask client to make it mandatory to add in invoices - ??
Unable to create a wp user issue was email notificaiton needs to be set in order to create wp user - Done
Trigger emails to only driver not all drivers - Done
auto fill doesnt map street number and additional address
auto scroll to top on i confirm appt 
Generated invoice payment UI screen not updating with adjustment amount - Done need to refresh the section
Filter in master invoice to add fitler for payment status - Done
Hide Details for generated invoice think and see if its necessary 
make signature form response and more user friendly
677 , 678 duplicate payments are created verify once

New items 
Signature form needs appt date and appt time - DONE
Seen Abdallah hajali create appt says warning  Selected period doesn't match provider's schedule check what is it 
Yousif Elalim Selected period doesn't match provider's schedule
remove access of login use /access always
Customers section payments is showing not just total amount its add generated invoice master invoice as well same on appointments - DONE
Add read me to pennsyline git hub
Make sure the https is set in appt URL settings
Make sure the Rejected status is not considered since we create a appt with rejected status to generate invoice and master invoice
Make sure ID 1 developer.pennsyline@gmail.com staff user is present this helps generating generated and master invoices 

<a href="https://staging11.houston.pennsyline.com/signature?pen_action=bookly_approve_appointment&f_name={client_first_name}&l_name={client_last_name}&category={category_name}&type={location_name}&service={service_name}&token={appointment_token}&approve_url={approve_appointment_url}&appt_id={appointment_id}}&appointment_date={appointment_date}&appointment_time={appointment_time}" target="_blank" rel="noopener">Get Signature</a>

Testing scenarios

Pennsyline overview
test the date range and make sure all appointment pending vs comleted paid are synced properly. The revenue and total appointment are matching . TODO : developer to add signature pending status as well 
Should be able to print and export the analytics
This section can only be viewed by pennsyline admin drivers cannot see it at all
Test the graph and other button clicks to make sure all logic works
Provide a screenshot of final overview section

Arrival & Departures Appointments
Total 5 steps
Step 1 -> Make sure all required fields are added during appt creation, first time user have to always create from this flow to make sure all the upload fields and address are gathered properly
Step 1 -> Type of appointment is populated properly, categories are pulled properly, service and drivers are showing for all categories, change the service and see if all the drivers are having access to those services, sometime some drivers will be missing for some categories if so we have to see the services settings to ensure that all drivers are assinged to that service.
Step 1 -> Make sure the date pop up is visible when user clicks it, check all the button clicks make sure no loop holes or bugs are present, any driver is set if you want system to auto assign the appointment to drivers basing on their capacity and availability
Step 2 -> Time is shown in 5 mins apart basing on the service ( each service has some duration ) so each service you pick will show different time gaps ensure the time for each service is set properly ask the client to verify the duration of each service.
Step 2 -> Make sure the calender is working properly in terms or dates etc., go back and come forward to test if any functionality is breaking
Step 3 -> Details  Steps, this is onboarding of new client where we have to take information like email phone address passport details, international phone client File PO number and any notes on that user for future references
Step 3 -> If any case if you want to update a client information you can look up the customer auto fill and should be able to create a new appointment with updated information
Step 3 -> Make sure when you load existing customer, no new customer is created and customer appointments are not overridden or removed this is crictical testing scenario dont miss it
Step 3 -> Mandatory files are all the fields provided here, espically email address name and phone address and clinet number —> we will talk to client to ensure they provide these fields
Step 3 -> If the price of service is greater than 0 we will show Booking confirmation page which is background generate an invoice in payments seciton, if the price is set to 0 no invoice is generated this needs to be understood and if clients ask we need to let them know the scenario to generate invoice after appointment is created
Step 3 -> Load user is loading all customer information we have, verify it by going to customers section and make the user profile data is loaded in all the forms, attachments you can ignore we will work in future to retreive uploads but its not mandatory as of now.
Step 3 -> Clear form clears the form and make sure when a new user information is entered the old loaded user information is not utlizied in profile creation, unique identifier is the email address if we try to create a new user with existing user email it will be update the existing customer info, verify this logic since clients can make a mistake and we don’t want them to find any issues
Step 4 -> I confirm step should be shown always if not shown the service user is added price is set to zero, this cannot happen since we have to generate an invoice and get payments later
Step 5 -> Done step. Email is trigerred to all staff and create appointments calendar should be updated with all the details, if new customer a new customer is created, a new payment entry is created in invoice section , if exisiting user information is updated. Verify other possible combinations
Status —> the appointment status is set to signature pending, the payment invoice status is set to pending, once payment is done we mark the payment as complete, the appointment status is set to signature recieved once the driver click the link in email or calendar app and take a signature from the customer, we track the status of each appointment and payment to determine which are done completely and which requires additional information.
Vefiry the appointment in calendar make sure all the information date service provider location, status of appointment as signature pending are populated correctly
there is section call created appointemnts which will have more detailed information about the appointments created verify all the information is captured the payment will show 0 of $amount once we get payment done after invoice generation this will mark as complete with $amount paid
Go to customers section and see the field total number of appointments it should be updated to +1 with this new appointment created.
…. add more test cases missing down here

Create Appointment - Calendar view
Each driver will have his appointment filtered when u click on each driver the calendars should filter make sure the calendar is sync always with new appointments created, if an appointment is not shown here it means the appointment didn’t get created so keep an eye.
More filter are avaialble use the filters and verify the filters are working
Filter All staff if we remove some drivers the UI should reflect only the driver selected in All staff dropdown, similar with locations and services.
Make sure the Month week day list view are showing properly
We control the content of calendar popup if any information needs to be added we can add it, make sure correct information is populated
Edit appointment should auto fill information, if we change the appointment service provider location and date it should reflect, make sure send notificaitons is sent only when required.
Calendar should reflect in gmail calendar app for each driver, emails are sent to all drivers we are working on fix to send to only driver, but we will mainly utilize gmail calendar app to track all drivers appointments
Calendar in gmail will show the signature link, if a driver is missing signature link email the calendar notification to admin and get the link from there.





{#each participants as participant} Name : {participant.client_name} Email : {participant.client_email} Phone : {participant.client_phone} Location : {participant.client_address} Status: {participant.status} Appt Date : {appointment_date} Appt Notes/Signature Link : {participant.appointment_notes} Appt Time : {appointment_time} Category :{category_name} Service : {service_name} Driver : {staff_name} Approval Link : <a href="http://staging11.houston.pennsyline.com/signature?pen_action=bookly_approve_appointment&{participant.appointment_token}&f_name={participant.client_first_name}&l_name={participant.client_last_name}&category={category_name}&type={location_name}&service={service_name}&approve_url={participant.approve_appointment_url}&appointment_date={appointment_date}&appointment_time={appointment_time}" target="_blank" rel="noopener">Get Signature</a> {/each}




<script type="text/javascript"> console.log('form code'); jQuery(document).ready(function(){ console.log('form code'); // Read a page's GET URL variables and return them as an associative array. function getUrlVars() { var vars = [], hash; var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&'); for(var i = 0; i < hashes.length; i++) { hash = hashes[i].split('='); vars.push(hash[0]); vars[hash[0]] = hash[1]; } return vars; } jQuery(document).on('gform_confirmation_loaded', function(event, formId){ if(formId == 1) { console.log('form code'); var actionName = getUrlVars()["pen_action"]; var appt_id = getUrlVars()["appt_id"]; var pdfUrl = $('#gform_confirmation_message_1 code').html(); var ActionUrl = window.location.origin + '/wp-admin/admin-ajax.php?action=' + actionName + '&pdfUrl=' + pdfUrl + '&appt_id=' + appt_id; console.log(ActionUrl); $.ajax({url: ActionUrl, type:'GET',success:function(resp){ console.log(resp); }}); //location.href = approve_url + '&' + token; } }); }) </script>
	


Appreacence theme css 

Date oct 30th after gravity form signature addon jquery fix not loading in safari and response category fields in gforms


======


.bookly-details-step{
	overflow: scroll!important;
    height: 600px!important;
}
#input_1_10, #input_1_10_toolbar {
    width: 524px!important;
}
.aio_form{
	max-width: 524px!important;
}
#aio_time_clock{
	width: 100%!important;
	padding: 0!important;
}
.extra-widget-price{
	display: none!important;
	font-size:1px;
}
.eael-tabs-nav{
	background: #f1f1f1;
}
.r-content {
    overflow: hidden;
    float: left;
    width: auto;
	background-color: #fff;
}

.r-content .bk-form-header {
    height: 40px;
    background-color: #150734;
    border-radius: 16px 16px 0px 0px;
    margin: 0px;
	text-align: right;
}
.r-content .bk-form-header div {
    width: 13px;
    height: 13px;
    background-color: #fff;
    margin: 14px 0 0 5px;
    border-radius: 7px;
    display: inline-block;
}
.r-content .bk-form-header div:last-child {
    margin-right: 18px;
}
.r-content .bk-form-body {
    padding: 90px 23px 40px;
    border: solid 1px #d5d2dc;
    margin: -40px 0px 0px;
    box-shadow: 0px 20px 25px 0px #f0f0f0;
    border-radius: 16px;
}

.picker__header,.picker__box{
	background: none!important;
}

.ginput_container_radio #input_1_9{
	display: block;
	justify-content: space-between;
}
@media (min-width: 768px) {
    .ginput_container_radio #input_1_9{
	display: flex!important;
	justify-content: space-between;
}
}
.gform_wrapper .gfield_radio li label{
	margin: 2px 0 0 10px!important;
}
.gform_wrapper .gfield_time_hour i{
	display: none;
}
.gform_wrapper select{
	line-height: 1.4;
}

.gv-widgets-header{
	display: flex!important;
	align-items: center;
}
.gv-widgets-header .gv-grid-col-1-2{
	display: none!important;
}
.gv-widget-search{
	display: inline-flex!important;
	flex-flow: wrap!important;
	width: inherit!important;
	align-items: center;
}
.gv-widget-search .gv-search-box{
	min-width: auto!important;
}
.gv-widget-search .gv-search-box.gv-search-box-submit{
	display: block!important;
	flex: auto!important;
	text-align: right;
	margin-top: 20px;
}

.gv-widget-search .gv-search-box.gv-search-date{
	display: block!important;
}
.gv-widget-search .gv-search-box.gv-search-date input{
	margin-bottom: 0!important;
	    margin-bottom: 0!important;
}
.gv-widget-search .gv-search-box input, .gv-widget-search .gv-search-box select{
    display: block;
    height: calc(1.5em + .75rem + 2px);
    padding: .375rem .75rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

.gv-widget-page-size select{
    height: calc(1.5em + .75rem + 2px);
    padding: .375rem .75rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

.gv-widget-search .gv-search-box.gv-search-box-submit .gv-search-button{
	display: inline-block!important;
	color: #fff!important;
background-color: #28a745!important;
border-color: #28a745!important;
	font-size: 1.5rem;
    font-weight: bold;
		width: 100%;
	margin-top: 10px;
}

.gv-search-clear{
	color: #fff!important;
    background-color: #28a745!important;
    border-color: #28a745!important;
    font-size: 1.5rem;
    width: 100%;
    padding: 0 20px;
}
.gv-is-search .gv-search-clear{
	margin: 0!important;
}

.gv-container-2159 .gv-table-view tfoot{
	display: none;
}
.gv-widgets-footer{
	border-top: 1px solid #dee2e6!important;
	    padding-top: 10px;
}
.gv-container-2159 .gv-table-view{
	clear: both;
	margin-top: 6px !important;
	margin-bottom: 6px !important;
	max-width: none !important;
	border-collapse: separate !important;
	border-spacing: 0;
	color: #212529;
	border-top: 1px solid #dee2e6;
}
.gv-container-2159 .gv-table-view td, .gv-container-2159 .gv-table-view th {
    padding: .75rem;
    padding-top: 0.75rem;
    padding-right: 0.75rem;
    padding-bottom: 0.75rem;
    padding-left: 0.75rem;
    vertical-align: top;
    border-top: 1px solid #dee2e6;
}
.gv-container-2159 .gv-table-view tbody tr:nth-of-type(odd){
	background-color: rgba(0,0,0,.05)!important;
}

.gv-container-2159 .gv-table-view thead th {
    vertical-align: bottom;
    border-bottom: 2px solid #dee2e6;
}

/** Start Block Kit CSS: 142-3-a175df65179b9ef6a5ca9f1b2c0202b9 **/

.envato-block__preview{
	overflow: visible;
}

/* Border Radius */
.envato-kit-139-accordion .elementor-widget-container{
	border-radius: 10px !important;
}
.envato-kit-139-map iframe,
.envato-kit-139-slider .slick-slide,
.envato-kit-139-flipbox .elementor-flip-box div{
		border-radius: 10px !important;

}


/** End Block Kit CSS: 142-3-a175df65179b9ef6a5ca9f1b2c0202b9 **/



/** Start Block Kit CSS: 143-3-7969bb877702491bc5ca272e536ada9d **/

.envato-block__preview{overflow: visible;}
/* Material Button Click Effect */
.envato-kit-140-material-hit .menu-item a,
.envato-kit-140-material-button .elementor-button{
  background-position: center;
  transition: background 0.8s;
}
.envato-kit-140-material-hit .menu-item a:hover,
.envato-kit-140-material-button .elementor-button:hover{
  background: radial-gradient(circle, transparent 1%, #fff 1%) center/15000%;
}
.envato-kit-140-material-hit .menu-item a:active,
.envato-kit-140-material-button .elementor-button:active{
  background-color: #FFF;
  background-size: 100%;
  transition: background 0s;
}

/* Field Shadow */
.envato-kit-140-big-shadow-form .elementor-field-textual{
	box-shadow: 0 20px 30px rgba(0,0,0, .05);
}

/* FAQ */
.envato-kit-140-faq .elementor-accordion .elementor-accordion-item{
	border-width: 0 0 1px !important;
}

/* Scrollable Columns */
.envato-kit-140-scrollable{
	 height: 100%;
   overflow: auto;
   overflow-x: hidden;
}

/* ImageBox: No Space */
.envato-kit-140-imagebox-nospace:hover{
	transform: scale(1.1);
	transition: all 0.3s;
}
.envato-kit-140-imagebox-nospace figure{
	line-height: 0;
}

.envato-kit-140-slide .elementor-slide-content{
	background: #FFF;
	margin-left: -60px;
	padding: 1em;
}
.envato-kit-140-carousel .slick-active:not(.slick-current)  img{
	padding: 20px !important;
	transition: all .9s;
}

/** End Block Kit CSS: 143-3-7969bb877702491bc5ca272e536ada9d **/

.a2a_hide{display:none}.a2a_logo_color{background-color:#0166ff}.a2a_menu,.a2a_menu *{-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box;float:none;margin:0;padding:0;position:static;height:auto;width:auto}.a2a_menu{border-radius:6px;display:none;direction:ltr;background:#FFF;font:16px sans-serif-light,HelveticaNeue-Light,"Helvetica Neue Light","Helvetica Neue",Arial,Helvetica,"Liberation Sans",sans-serif;color:#000;line-height:12px;border:1px solid #CCC;vertical-align:baseline;overflow:hidden}.a2a_mini{min-width:200px;position:absolute;width:300px;z-index:9999997}.a2a_overlay{display:none;background:#616c7d;opacity:.92;backdrop-filter:blur(8px);position:fixed;top:0;right:0;left:0;bottom:0;z-index:9999998;-webkit-tap-highlight-color:transparent;transition:opacity .14s,backdrop-filter .14s}.a2a_full{background:#FFF;border:1px solid #FFF;height:auto;height:calc(320px);top:15%;left:50%;margin-left:-320px;position:fixed;text-align:center;width:640px;z-index:9999999;transition:transform .14s,opacity .14s}.a2a_full_footer,.a2a_full_header,.a2a_full_services{border:0;margin:0;padding:12px;box-sizing:border-box}.a2a_full_header{padding-bottom:8px}.a2a_full_services{height:280px;overflow-y:scroll;padding:0 12px;-webkit-overflow-scrolling:touch}.a2a_full_services .a2a_i{display:inline-block;float:none;width:181px;width:calc(33.334% - 18px)}div.a2a_full_footer{font-size:12px;text-align:center;padding:8px 14px}div.a2a_full_footer a,div.a2a_full_footer a:visited{display:inline;font-size:12px;line-height:14px;padding:8px 14px}div.a2a_full_footer a:focus,div.a2a_full_footer a:hover{background:0 0;border:0;color:#0166FF}div.a2a_full_footer a span.a2a_s_a2a,div.a2a_full_footer a span.a2a_w_a2a{background-size:14px;border-radius:3px;display:inline-block;height:14px;line-height:14px;margin:0 3px 0 0;vertical-align:top;width:14px}.a2a_modal{height:0;left:50%;margin-left:-320px;position:fixed;text-align:center;top:15%;width:640px;z-index:9999999;transition:transform .14s,opacity .14s;-webkit-tap-highlight-color:transparent}.a2a_modal_body{background:0 0;border:0;font:24px sans-serif-light,HelveticaNeue-Light,"Helvetica Neue Light","Helvetica Neue",Arial,Helvetica,"Liberation Sans",sans-serif;position:relative;height:auto;width:auto}.a2a_thanks{color:#fff;height:auto;margin-top:20px;width:auto}.a2a_thanks>div:first-child{margin:0 0 40px 0}.a2a_thanks div *{height:inherit}#a2a_copy_link{background:#FFF;border:1px solid #FFF;margin-top:15%}span.a2a_s_link#a2a_copy_link_icon,span.a2a_w_link#a2a_copy_link_icon{background-size:48px;border-radius:0;display:inline-block;height:48px;left:0;line-height:48px;margin:0 3px 0 0;position:absolute;vertical-align:top;width:48px}#a2a_modal input#a2a_copy_link_text{background-color:transparent;border:0;color:#2A2A2A;font:inherit;height:48px;left:62px;max-width:initial;padding:0;position:relative;width:564px;width:calc(100% - 76px)}#a2a_copy_link_copied{background-color:#0166ff;color:#fff;display:none;font:inherit;font-size:16px;margin-top:1px;padding:3px 8px}@media (prefers-color-scheme:dark){.a2a_menu a,.a2a_menu a.a2a_i,.a2a_menu a.a2a_i:visited,.a2a_menu a.a2a_more,i.a2a_i{border-color:#2a2a2a!important;color:#fff!important}.a2a_menu a.a2a_i:active,.a2a_menu a.a2a_i:focus,.a2a_menu a.a2a_i:hover,.a2a_menu a.a2a_more:active,.a2a_menu a.a2a_more:focus,.a2a_menu a.a2a_more:hover,.a2a_menu_find_container{border-color:#444!important;background-color:#444!important}.a2a_menu{background-color:#2a2a2a;border-color:#2a2a2a}.a2a_menu_find{color:#fff!important}.a2a_menu span.a2a_s_find svg{background-color:transparent!important}.a2a_menu span.a2a_s_find svg path{fill:#fff!important}}@media print{.a2a_floating_style,.a2a_menu,.a2a_overlay{visibility:hidden}}@keyframes a2aFadeIn{from{opacity:0}to{opacity:1}}.a2a_starting{opacity:0}.a2a_starting.a2a_full,.a2a_starting.a2a_modal{transform:scale(.8)}@media (max-width:639px){.a2a_full{border-radius:0;top:15%;left:0;margin-left:auto;width:100%}.a2a_modal{left:0;margin-left:10px;width:calc(100% - 20px)}}@media (min-width:318px) and (max-width:437px){.a2a_full .a2a_full_services .a2a_i{width:calc(50% - 18px)}}@media (max-width:317px){.a2a_full .a2a_full_services .a2a_i{width:calc(100% - 18px)}}@media (max-height:436px){.a2a_full{bottom:40px;height:auto;top:40px}}@media (max-height:550px){.a2a_modal{top:30px}}@media (max-height:360px){.a2a_modal{top:20px}.a2a_thanks>div:first-child{margin-bottom:20px}}.a2a_menu a{color:#0166FF;text-decoration:none;font:16px sans-serif-light,HelveticaNeue-Light,"Helvetica Neue Light","Helvetica Neue",Arial,Helvetica,"Liberation Sans",sans-serif;line-height:14px;height:auto;width:auto;outline:0}.a2a_menu a.a2a_i:visited,.a2a_menu a.a2a_more{color:#0166FF}.a2a_menu a.a2a_i:active,.a2a_menu a.a2a_i:focus,.a2a_menu a.a2a_i:hover,.a2a_menu a.a2a_more:active,.a2a_menu a.a2a_more:focus,.a2a_menu a.a2a_more:hover{color:#2A2A2A;border-color:#EEE;border-style:solid;background-color:#EEE;text-decoration:none}.a2a_menu span.a2a_s_find{background-size:24px;height:24px;left:8px;position:absolute;top:7px;width:24px}.a2a_menu span.a2a_s_find svg{background-color:#FFF}.a2a_menu span.a2a_s_find svg path{fill:#CCC}#a2a_menu_container{display:inline-block}.a2a_menu_find_container{border:1px solid #CCC;border-radius:6px;padding:2px 24px 2px 0;position:relative;text-align:left}.a2a_cols_container .a2a_col1{overflow-x:hidden;overflow-y:auto;-webkit-overflow-scrolling:touch}#a2a_modal input,#a2a_modal input[type=text],.a2a_menu input,.a2a_menu input[type=text]{display:block;background-image:none;box-shadow:none;line-height:100%;margin:0;outline:0;overflow:hidden;padding:0;-moz-box-shadow:none;-webkit-box-shadow:none;-webkit-appearance:none}#a2afeed_find_container input,#a2afeed_find_container input[type=text],#a2apage_find_container input,#a2apage_find_container input[type=text]{background-color:transparent;border:0;box-sizing:content-box;color:#2A2A2A;font:inherit;font-size:16px;height:28px;line-height:20px;left:38px;outline:0;margin:0;max-width:initial;padding:2px 0;position:relative;width:99%}.a2a_clear{clear:both}.a2a_svg{background-repeat:no-repeat;display:block;overflow:hidden;height:32px;line-height:32px;padding:0;width:32px}.a2a_svg svg{background-repeat:no-repeat;background-position:50% 50%;border:none;display:block;left:0;margin:0 auto;overflow:hidden;padding:0;position:relative;top:0;width:auto;height:auto}a.a2a_i,i.a2a_i{display:block;float:left;border:1px solid #FFF;line-height:24px;padding:6px 8px;text-align:left;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;width:132px}a.a2a_i span,a.a2a_more span{display:inline-block;overflow:hidden;vertical-align:top}a.a2a_i .a2a_svg{margin:0 6px 0 0}a.a2a_i .a2a_svg,a.a2a_more .a2a_svg{background-size:24px;height:24px;line-height:24px;width:24px}a.a2a_sss:hover{border-left:1px solid #CCC}a.a2a_menu_show_more_less{border-bottom:1px solid #FFF;border-left:0;border-right:0;line-height:24px;margin:6px 0 0;padding:6px;-webkit-touch-callout:none}a.a2a_menu_show_more_less span{height:24px;margin:0 6px 0 0}.a2a_kit .a2a_svg{background-repeat:repeat}.a2a_default_style a{float:left;line-height:16px;padding:0 2px}.a2a_default_style a:hover .a2a_svg,.a2a_floating_style a:hover .a2a_svg,.a2a_overlay_style a:hover .a2a_svg svg{opacity:.7}.a2a_overlay_style.a2a_default_style a:hover .a2a_svg{opacity:1}.a2a_default_style .a2a_count,.a2a_default_style .a2a_svg,.a2a_floating_style .a2a_svg,.a2a_menu .a2a_svg,.a2a_vertical_style .a2a_count,.a2a_vertical_style .a2a_svg{border-radius:4px}.a2a_default_style .a2a_counter img,.a2a_default_style .a2a_dd,.a2a_default_style .a2a_svg{float:left}.a2a_default_style .a2a_img_text{margin-right:4px}.a2a_default_style .a2a_divider{border-left:1px solid #000;display:inline;float:left;height:16px;line-height:16px;margin:0 5px}.a2a_kit a{cursor:pointer}.a2a_floating_style{background-color:#fff;border-radius:6px;position:fixed;z-index:9999995}.a2a_overlay_style{z-index:2147483647}.a2a_floating_style,.a2a_overlay_style{animation:a2aFadeIn .2s ease-in;padding:4px}.a2a_vertical_style a{clear:left;display:block;overflow:hidden;padding:4px;text-decoration:none}.a2a_floating_style.a2a_default_style{bottom:0}.a2a_floating_style.a2a_default_style a,.a2a_overlay_style.a2a_default_style a{padding:4px}.a2a_count{background-color:#fff;border:1px solid #ccc;box-sizing:border-box;color:#2a2a2a;display:block;float:left;font:12px Arial,Helvetica,sans-serif;height:16px;margin-left:4px;position:relative;text-align:center;width:50px}.a2a_count:after,.a2a_count:before{border:solid transparent;border-width:4px 4px 4px 0;content:"";height:0;left:0;line-height:0;margin:-4px 0 0 -4px;position:absolute;top:50%;width:0}.a2a_count:before{border-right-color:#ccc}.a2a_count:after{border-right-color:#fff;margin-left:-3px}.a2a_count span{animation:a2aFadeIn .14s ease-in}.a2a_vertical_style .a2a_counter img{display:block}.a2a_vertical_style .a2a_count{float:none;margin-left:0;margin-top:6px}.a2a_vertical_style .a2a_count:after,.a2a_vertical_style .a2a_count:before{border:solid transparent;border-width:0 4px 4px 4px;content:"";height:0;left:50%;line-height:0;margin:-4px 0 0 -4px;position:absolute;top:0;width:0}.a2a_vertical_style .a2a_count:before{border-bottom-color:#ccc}.a2a_vertical_style .a2a_count:after{border-bottom-color:#fff;margin-top:-3px}.a2a_nowrap{white-space:nowrap}.a2a_note{margin:0 auto;padding:9px;font-size:12px;text-align:center}.a2a_note .a2a_note_note{margin:0;color:#2A2A2A}.a2a_wide a{display:block;margin-top:3px;border-top:1px solid #EEE;text-align:center}.a2a_label{position:absolute!important;clip-path:polygon(0 0,0 0,0 0);-webkit-clip-path:polygon(0 0,0 0,0 0);overflow:hidden;height:1px;width:1px}.a2a_kit,.a2a_menu,.a2a_modal,.a2a_overlay{-ms-touch-action:manipulation;touch-action:manipulation;outline:0}.a2a_dd img{border:0}.a2a_button_facebook_like iframe{max-width:none}

/** Start Block Kit CSS: 136-3-fc37602abad173a9d9d95d89bbe6bb80 **/

.envato-block__preview{overflow: visible !important;}

/** End Block Kit CSS: 136-3-fc37602abad173a9d9d95d89bbe6bb80 **/

#aquilaAdminbarIcon{
	display: none!important;
}
=====



