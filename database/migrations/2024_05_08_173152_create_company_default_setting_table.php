<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('companydefaultsetting', function (Blueprint $table) {
            $table->id();
            $table->string('option', 255);
            $table->text('value');
            $table->timestamps();
        });

        // Valores predeterminados para poblar la tabla
        $defaultInvoiceEmailBody = 'You have received a new invoice from <b>{COMPANY_NAME}</b>.</br>Please download using the button below:';
        $defaultEstimateEmailBody = 'You have received a new estimate from <b>{COMPANY_NAME}</b>.</br>Please download using the button below:';
        $defaultPaymentEmailBody = 'Thank you for the payment.</b></br>Please download your payment receipt using the button below:';
        $billingAddressFormat = '<h3>{BILLING_ADDRESS_NAME}</h3><p>{BILLING_ADDRESS_STREET_1}</p><p>{BILLING_ADDRESS_STREET_2}</p><p>{BILLING_CITY}  {BILLING_STATE}</p><p>{BILLING_COUNTRY}  {BILLING_ZIP_CODE}</p><p>{BILLING_PHONE}</p>';
        $shippingAddressFormat = '<h3>{SHIPPING_ADDRESS_NAME}</h3><p>{SHIPPING_ADDRESS_STREET_1}</p><p>{SHIPPING_ADDRESS_STREET_2}</p><p>{SHIPPING_CITY}  {SHIPPING_STATE}</p><p>{SHIPPING_COUNTRY}  {SHIPPING_ZIP_CODE}</p><p>{SHIPPING_PHONE}</p>';
        $companyAddressFormat = '<h3><strong>{COMPANY_NAME}</strong></h3><p>{COMPANY_ADDRESS_STREET_1}</p><p>{COMPANY_ADDRESS_STREET_2}</p><p>{COMPANY_CITY} {COMPANY_STATE}</p><p>{COMPANY_COUNTRY}  {COMPANY_ZIP_CODE}</p><p>{COMPANY_PHONE}</p>';
        $paymentFromCustomerAddress = '<h3>{BILLING_ADDRESS_NAME}</h3><p>{BILLING_ADDRESS_STREET_1}</p><p>{BILLING_ADDRESS_STREET_2}</p><p>{BILLING_CITY} {BILLING_STATE} {BILLING_ZIP_CODE}</p><p>{BILLING_COUNTRY}</p><p>{BILLING_PHONE}</p>';
        $pbxReactivationServiceEmailBody = '<p>Hi {PRIMARY_CONTACT_NAME},</p><p>Your service, {SERVICE_PBX_SERVICES_NUMBER} , has been reactivated.</p>';

        $creationServiceEmailBody = '<p>Hi {PRIMARY_CONTACT_NAME},<br><br>Your service has been approved and activated. Please keep this email for your records.<br><br></p><p></p>{COMPANY_NAME}';
        $suspensionServiceEmailBody = '<p>Hi {PRIMARY_CONTACT_NAME},</p><p>Your service, {SERVICES_NUMBER} has been suspended. The service may have been suspended for the following reasons:</p><ol><li><p>Non-payment. If your service was suspended for non-payment, you may login at&nbsp;<a href="http://{client_uri}" rel="noopener noreferrer nofollow">http://careonecomm.com/my-account/</a>&nbsp;to post payment and re-activate the service.</p></li><li><p>TOS or abuse violation.</p></li></ol><p>If the service is suspended for an extended period of time, it may be cancelled. Please contact us if you have any questions.</p><p></p>';
        $cancellationServiceEmailBody = '<p>Hi {PRIMARY_CONTACT_NAME},</p><p>Your service, {SERVICES_NUMBER} , has been canceled.</p>';
        $reactivationServiceEmailBody = '<p>Hi {PRIMARY_CONTACT_NAME},</p><p>Your service, {SERVICES_NUMBER} , has been reactivated.</p>';

        // Nuevos valores
        $invoiceNoticeOne = '<p>Hi , {CONTACT_DISPLAY_NAME}<br><br>This is a reminder that invoice <strong># {INVOICE_NUMBER}</strong> is DUE UPON RECEIPT. If you have recently mailed in payment for this invoice, you can ignore this reminder.</p><p>Please click on the link below to make a payment.<br><br>Thank you for your continued business!</p><h1><strong>{COMPANY_NAME}</strong></h1><p>{COMPANY_ADDRESS_STREET_1}</p><p>{COMPANY_CITY} {COMPANY_STATE} {COMPANY_ZIP_CODE}</p><p>{COMPANY_PHONE}</p>';
        $invoiceNoticeTwo = '<p>Hi , {CONTACT_DISPLAY_NAME}<br><br>This is the <strong>2nd notice</strong> we have sent regarding invoice <strong>#</strong>{INVOICE_NUMBER}. Is now past due. If you have recently mailed in payment for this invoice, you can ignore this email.<br><br><a href=\"http://{payment_url}\" rel=\"noopener noreferrer nofollow\">Pay Now</a> (Click on the link below)</p><h1><strong>{COMPANY_NAME}</strong></h1><p>{COMPANY_ADDRESS_STREET_1}</p><p>{COMPANY_CITY} {COMPANY_STATE} {COMPANY_ZIP_CODE}</p><p>{COMPANY_PHONE}</p><p></p>';
        $invoiceNoticeThree = '<p>Hi {CONTACT_DISPLAY_NAME},<br><br>This is the <strong>3rd notice</strong> we have sent regarding invoice <strong>#</strong>{INVOICE_NUMBER} and is now past due. This is the last notice we will send regarding this particular invoice. If payment is not received with in the next 10 days, the account may be suspended.</p><p></p><h1><strong>{COMPANY_NAME}</strong></h1><p>{COMPANY_ADDRESS_STREET_1}</p><p>{COMPANY_CITY} {COMPANY_STATE} {COMPANY_ZIP_CODE}</p><p>{COMPANY_PHONE}</p>';
        $invoiceNoticeUnpaid = '<p>Hi , {CONTACT_DISPLAY_NAME}<br><br>This is a reminder that invoice <strong># {INVOICE_NUMBER}</strong> is DUE UPON RECEIPT. If you have recently mailed in payment for this invoice, you can ignore this reminder.</p><p>Please click on the link below to make a payment.<br><br>Thank you for your continued business!</p><h1><strong>{COMPANY_NAME}</strong></h1><p>{COMPANY_ADDRESS_STREET_1}</p><p>{COMPANY_CITY} {COMPANY_STATE} {COMPANY_ZIP_CODE}</p><p>{COMPANY_PHONE}</p>';
        $ticketCreatioCustomer = '<p>Hello {CONTACT_DISPLAY_NAME},</p><p>We have received your request and someone will be looking at it shortly.</p><p></p><p>{TICKECT_DETAILS}</p><p></p><p>Thank you</p><p></p>';
        $ticketCreatioUser = '<p>A new ticket has been received with the following details:</p><p>{TICKECT_PRIORITY}</p><p>{TICKECT_DEPARTAMENT}</p><p>{TICKECT_ASSIGNED_TO}</p><p>{TICKECT_DETAILS}</p><p>{TICKECT_STATUS}</p><p></p><p></p>';
        $ticketUpdateCustomer = '<p>Hello{CONTACT_DISPLAY_NAME},</p><p></p><p>Your request is currently being worked on by our team and we will get back to you as soon as we have an update.</p><p>{TICKECT_STATUS}</p><p>{TICKECT_DETAILS}</p><p>Thank you for your patience.</p><p></p><p></p><p></p><p></p><p></p>' <
        $ticketUpdateUser = '<p>The following ticket has been updated:</p><p>{TICKECT_DEPARTAMENT}</p><p>{TICKECT_ASSIGNED_TO}</p><p>{TICKECT_PRIORITY}</p><p>{TICKECT_STATUS}</p><p>{TICKECT_DETAILS}</p>';
        $paymentApprovedAch = '<p>Hi <strong>{PRIMARY_CONTACT_NAME}</strong>,<br>We have submitted a debit request to your bank for <strong>{PAYMENT_AMOUNT}</strong>, Transaction Number <strong>{TRANSACTION}</strong>.</p><p>Please verify that the account details we have on file are correct. We will attempt this request again once a day until it is successful, up to three times.</p><p><strong>Please remember to update your payment information so we can proceed with the debit request</strong></p><p></p><p>Thank you for your business!</p>';
        $paymentAchDeclined = '<p>Hi <strong>{PRIMARY_CONTACT_NAME}</strong>,<br>We submitted a debit request to your bank for the amount of <strong>{PAYMENT_AMOUNT}</strong>, but the request was declined.<br><strong>Please verify that the account details we have on file are correct.</strong><br><br>We will attempt this request again once a day until it is successful, up to three times.</p>';
        $paymentApprovedCreditCard = '<p>Hi <strong>{PRIMARY_CONTACT_NAME}</strong>,<br>We have successfully processed payment with your <strong>{CREDIT_CARD}</strong>, ending in <strong>{CARD_NUMBER}</strong>. Please keep this email as a receipt for your records.</p><p><br>Amount: <strong>{PAYMENT_AMOUNT}</strong><br>Transaction Number: <strong>{TRANSACTION}</strong></p><p><br>The charge will be listed as being from {COMPANY_NAME} on your credit card statement.<br>Thank you for your business!</p>';
        $paymentCreditCardRejected = '<p>Hi <strong>{PRIMARY_CONTACT_NAME}</strong>,<br><br>We tried to charge your <strong>{CREDIT_CARD}</strong>, ending in <strong>{CARD_NUMBER}</strong> the amount of <strong>{PAYMENT_AMOUNT}</strong>, but it was declined.</p><p><strong>Please verify that the account details we have on file are correct.</strong><br>We will attempt to charge your card again once a day until it is successful, up to three times.</p>';
        $customerAccountRegistration = '<p><strong>HELLO {CONTACT_DISPLAY_NAME} Welcome&nbsp;to {COMPANY_NAME}!</strong></p><p></p><p>Thank you for choosing <strong>{COMPANY_NAME}</strong>. The activation of your account&nbsp;is now complete.</p><p>Your new account&nbsp;login details below. Please login and change your password at your earliest convenience.<br><br>Username: {CONTACT_EMAIL}<br>Password: {CONTACT_PASSWORD}</p><p><br>If you placed an order for services, you\'ll receive a separate email once they are activated.<br><br>Thank you for choosing <strong>{COMPANY_NAME}</strong>!</p>';
        $customerPasswordReset = '<p>Hi <strong>{CONTACT_DISPLAY_NAME}</strong>,<br><br>You have requested a password reset for your account. If this was you, please visit the following URL to reset your password..<br><br><a href=\"http://{password_reset_url}\" rel=\"noopener noreferrer nofollow\">http://{password_reset_url}</a><br><br>If you did not submit this request, please contact your system administrator. No action is required to keep your password the same.</p>';
        $customerEmailVerification = '<p>Hi <strong>{CONTACT_DISPLAY_NAME}</strong></p><p>Some features of your account may be restricted, or orders may be held, until your email address is verified. To verify your email address, please click the link below or copy and paste it into your browser.</p>';
        $customerForgettingUsername = '<p>Hi <strong>{CONTACT_DISPLAY_NAME}</strong>,</p><p>You have requested the username for your account.<br>The username for your account is: <strong>{CONTACT_USERNAME}</strong></p><p>If you did not make this request, you can safely ignore this email.</p>';
        $pbxCreationServices = '<p>Hi <strong>{PRIMARY_CONTACT_NAME}</strong>,<br><br>Your service PBX <strong>{SERVICE_PBX_SERVICES_NUMBER}</strong> has been approved and activated. Please keep this email for your records.</p>';
        $pbxSuspensionServices = '<p>Hi <strong>{PRIMARY_CONTACT_NAME}</strong>,</p><p>Your service, <strong>{SERVICE_PBX_SERVICES_NUMBER}</strong> has been suspended. The service may have been suspended for the following reasons:</p><ol><li><p>Non-payment. If your service was suspended for non-payment, you may login at&nbsp;<strong>https://cbdev.corebill.co/login</strong>&nbsp;to post payment and re-activate the service.</p></li><li><p>TOS or abuse violation.</p></li></ol><p>If the service is suspended for an extended period of time, it may be cancelled. Please contact us if you have any questions.</p>';
        $pbxCancellationServices = '<p>Hi <strong>{PRIMARY_CONTACT_NAME}</strong>,</p><p>Your service, <strong>{SERVICE_PBX_SERVICES_NUMBER}</strong> , has been canceled.</p>';
        $paymentCardExpirationReminders = '<p>Hi <strong>{CONTACT_DISPLAY_NAME}</strong>,</p><p>Your <strong>{CREDIT_CARD}</strong> ending in <strong>{CARD_NUMBER}</strong> is expiring this month.</p><p></p><p>To update your card, please login into our client area at <code>https://cbdev.corebill.co/login. </code>If the card is not updated, we\'ll be unable to process payment for any current or future charges</p><p></p><p>Thank you!</p>';
        $customerEmailNotification = '<p><strong>Dear {CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION}</strong></p><p>Your prepaid account has reached a critically low balance of $<strong>{CONTACT_BALANCE}</strong>. When your account reaches $0.00, your termination services will be suspended. To avoid future service interruptions please login to your BackOffice at&nbsp;<code>https://cbdev.corebill.co/login</code>&nbsp;and submit a one-time or recurring payment.</p><p>If you have any questions or would like to add, change, or remove email notification recipients, please submit a billing ticket through your BackOffice.</p><p></p><p>Sincerely,</p><p></p>';
        $customerCustomerCreation = '<p><strong>Hi {CONTACT_DISPLAY_NAME} Welcome&nbsp;to {COMPANY_NAME}!</strong><br>Thank you for choosing <strong>{COMPANY_NAME}</strong>. The activation of your account&nbsp;is now complete.<br>If you placed an order for services, you\'ll receive a separate email once they are activated.<br><br>Thank you for choosing <strong>{COMPANY_NAME}</strong>!</p>';
        $invoiceFooter = '<p>Terms</p><p>At Care One Communications we are committed to excellent costumer service and support.</p><p>Please pay your invoice before the due date to avoid any late charges. Mail all payments or correspondence to</p><p>the address below. If you need support Care One Communications support team is available via E-Mail at Support@CareOneComm.com .Any payments received 15 days</p><p>past the due date will be subject to a 10% penalty.</p><p>Costumer Service# 1469-250-0260</p>';
        $estimateSubject = '<p>This quote is subject to stock availability.</p>';
        $pbxServerEmailbody = '<p>Hello,</p><p><strong>We just detected an incident on your PBX. It seems your service is currently&nbsp;down from {HOUR_DOWN}. </strong>As long as the incident is active, Corebill will be impacted on your PBX services.</p><p></p><p>Server details:</p><h2>{SERVER_LABEL}</h2><p>Checked URL</p><h2>{HOST_IP}</h2><p>Root cause</p><h2>Host Is Unreachable</h2><p></p>';
        $pbxServerEmailbodyUp = '<p>Hello, {COMPANY_NAME}</p><p><strong>It seems like the accident on your PBX was solve at {HOUR_UP}. It seems your service is currently&nbsp;up.';

        $settings = [
            'invoice_auto_generate' => 'YES',
            'payment_auto_generate' => 'YES',
            'estimate_auto_generate' => 'YES',
            'save_pdf_to_disk' => 'NO',
            'invoice_mail_body' => $defaultInvoiceEmailBody,
            'estimate_mail_body' => $defaultEstimateEmailBody,
            'payment_mail_body' => $defaultPaymentEmailBody,
            'invoice_company_address_format' => $companyAddressFormat,
            'invoice_shipping_address_format' => $shippingAddressFormat,
            'invoice_billing_address_format' => $billingAddressFormat,
            'estimate_company_address_format' => $companyAddressFormat,
            'estimate_shipping_address_format' => $shippingAddressFormat,
            'estimate_billing_address_format' => $billingAddressFormat,
            'payment_company_address_format' => $companyAddressFormat,
            'payment_from_customer_address_format' => $paymentFromCustomerAddress,
            'currency' => 1,
            'time_zone' => 'Asia/Kolkata',
            'language' => 'en',
            'fiscal_year' => '1-12',
            'carbon_date_format' => 'Y/m/d',
            'moment_date_format' => 'YYYY/MM/DD',
            'notification_email' => 'support@corebill.co',
            'notify_invoice_viewed' => 'NO',
            'notify_estimate_viewed' => 'NO',
            'tax_per_item' => 'NO',
            'discount_per_item' => 'NO',
            'invoice_auto_generate' => 'YES',
            'invoice_prefix' => 'INV',
            'creation_services' => $creationServiceEmailBody,
            'suspension_services' => $suspensionServiceEmailBody,
            'cancellation_services' => $cancellationServiceEmailBody,
            'reactivation_services' => $reactivationServiceEmailBody,
            'packages_prefix' => 'PACK',
            'estimate_prefix' => 'EST',
            'estimate_auto_generate' => 'YES',
            'payment_prefix' => 'PAY',
            'payment_auto_generate' => 'YES',
            'save_pdf_to_disk' => 'NO',
            'invoice_notices_settings_auto_debit_pending' => 0,
            'invoice_notices_settings_notice_3_type' => 'After',
            'invoice_notices_settings_notice_3' => 0,
            'invoice_notices_settings_notice_2_type' => 'After',
            'invoice_notices_settings_notice_2' => 2,
            'invoice_notices_settings_notice_1_type' => 'After',
            'invoice_notices_settings_notice_1' => 4,
            'invoice_notices_settings_send_payment' => 1,
            'invoice_notices_settings_send_cancellation' => 1,
            'period_time_run_send_invoice_job' => 15,
            'allow_send_invoice_job' => 1,
            'time_run_renewal_date_job_pbx' => '14:00',
            'pbx_reactivation_services' => $pbxReactivationServiceEmailBody,
            'allow_renewal_date_job_pbx' => 1,
            'time_run_renewal_date_job' => '09:57',
            'allow_renewal_date_job' => 1,
            'prov_prefix' => 'PROR',
            'item_prefix' => 'ITEM',
            'customer_prefix' => 'CUST',
            'did_pbx_prefix' => 'DIDR',
            'pbx_services_prefix' => 'PBXS',
            'service_prefix' => 'PAX',
            'extension_pbx_prefix' => 'PBXEE',
            'packages_pbx_prefix' => 'PBXP',
            'expense_prefix' => 'EXPT',
            'expense_prefix' => 'EXPT',
            'expense_auto_generate' => 'YES',
            'packages_prefix' => 'PACK',

            //Nuevos campos
            'invoice_number_length' => '6',
            'invoice_email_attachment' => 'NO',
            'estimate_number_length' => '6',
            'estimate_email_attachment' => 'NO',
            'payment_number_length' => '6',
            'payment_email_attachment' => 'NO',
            'invoice_notice_one' => $invoiceNoticeOne,
            'invoice_notice_two' => $invoiceNoticeTwo,
            'invoice_notice_three' => $invoiceNoticeThree,
            'invoice_notice_unpaid' => $invoiceNoticeUnpaid,
            'ticket_creatio_customer' => $ticketCreatioCustomer,
            'ticket_creatio_user' => $ticketCreatioUser,
            'ticket_update_customer' => $ticketUpdateCustomer,
            'ticket_update_user' => $ticketUpdateUser,
            'payment_approved_ach' => $paymentApprovedAch,
            'payment_ach_declined' => $paymentAchDeclined,
            'payment_approved_credit_card' => $paymentApprovedCreditCard,
            'payment_credit_card_rejected' => $paymentCreditCardRejected,
            'customer_account_registration' => $customerAccountRegistration,
            'customer_password_reset' => $customerPasswordReset,
            'customer_email_verification' => $customerEmailVerification,
            'customer_forgetting_username' => $customerForgettingUsername,
            'pbx_creation_services' => $pbxCreationServices,
            'pbx_suspension_services' => $pbxSuspensionServices,
            'pbx_cancellation_services' => $pbxCancellationServices,
            'allow_autodebit_customer_job' => '1',
            'time_run_autodebit_customer_job' => '17:17',
            'allow_reminder_payment_job' => '1',
            'time_run_reminder_payment_job' => '16:59',
            'allow_cardexpiration_payment_job' => '1',
            'time_run_cardexpiration_payment_job' => '16:47',
            'allow_suspension_pbx_job' => '1',
            'time_run_suspension_pbx_job' => '19:57',
            'allow_suspension_packages_job' => '1',
            'time_run_suspension_packages_job' => '10:00',
            'allow_unsuspend_packages_job' => '1',
            'period_run_unsuspend_packages_job' => '10',
            'time_unsuspend_packages_job' => '2022-11-11 14:34',
            'allow_unsuspend_pbx_job' => '1',
            'period_run_unsuspend_job' => '11',
            'time_unsuspend_pbx_job' => '2022-11-11 14:31',
            'payment_card_expiration_reminders' => $paymentCardExpirationReminders,
            'customer_email_notification' => $customerEmailNotification,
            'customer_customer_creation' => $customerCustomerCreation,
            'allow_pending_payment_job' => '1',
            'period_run_pending_payment_job' => '1000',
            'idle_time_logout' => '10',
            'time_invoices_draft_sent' => '2022-11-11 14:39',
            'header_color' => '#B29C95FF',
            'primary_color' => '#4E4E34FF',
            'retention_active' => 'NO',
            'color_invoice' => '#00BFFFFF',
            'footer_text_value' => 'Corebill a Care One Communications Solution',
            'footer_url_value' => 'https://careonecomm.com',
            'invoice_footer' => $invoiceFooter,
            'estimate_footer' => $estimateSubject,
            'payment_subject' => 'Payment test 4',
            'late_fee_hour' => '14:45',
            'invoice_late_fee_active_one' => '1',
            'invoice_late_fee_active_two' => '1',
            'invoice_late_fee_active_three' => '1',
            'invoice_late_fee_days_one' => '10',
            'invoice_late_fee_days_two' => '20',
            'invoice_late_fee_days_three' => '30',
            'invoice_late_fee_type_one_value' => '10',
            'invoice_late_fee_type_two_value' => '100',
            'invoice_late_fee_type_three_value' => '20',
            'invoice_late_fee_type_one' => 'percentage',
            'invoice_late_fee_type_two' => 'fixed',
            'invoice_late_fee_type_three' => 'percentage',
            'footer_url_name' => 'Corebill SA',
            'current_year' => '1',
            'server_notification' => 'frivero@careonecomm.com',
            'activate_notification' => '1',
            'server_subject' => 'PBX server down',
            'pbx_server_emailbody' => $pbxServerEmailbody,
            'pbx_server_emailbody_down' => $pbxServerEmailbody,
            'pbx_server_emailbody_up' => $pbxServerEmailbodyUp,
            'server_subject_down' => 'Oops something went wrong 🔥',
            'server_subject_up' => 'Everything is fine ☕',
            'send_email_deactive' => 'NO',
        ];

        // Insertar los valores predeterminados en la tabla
        foreach ($settings as $option => $value) {
            DB::table('companydefaultsetting')->insert([
                'option' => $option,
                'value' => $value,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('companydefaultsetting');
    }
};
