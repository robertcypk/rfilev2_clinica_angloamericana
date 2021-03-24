function Select(){
    var select = $('#valor');
    $(select).on('change', function(){
        var val = $(this).val();
        $('.contentMetricas .indicadores').hide();
        $('#'+val).fadeIn(300);
        console.log(val);
        console.log(val2);
    })
}
 $(document).ready(function(){
        //initialize the javascript
        App.init();
        App.menuActive(1,2);
		App.dataTables();

        Select();

        })