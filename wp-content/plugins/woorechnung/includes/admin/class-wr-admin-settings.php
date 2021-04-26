<?php

// Exit if accessed directly
if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists( 'WR_Admin_Settings' ) ):

/**
 * WooRechnung Admin Settings Component
 *
 * This class loads the settings page for the plugins admin backend.
 * The settings are to be set by the site admin in order to prepare the
 * plugin for work.
 *
 * @version  1.0.0
 * @package  WooRechnung\Admin
 * @author   Zweischneider
 */
final class WR_Admin_Settings extends WR_Abstract_Module
{
    /**
     * Initialize the hooks of this module.
     *
     * @return void
     */
    public function init_hooks()
    {
        if ( is_admin() ) {
            $this->add_filter( 'plugin_action_links_' . plugin_basename( $this->_plugin->get_file() ), 'plugin_action_links' );
            $this->add_filter( 'woocommerce_settings_tabs_array', 'add_settings_tab', 50 );
            $this->add_action( 'woocommerce_settings_tabs_woorechnung', 'add_settings' );
            $this->add_action( 'woocommerce_update_options_woorechnung', 'update_settings' );
            $this->add_filter( 'option_woorechnung_email_content_html', 'html_content_filter', 10, 2 );
        }
    }

    /**
     * Show action links on the plugin screen
     *
     * @param mixed $links
     *
     * @return array
     */
    public function plugin_action_links($links)
    {
        return array_merge( array(
            '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=woorechnung' ) . '">Einstellungen</a>',
        ), $links );
    }

    /**
     * Add the settings tab to the WooCommerce settings tabs.
     *
     * @param  array $settings_tabs
     * @return array
     */
    public function add_settings_tab($settings_tabs)
    {
        $settings_tabs = is_array($settings_tabs) ? $settings_tabs : array();
        $settings_tabs['woorechnung'] = 'WooRechnung';
        return $settings_tabs;
    }

    /**
     * Add the settings fields to the WooCommerce tab.
     *
     * @return void
     */
    public function add_settings()
    {
        woocommerce_admin_fields( $this->get_settings() );
    }

    /**
     * Update the settings with the current values.
     *
     * @return void
     */
    public function update_settings()
    {
        $data = $_POST;
        if (!empty($data['woorechnung_email_content_html'])) {
            $data['woorechnung_email_content_html'] = WR_Plugin::encode_html_content(
                $data['woorechnung_email_content_html']
            );
        }
        woocommerce_update_options( $this->get_settings(), $data );
    }

    /**
     * Filter for get options with html content as value.
     *
     * @return string
     */
    public function html_content_filter($value, $option)
    {
        return WR_Plugin::decode_html_content($value);
    }

    /**
     * Collect all settings fields for the WooCommerce tab.
     *
     * @return array
     */
    public function get_settings()
    {
        $settings = array();
        $settings = array_merge( $settings, $this->section_general() );
        $settings = array_merge( $settings, $this->section_invoice() );
        $settings = array_merge( $settings, $this->section_email() );
        return $settings;
    }


    private function get_order_states()
    {
        return array(
            'pending' => 'Zahlung ausstehend',
            'on-hold' => 'In Wartestellung',
            'processing' => 'In Bearbeitung',
            'completed' => 'Abgeschlossen',
        );
    }

    private function get_line_descriptions()
    {
        return array(
            '' => 'Keine Beschreibung',
            'article' => 'Produktbeschreibung',
            'short' => 'Produktkurzbeschreibung',
            'variation_title' => 'Variationstitel',
            'variation' => 'Variationsbeschreibung',
            'meta_data' => 'Metadaten',
            'mini_desc' => 'Warenkorbkurzbeschreibung',
        );
    }

    private function get_email_types()
    {
        $email_types = array();
        $email_types['new_order'] = 'Neue Bestellung (an Shopbetreiber)';
        $email_types['customer_processing_order'] = $this->plugin()->is_germanized_active()
            ? 'Bestellbestätigung (an Kunde)' : 'Bestellung in Bearbeitung (an Kunde)';
        $email_types['customer_on_hold_order'] = 'Bestellung wartend (an Kunde)';
        if ($this->plugin()->is_germanized_active()) {
            $email_types['customer_paid_for_order'] = 'Bestellung bezahlt (an Kunde)';
            $email_types['customer_shipment'] = 'Bestellung versandt (an Kunde)';
        }
        $email_types['customer_completed_order'] = 'Bestellung abgeschlossen (an Kunde)';
        return $email_types;
    }

    private function get_payment_methods()
    {
        $gateways = WC()->payment_gateways;
        $gateways = $gateways->payment_gateways();

        $result = array();

        foreach ($gateways as $id=>$gateway) {
            $title = $gateway->get_method_title();
            $result[$id] = $title;
        }

        return $result;
    }

    private function section_general()
    {
        $result = array();
        $result[] = $this->section_general_start();
        $result[] = $this->field_shop_url();
        $result[] = $this->field_shop_token();
        $result[] = $this->section_general_end();
        return $result;
    }

    private function section_invoice()
    {
        $result = array();
        $result[] = $this->section_invoice_start();
        $result[] = $this->field_create_invoices();
        $result[] = $this->field_invoices_for_states();
        $result[] = $this->field_no_invoices_for_methods();
        $result[] = $this->field_paid_for_methods();
        $result[] = $this->field_cancel_invoices();
        $result[] = $this->field_zero_value_invoices();
        $result[] = $this->field_open_invoices();
        $result[] = $this->field_customer_link();
        $result[] = $this->field_line_description();
        $result[] = $this->field_article_name_shipping();
        $result[] = $this->field_article_number_shipping();
        $result[] = $this->field_order_number_prefix();
        $result[] = $this->field_order_number_suffix();
        $result[] = $this->field_email_filename();
        $result[] = $this->section_invoice_end();
        return $result;
    }


    private function section_email()
    {
        $result = array();
        $result[] = $this->section_email_start();
        $result[] = $this->field_invoice_email();
        $result[] = $this->field_email_to_append_to();
        $result[] = $this->field_email_for_states();
        $result[] = $this->field_no_email_for_methods();
        $result[] = $this->field_email_subject();
        $result[] = $this->field_email_copy();
        $result[] = $this->field_email_content_text();
        $result[] = $this->field_email_content_html();
        $result[] = $this->section_email_end();
        return $result;
    }


    private function section_general_start()
    {
        return array(
            'id'        => 'woorechnung_section_general',
            'title'     => 'Grundeinstellungen',
            'type'      => 'title',
            'desc'      => 'Bitte gib die URL und das API-Token deines Shops ein, um eine Verbindung zu deinem WooRechnungs-Konto herzustellen. Das API-Token findest du in den Einstellungen deines Shops im Abschnitt "API-Token". Falls du noch kein Benutzerkonto bei WooRechnung hast, registriere dich kostenlos unter http://www.woorechnung.com.'
        );
    }

    private function section_general_end()
    {
        return array(
            'id'        => 'woorechnung_section_general',
            'type'      => 'sectionend',
        );
    }

    private function field_description_table($columns, $rows)
    {
        $table = '<table class="wr-desc-table">';
        $table .= '<tr>';
        foreach ($columns as $column) {
            $table .= '<th class="wr-desc-th">'.$column.'</th>';
        }
        $table .= '</tr>';
        foreach ($rows as $row) {
            $table .= '<tr>';
            foreach ($row as $data) {
                $table .= '<td class="wr-desc-td">'.$data.'</td>';
            }
            $table .= '</tr>';
        }
        $table .= '</table>';
        return $table;
    }

    private function field_shop_url()
    {
        return array(
            'id'        => 'woorechnung_shop_url',
            'title'     => 'Shop URL',
            'type'      => 'text',
            'default'   => get_home_url(),
            'desc_tip'  => 'Die Shop-URL sollte mit der URL übereinstimmen, die du
                            in deinem Account für deinen Shop festgelegt hast.',
        );
    }

    private function field_shop_token()
    {
        return array(
            'id'        => 'woorechnung_shop_token',
            'title'     => 'Shop Token',
            'type'      => 'text',
            'desc_tip'  => 'Das Shop-Token sollte mit dem Token übereinstimmen,
                            das du in deinem Account für deinen Shop erhalten hast.',
        );
    }


    private function section_invoice_start()
    {
        return array(
            'id'        => 'woorechnung_section_invoice',
            'type'      => 'title',
            'title'     => 'Rechnungseinstellungen',
            'desc'      => 'Bitte konfiguriere in diesem Abschnitt die Details zur Erstellung von Rechnungen in deinem Shop.',
        );
    }

    private function section_invoice_end()
    {
        return array(
            'id'        => 'woorechnung_section_invoice',
            'type'      => 'sectionend',
        );
    }

    private function field_create_invoices()
    {
        return array(
            'id'        => 'woorechnung_create_invoices',
            'title'     => 'Automatische Erstellung',
            'type'      => 'checkbox',
            'desc'      => 'Rechnungen automatisch für Bestellungen erstellen',
            'default'   => 'yes',
            'desc_tip'  => 'Wenn du diese Option aktivierst, werden automatisch Rechnungen
                            für Bestellungen erstellt, wenn diese einen bestimmten Status
                            und eine bestimmte Zahlungsart aufweisen. Für welchen Bestellstatus
                            und welche Zahlungsart Rechnungen erstellt werden sollen, kannst du
                            mit den folgenden zwei Optionen konfigurieren.',
        );
    }

    private function field_invoices_for_states()
    {
        return array(
            'id'        => 'woorechnung_invoice_for_states',
            'title'     => 'Erstellung für Bestellstatus',
            'desc'      => '',
            'class'     => 'chosen_select',
            'type'      => 'multiselect',
            'options'   => $this->get_order_states(),
            'default'   => array('completed'),
            'desc_tip'  => 'Wähle bitte alle Bestellstati aus, für die die automatische Erstellung
                            von Rechnungen durch WooRechnung erfolgen soll.',
        );
    }

    private function field_no_invoices_for_methods()
    {
        return array(
            'id'        => 'woorechnung_no_invoice_for_methods',
            'title'     => 'Ausnahmen für Zahlungsarten',
            'desc'      => '',
            'class'     => 'chosen_select',
            'type'      => 'multiselect',
            'options'   => $this->get_payment_methods(),
            'default'   => array(),
            'desc_tip'  => 'Wähle bitte alle Zahlungsarten aus, für die die automatische Erstellung
                            von Rechnungen durch WooRechnung NICHT erfolgen soll.',
        );
    }

    private function field_paid_for_methods()
    {
        return array(
            'id'        => 'woorechnung_paid_for_methods',
            'title'     => '"Bezahlt" je Zahlungsart',
            'desc'      => '',
            'class'     => 'chosen_select',
            'type'      => 'multiselect',
            'options'   => $this->get_payment_methods(),
            'default'   => array(),
            'desc_tip'  => 'Wähle bitte alle Zahlungsarten aus, für die deine Rechnung als
                            "bezahlt" markiert werden soll.',
        );
    }

    private function field_zero_value_invoices()
    {
        return array(
            'id'        => 'woorechnung_zero_value_invoices',
            'title'     => 'Erstellung bei 0€',
            'type'      => 'checkbox',
            'desc'      => 'Erstellung von Rechnungen über 0€',
            'default'   => 'no',
            'desc_tip'  => 'Wenn du diese Option aktivierst, werden Rechnungen auch für
                            Bestellungen erstellt, die einen Gesamtwert von 0 Euro aufweisen.
                            Standardmäßig werden für diese Bestellungen keine Rechnungen
                            erstellt.',
        );
    }

    private function field_cancel_invoices()
    {
        return array(
            'id'        => 'woorechnung_cancel_invoices',
            'title'     => 'Automatische Stornierung',
            'type'      => 'checkbox',
            'desc'      => 'Rechnungen automatisch stornieren',
            'default'   => 'no',
            'desc_tip'  => 'Wenn du diese Option aktivierst, werden automatisch Stornorechnungen
                            erstellt, wenn eine Bestellung stoniert wird. Die Stornierung erfolgt
                            nur dann, wenn du einen externen Rechnungsdienst nutzt.',
        );
    }

    private function field_open_invoices()
    {
        return array(
            'id'        => 'woorechnung_open_invoices',
            'title'     => 'Rechnung öffnen',
            'type'      => 'checkbox',
            'desc'      => 'Rechnung öffnen statt direkt herunterladen',
            'default'   => 'no',
            'desc_tip'  => 'Wenn du diese Option aktivierst, werden Rechnungen, die du über den
                            Rechnungsbutton in der Bestellübersicht generierst, im Browser geöffnet
                            statt einen Download der Rechnungs-PDF zu starten.',
        );
    }

    private function field_customer_link()
    {
        return array(
            'id'        => 'woorechnung_customer_link',
            'title'     => 'Rechnung für Kunde',
            'type'      => 'checkbox',
            'desc'      => 'Rechnung für Kunden zum Download bereitstellen',
            'default'   => 'no',
            'desc_tip'  => 'Wenn du diese Option aktivierst, wird der Bestellübersicht im Konto
                            deines Kunden ein Link zum Download seiner Rechnung hinzugefügt. Der Link
                            erscheint erst dann, wenn eine Rechnung zu seiner Bestellung erzeugt wurde.',
        );
    }

    private function field_line_description()
    {
        return array(
            'id'        => 'woorechnung_line_description',
            'title'     => 'Produktbeschreibung',
            'type'      => 'select',
            'options'   => $this->get_line_descriptions(),
            'desc_tip'  => 'Text, der als Produktbeschreibung übertragen werden soll.',
            'default'   => 'article',
        );
    }

    private function field_article_name_shipping()
    {
        return array(
            'id'        => 'woorechnung_article_name_shipping',
            'title'     => 'Bezeichnung Versandkosten',
            'type'      => 'text',
            'desc'      => '',
            'default'   => '',
            'desc_tip'  => 'Die Bezeichnung, die für alle unterschiedlichen Versandkostenarten auf
                            der Rechnung erscheinen soll (optional).',
        );
    }

    private function field_article_number_shipping()
    {
        return array(
            'id'        => 'woorechnung_article_number_shipping',
            'title'     => 'Artikelnummer Versandkosten',
            'type'      => 'text',
            'desc'      => '',
            'default'   => '',
            'desc_tip'  => 'Die Artikelnummer, die für Versandkosten auf der Rechnung erscheinen soll.',
        );
    }

    private function field_order_number_prefix()
    {
        return array(
            'id'        => 'woorechnung_order_number_prefix',
            'title'     => 'Bestellnummer Prefix',
            'type'      => 'text',
            'desc'      => '',
            'default'   => '',
            'desc_tip'  => 'Ein optionales Prefix für die ursprüngliche Bestellnummer.',
        );
    }

    private function field_order_number_suffix()
    {
        return array(
            'id'        => 'woorechnung_order_number_suffix',
            'title'     => 'Bestellnummer Suffix',
            'type'      => 'text',
            'desc'      => '',
            'default'   => '',
            'desc_tip'  => 'Ein optionales Suffix für die ursprüngliche Bestellnummer.',
        );
    }

    private function field_email_filename()
    {
        $variables = $this->plugin()->get_invoice_filename_variables();
        $rows = [];
        foreach ($variables as $name => $description) {
            $rows[] = array('%'.$name.'%', $description);
        }
        $desc = $this->field_description_table(array('Variable', 'Beschreibung'), $rows);
        return array(
            'id'        => 'woorechnung_email_filename',
            'title'     => 'Dateiname der PDF',
            'type'      => 'text',
            'desc'      => $desc,
            'default'   => '',
            'desc_tip'  => 'Der Name der Rechnungsdatei, die per E-Mail versendet wird (ohne Dateiendung!).',
        );
    }

    private function section_email_start()
    {
        return array(
            'id'         => 'woorechnung_section_email',
            'type'       => 'title',
            'title'      => 'E-Mail-Einstellungen',
            'desc'       => 'Bitte konfiguriere in diesem Abschnitt den Versand deiner Rechnungen per E-Mail.',
        );
    }

    private function section_email_end()
    {
        return array(
            'id'         => 'woorechnung_section_email',
            'type'       => 'sectionend',
        );
    }

    private function field_invoice_email()
    {
        return array(
            'id'         => 'woorechnung_invoice_email',
            'title'      => 'E-Mail-Versand',
            'desc'       => '',
            'type'       => 'radio',
            'default'    => 'append',
            'options'    => array(
                'none'      => 'Rechnung nicht per E-Mail versenden',
                'append'    => 'Rechnung an WooCommerce-E-Mail anhängen',
                'separate'  => 'Rechnung in separater E-Mail versenden',
            )
        );
    }

    private function field_email_to_append_to()
    {
        return array(
            'id'        => 'woorechnung_email_to_append_to',
            'title'     => 'E-Mail für Anhang',
            'class'     => 'chosen_select',
            'type'      => 'multiselect',
            'desc_tip'  => 'E-Mail, an die die Rechnung angehängt werden soll.',
            'default'   => array('customer_processing_order'),
            'options'   => $this->get_email_types(),
        );
    }

    private function field_email_for_states()
    {
        return array(
            'id'        => 'woorechnung_email_for_states',
            'title'     => 'E-Mail für Bestellstatus',
            'desc'      => '',
            'desc_tip'  => true,
            'class'     => 'chosen_select',
            'type'      => 'multiselect',
            'options'   => $this->get_order_states(),
            'default'   => array('completed'),
            'desc_tip'  => 'Die Bestellstati, für die die Rechnung als eigene
                            E-Mail versendet werden soll.',
        );
    }

    private function field_no_email_for_methods()
    {
        return array(
            'id'        => 'woorechnung_no_email_for_methods',
            'title'     => 'Ausnahmen für Zahlungsarten',
            'desc'      => '',
            'desc_tip'  => true,
            'class'     => 'chosen_select',
            'type'      => 'multiselect',
            'options'   => $this->get_payment_methods(),
            'default'   => array(),
            'desc_tip'  => 'Die Zahlungsmethoden, für die die Rechnung NICHT als
                            E-Mail versendet werden soll.',
        );
    }

    private function field_email_subject()
    {
        return array(
            'id'        => 'woorechnung_email_subject',
            'title'     => 'E-Mail Betreff',
            'type'      => 'text',
            'desc'      => '',
            'default'   => '',
            'desc_tip'  => 'Der Betreff der Rechnungs E-Mail.',
        );
    }


    private function field_email_copy()
    {
        return array(
            'title'     => 'E-Mail CC',
            'id'        => 'woorechnung_email_copy',
            'type'      => 'text',
            'desc'      => '',
            'default'   => '',
            'desc_tip'  => 'Weitere E-Mail-Adressen, an die die E-Mail als Kopie gesendet werden soll.
                            Trennung der Adressen durch Semikolon.',
        );
    }

    private function field_email_content_text()
    {
        return array(
            'id'        => 'woorechnung_email_content_text',
            'title'     => 'E-Mail Textinhalt',
            'desc'      => '',
            'desc_tip'  => true,
            'default'   =>  '',
            'css'       => 'height: 100px;',
            'type'      => 'textarea',
            'desc_tip'  => 'Der Textinhalt deiner Rechnungs-E-Mail. HTML ist hier nicht erlaubt.',
        );
    }

    private function field_email_content_html()
    {
        return array(
            'id'        => 'woorechnung_email_content_html',
            'title'     => 'E-Mail HTML-Inhalt',
            'desc'      => '',
            'desc_tip'  => true,
            'default'   =>  '',
            'css'       => 'height: 100px;',
            'type'      => 'textarea',
            'desc_tip'  => 'Der HTML-Inhalt deiner Rechnungs-E-Mail. Hier darfst du HTML verwenden.',
        );
    }
}

endif;
