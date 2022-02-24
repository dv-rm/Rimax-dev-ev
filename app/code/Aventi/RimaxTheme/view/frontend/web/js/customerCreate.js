require(['jquery', 'jquery/ui'], function ($) {

    $(window).ready(function () {
        $('.field.taxvat').hide();
        $( ".field-id_verif_digit" ).hide()

        var id_number = $('#id_number');
        id_number.attr('type', 'number');
        id_number.attr('minlength', '7');
        id_number.attr('maxlength', '12');
        var mobile = $('#mobile');
        mobile.attr('type', 'number');
        mobile.attr('minlength', '7');
        mobile.attr('maxlength', '12');

        id_number.onKeyPress(function () {
            if(this.value.length==10 || event.keyCode==69 || event.keyCode==101) return false;
        })
        $( "#id_number" ).keyup(function () {
            $('#taxvat').val($(this).val());
        })

        $( "#id_type" ).change(function () {
            if($('#id_type option:selected').text()=='NIT')
            {
                $( ".field-id_verif_digit" ).show()
            }
            else {
                $( ".field-id_verif_digit" ).hide()
            }
        })
    })
});
