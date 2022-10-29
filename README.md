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
	
