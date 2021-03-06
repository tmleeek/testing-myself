document.observe('dom:loaded', function(){
    var companyNameInput = jQuery('#_accountcompany_name');
    var companyNameLabel = jQuery('label[for="_accountcompany_name"]');
    var requiredAsterisk = '<span id="companyNameRequired" class="required"> *</span>';
    var wholesaleId = -1;
    jQuery('#_accountwebsite_id option').each(function() {
        if(jQuery(this).html() == 'The Laundress Wholesale') {
            wholesaleId = jQuery(this).val();
        }
    });
    jQuery('#_accountwebsite_id').change(function() {
        if(jQuery(this).val() == wholesaleId) {
            companyNameInput.removeClass('validation-passed');
            companyNameInput.addClass('required-entry');
            companyNameLabel.append(requiredAsterisk);
        } else {
            companyNameInput.removeClass('required-entry');
            if(jQuery('#companyNameRequired').length > 0) {
               jQuery('#companyNameRequired').remove(); 
            }
            if(jQuery('#advice-required-entry-_accountcompany_name').length > 0) {
                jQuery('#advice-required-entry-_accountcompany_name').remove();
            }
        }
    });
});