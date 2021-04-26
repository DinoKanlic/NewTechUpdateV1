jQuery(document).ready(function($)
{
  var email_selector = $("input[name=woorechnung_invoice_email]");
  var email_selected = $("input[name=woorechnung_invoice_email]:checked");
  var email_to_append_to = $('#woorechnung_email_to_append_to').closest('tr');
  var email_for_methods = $('#woorechnung_no_email_for_methods').closest('tr');
  var email_for_states = $('#woorechnung_email_for_states').closest('tr');
  var email_subject = $('#woorechnung_email_subject').closest('tr');
  var email_copy = $('#woorechnung_email_copy').closest('tr');
  var email_contet_text = $('#woorechnung_email_content_text').closest('tr');
  var email_contet_html = $('#woorechnung_email_content_html').closest('tr');

  var change_settings = function(selection)
  {
    if ("none" === selection)
    {
      email_to_append_to.hide();
      email_for_states.hide();
      email_for_methods.hide();
      email_subject.hide();
      email_copy.hide();
      email_contet_text.hide();
      email_contet_html.hide();
    }
    if ("append" === selection)
    {
      email_to_append_to.show();
      email_for_methods.show();
      email_for_states.hide();
      email_subject.hide();
      email_copy.hide();
      email_contet_text.hide();
      email_contet_html.hide();
    }
    if ("separate" === selection)
    {
      email_to_append_to.hide();
      email_for_states.show();
      email_for_methods.show();
      email_subject.show();
      email_copy.show();
      email_contet_text.show();
      email_contet_html.show();
    };
  };

  email_selector.change(function(e)
  {
    change_settings($(this).val());
  });

  change_settings(email_selected.val());
});
