<?php

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WC_Email_Vendor_New_Coupon_Added_To_Customer')) :

    /**
     * Customer New Account
     *
     * An email sent to the customer when they create an account.
     *
     * @class 		WC_Email_Vendor_New_Coupon_Added_To_Customer
     * @version		2.0.0
     * @package		WooCommerce/Classes/Emails
     * @author 		WooThemes
     * @extends 	WC_Email
     */
    class WC_Email_Vendor_New_Coupon_Added_To_Customer extends WC_Email {
        /**
         * Constructor
         *
         * @access public
         * @return void
         */
        function __construct() {
            global $MVX;

            $this->id = 'vendor_coupon_to_followed_customer';

            $this->title = __('Vendor New Coupon To Customer', 'multivendorx');
            $this->description = __('Notification emails are sent to followed customers when a new coupon is submitted by a vendor.', 'multivendorx');

            $this->heading = __('New coupon added: {coupon_name}', 'multivendorx');
            $this->subject = __('[{blogname}] New coupon added by your followed vendor {vendor_name} - {coupon_name}', 'multivendorx');

            $this->template_base = $MVX->plugin_path . 'templates/';
            $this->template_html = 'emails/new-coupon-to-followed-customer.php';
            $this->template_plain = 'emails/plain/new-coupon-to-followed-customer.php';

            // Call parent constructor
            parent::__construct();

        }

        /**
         * trigger function.
         *
         * @access public
         * @return void
         *
         * @param unknown $order_id
         */
        function trigger($post_id, $post, $vendor, $customer_email) {

            if (!$this->is_enabled())
                return;
            
            $this->object = $post;
            $this->find[] = '{coupon_name}';
            $this->coupon_name = $post->post_title;
            $this->replace[] = $this->coupon_name;

            $this->find[] = '{vendor_name}';
            $this->vendor_name = $vendor->page_title;
            $this->replace[] = $this->vendor_name;
            
            $this->post_id = $post->ID;

            $post_type = get_post_type($this->post_id);
            $this->post_type = $post_type;

            $this->recipient = $customer_email;

            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
        }
        
        /**
         * Get email subject.
         *
         * @access  public
         * @return string
         */
        public function get_default_subject() {
            return apply_filters('mvx_customer_new_vendor_coupon_email_subject', __('[{blogname}] New coupon added by your followed vendor {vendor_name} - {coupon_name}', 'multivendorx'), $this->object);
        }

        /**
         * Get email heading.
         *
         * @access  public
         * @return string
         */
        public function get_default_heading() {
            return apply_filters('mvx_customer_new_vendor_coupon_email_heading', __('New coupon added: {coupon_name}', 'multivendorx'), $this->object);
        }

        /**
         * get_content_html function.
         *
         * @access public
         * @return string
         */
        function get_content_html() {
            ob_start();
            wc_get_template($this->template_html, array(
                'coupon_name' => $this->coupon_name,
                'vendor_name' => $this->vendor_name,
                'post_id' => $this->post_id,
                'post_type' => $this->post_type,
                'email_heading' => $this->get_heading(),
                'email'         => $this,
                    ), 'MultiVendorX/', $this->template_base);

            return ob_get_clean();
        }

        /**
         * get_content_plain function.
         *
         * @access public
         * @return string
         */
        function get_content_plain() {
            ob_start();
            wc_get_template($this->template_plain, array(
                'coupon_name' => $this->coupon_name,
                'vendor_name' => $this->vendor_name,
                'post_id' => $this->post_id,
                'post_type' => $this->post_type,
                'email_heading' => $this->get_heading(),
                'email'         => $this,
                    ), 'MultiVendorX/', $this->template_base);

            return ob_get_clean();
        }

        /**
         * Initialise Settings Form Fields
         *
         * @access public
         * @return void
         */
        function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'multivendorx'),
                    'type' => 'checkbox',
                    'label' => __('Enable this email notification.', 'multivendorx'),
                    'default' => 'yes'
                ),
                'subject' => array(
                    'title' => __('Subject', 'multivendorx'),
                    'type' => 'text',
                    'description' => sprintf(__('This controls the email subject line. Leave it blank to use the default subject: <code>%s</code>.', 'multivendorx'), $this->get_default_subject()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'heading' => array(
                    'title' => __('Email Heading', 'multivendorx'),
                    'type' => 'text',
                    'description' => sprintf(__('This controls the main heading contained within the email notification. Leave it blank to use the default heading: <code>%s</code>.', 'multivendorx'), $this->get_default_heading()),
                    'placeholder' => '',
                    'default' => ''
                ),
                'email_type' => array(
                    'title' => __('Email Type', 'multivendorx'),
                    'type' => 'select',
                    'description' => __('Choose which format of email to be sent.', 'multivendorx'),
                    'default' => 'html',
                    'class' => 'email_type',
                    'options' => array(
                        'plain' => __('Plain Text', 'multivendorx'),
                        'html' => __('HTML', 'multivendorx'),
                        'multipart' => __('Multipart', 'multivendorx'),
                    )
                )
            );
        }
    }
endif;