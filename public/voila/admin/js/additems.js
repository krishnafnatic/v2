// add items to kitchen
$('.firstadd').on('click',function(event){
    event.preventDefault();
    var temp = '#form'+this.id;
    var id = '#'+this.id;
    var nu = '#rec_'+this.id;
    const arr = $(temp).serializeArray(); // get the array
  const data = arr.reduce((acc, {name, value}) => ({...acc, [name]: value}),{}); // form the object
  // console.log(data);
  var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
  $.ajax({
    /* the route pointing to the post function */
    url: '/admin/kitchen',
    type: 'POST',
    /* send the csrf-token and the input to the controller */
    data: {_token: CSRF_TOKEN, item_id:this.id, action:'addon',totaldata: data},
    dataType: 'JSON',
    /* remind that 'data' is the response of the AjaxController */
    success: function (data) {
        $("#kitchen_total").html(data.msg); 
        $(id).next('div').children('input').val(function(i, oldval) {
            return ++oldval;
        });
        $(id).hide("fast");
        $(id).next("div").show("fast");
        $(nu).next('div').children('input').val(function(i, oldval) {
            return ++oldval;
        });
        $(nu).hide("fast");
        $(nu).next("div").show("fast");
        refresh();
    }
});
  $('#n'+this.id).modal('hide');
});


//plusbutton on main menu
$('.plus').on('click',function(event) {
	event.preventDefault();
 //    $(this).prev('input').val(function(i, oldval) {
 //    return ++oldval;
	// });
    var get_id = this.id;
    console.log(get_id);
    if(get_id.includes('rec_'))
    {   
        var get_id = get_id.slice(4);
    }
	var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
	$.ajax({
        /* the route pointing to the post function */
        url: '/admin/customize',
        type: 'POST',
        /* send the csrf-token and the input to the controller */
        data: {_token: CSRF_TOKEN, item_id:get_id},
        dataType: 'JSON',
        /* remind that 'data' is the response of the AjaxController */
        success: function (data) { 
            $("#addcustomization").empty();
                        // $("#addcustomization").append('<div class="col-sm-8 add-cust-box-pop"> <img src="assets/img/ic-veg.svg" class="veg-badge mr-1 d-inline"> Bahawalpur Ganne Ka Ras <p> Addons that have been added to the dish will come here </p> </div> <div class="col-sm-4"> <div class="input-group d-block float-right"> <button class="btn btn-light btn-sm float-left" id="minus-btn"><img src="assets/img/ic-minus.svg" class="d-inline"></button> <input type="number" id="qty_input" class="add-plus-min float-left" value="0" min="0"> <button class="btn btn-light btn-sm float-left" id="plus-btn"><img src="assets/img/ic-plus.svg" class="d-inline"></button> </div> </div>');
                        // $("#addcustomization").append('<textarea>'+data.item_details[0].created_at+'</textarea>');
                        console.log(data);
                        console.log(data.kitchen_custom.length);
                        console.log(data.item_details[0].item_vegetarian);
                        var appendString = "";
                        for(var i = 0; i < data.kitchen_custom.length; i++){
                          appendString += '<div class="col-sm-8 add-cust-box-pop" id="custom' + data.kitchen_custom[i].id + '"> <img src="/voila/dine_in_asset/img/ic-' + data.item_details[0].item_vegetarian + '.svg" class="veg-badge mr-1 d-inline">' + data.item_details[0].item_name + '<p>';
                          for(var j=0; j < data.kitchen_addon.length; j++){
                              if(data.kitchen_addon[j].order_id == data.kitchen_custom[i].id){
                                  if(data.kitchen_addon[j].addon_name == 'note'){
                                      appendString += "Note: " + data.kitchen_addon[j].addon_value;
                                  }
                                  else
                                    appendString += data.kitchen_addon[j].addon_name + " ";
                            }
                        }
                        appendString += '</p> </div> <div class="col-sm-4" id="custombutton' + data.kitchen_custom[i].id + '"> <div class="input-group d-block float-right"> <button class="btn btn-light btn-sm float-left customize-minus" id="' + data.kitchen_custom[i].id + '"><img src="/voila/dine_in_asset/img/ic-minus.svg" class="d-inline"></button> <input type="number" id="qty_input" class="add-plus-min float-left" value="' + data.kitchen_custom[i].quantity + '" min="0" disabled> <button class="btn btn-light btn-sm float-left customize-plus" id="' + data.kitchen_custom[i].id + '"><img src="/voila/dine_in_asset/img/ic-plus.svg" class="d-inline"></button> </div> </div>';
                    }
                    newappend = '<h5> <img id="foodbadge" src="/voila/dine_in_asset/img/ic-nonveg.svg" class="veg-badge mr-1 d-inline">' + data.item_details[0].item_name + '</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>'  
                    $('#addcustomization_header').empty().append(newappend);
                    $("#addcustomization").append(appendString);
                    $('#foodbadge').attr('src','/voila/dine_in_asset/img/ic-'+data.item_details[0].item_vegetarian+'.svg');
                    $('#customize-modal-id').attr('data-target','#n'+data.item_details[0].item_id);
                    $("#kitchen_total").html(data.total_items); 

                    //here lies inside plus minus
                    $(document).ready(function(){
                        $('.customize-plus').click(function() {
                            // alert(this.id);
                            $(this).prev('input').val(function(i, oldval) {
                                        return ++oldval;
                                    });
                            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                /* the route pointing to the post function */
                                url: '/admin/customize',
                                type: 'POST',
                                /* send the csrf-token and the input to the controller */
                                data: {_token: CSRF_TOKEN, item_id:this.id, action:'add'},
                                dataType: 'JSON',
                                /* remind that 'data' is the response of the AjaxController */
                                success: function (data) { 
                                    console.log(data.status);
                                    $("#kitchen_total").html(data.total_items);
                                    var temp = '#qty_input' + data.item_id;
                                        $(temp).val(data.item_quantity);
                                    var temp1 = '#rec_qty_input' + data.item_id;
                                        $(temp1).val(data.item_quantity);
                                        refresh();
                                }
                            });
                        });
                        $('.customize-minus').click(function() {
                            // alert(this.id);
                            $(this).next('input').val(function(i, oldval) {
                                        return --oldval;
                                    });
                            var id = '#custom'+this.id;
                            var sec_id = '#custombutton' + this.id;
                            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                /* the route pointing to the post function */
                                url: '/admin/customize',
                                type: 'POST',
                                /* send the csrf-token and the input to the controller */
                                data: {_token: CSRF_TOKEN, item_id:this.id, action:'minus'},
                                dataType: 'JSON',
                                /* remind that 'data' is the response of the AjaxController */
                                success: function (data) { 
                                    console.log(data.delete_status);
                                    $("#kitchen_total").html(data.total_items);
                                    if(data.delete_status == 'success'){
                                        console.log(this.id);
                                        $(id).hide();
                                        $(id).remove();
                                        $(sec_id).hide();
                                        $(sec_id).remove();
                                        if($.trim($("#addcustomization").html())==''){
                                            $('#customize-pop').modal('hide');
                                        }
                                    }
                                    if(data.item_quantity == 0){
                                        var temp = '#qty_input' + data.item_id;
                                        $(temp).parent('div').parent('div').children('button').show();
                                        $(temp).val(0);
                                        $(temp).parent('div').hide();
                                        var temp1 = '#rec_qty_input' + data.item_id;
                                        $(temp1).parent('div').parent('div').children('button').show();
                                        $(temp1).val(0);
                                        $(temp1).parent('div').hide();
                                    }
                                    else{
                                        var temp = '#qty_input' + data.item_id;
                                        $(temp).val(data.item_quantity);
                                        var temp1 = '#rec_qty_input' + data.item_id;
                                        $(temp1).val(data.item_quantity);
                                    }
                                    refresh();
                                }
                            });
                        });
                    });

                    $('#customize-pop').modal('show');
                }
            }); 
});


$('.minus').on('click',function(event) {
    event.preventDefault();
 //    $(this).prev('input').val(function(i, oldval) {
 //    return ++oldval;
    // });
    var get_id = this.id;
    console.log(get_id);
    if(get_id.includes('rec_'))
    {   
        var get_id = get_id.slice(4);
    }
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        /* the route pointing to the post function */
        url: '/admin/customize',
        type: 'POST',
        /* send the csrf-token and the input to the controller */
        data: {_token: CSRF_TOKEN, item_id:get_id},
        dataType: 'JSON',
        /* remind that 'data' is the response of the AjaxController */
        success: function (data) { 
            $("#addcustomization").empty();
                        // $("#addcustomization").append('<div class="col-sm-8 add-cust-box-pop"> <img src="assets/img/ic-veg.svg" class="veg-badge mr-1 d-inline"> Bahawalpur Ganne Ka Ras <p> Addons that have been added to the dish will come here </p> </div> <div class="col-sm-4"> <div class="input-group d-block float-right"> <button class="btn btn-light btn-sm float-left" id="minus-btn"><img src="assets/img/ic-minus.svg" class="d-inline"></button> <input type="number" id="qty_input" class="add-plus-min float-left" value="0" min="0"> <button class="btn btn-light btn-sm float-left" id="plus-btn"><img src="assets/img/ic-plus.svg" class="d-inline"></button> </div> </div>');
                        // $("#addcustomization").append('<textarea>'+data.item_details[0].created_at+'</textarea>');
                        console.log(data);
                        console.log(data.kitchen_custom.length);
                        console.log(data.item_details[0].item_vegetarian);
                        var appendString = "";
                        for(var i = 0; i < data.kitchen_custom.length; i++){
                          appendString += '<div class="col-sm-8 add-cust-box-pop" id="custom' + data.kitchen_custom[i].id + '"> <img src="/voila/dine_in_asset/img/ic-' + data.item_details[0].item_vegetarian + '.svg" class="veg-badge mr-1 d-inline">' + data.item_details[0].item_name + '<p>';
                          for(var j=0; j < data.kitchen_addon.length; j++){
                              if(data.kitchen_addon[j].order_id == data.kitchen_custom[i].id){
                                  if(data.kitchen_addon[j].addon_name == 'note'){
                                      appendString += "Note: " + data.kitchen_addon[j].addon_value;
                                  }
                                  else
                                    appendString += data.kitchen_addon[j].addon_name + " ";
                            }
                        }
                        appendString += '</p> </div> <div class="col-sm-4" id="custombutton' + data.kitchen_custom[i].id + '"> <div class="input-group d-block float-right"> <button class="btn btn-light btn-sm float-left customize-minus" id="' + data.kitchen_custom[i].id + '"><img src="/voila/dine_in_asset/img/ic-minus.svg" class="d-inline"></button> <input type="number" id="qty_input" class="add-plus-min float-left" value="' + data.kitchen_custom[i].quantity + '" min="0" disabled> <button class="btn btn-light btn-sm float-left customize-plus" id="' + data.kitchen_custom[i].id + '"><img src="/voila/dine_in_asset/img/ic-plus.svg" class="d-inline"></button> </div> </div>';
                    }
                    newappend = '<h5> <img id="foodbadge" src="/voila/dine_in_asset/img/ic-nonveg.svg" class="veg-badge mr-1 d-inline">' + data.item_details[0].item_name + '</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>'  
                    $('#addcustomization_header').empty().append(newappend);  
                    $("#addcustomization").append(appendString);
                    $('#foodbadge').attr('src','/voila/dine_in_asset/img/ic-'+data.item_details[0].item_vegetarian+'.svg');
                    $('#customize-modal-id').attr('data-target','#n'+data.item_details[0].item_id);
                    $("#kitchen_total").html(data.total_items); 

                    //here lies inside plus minus
                    $(document).ready(function(){
                        $('.customize-plus').click(function() {
                            // alert(this.id);
                            $(this).prev('input').val(function(i, oldval) {
                                        return ++oldval;
                                    });
                            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                /* the route pointing to the post function */
                                url: '/admin/customize',
                                type: 'POST',
                                /* send the csrf-token and the input to the controller */
                                data: {_token: CSRF_TOKEN, item_id:this.id, action:'add'},
                                dataType: 'JSON',
                                /* remind that 'data' is the response of the AjaxController */
                                success: function (data) { 
                                    console.log(data.status);
                                    $("#kitchen_total").html(data.total_items);
                                    var temp = '#qty_input' + data.item_id;
                                        $(temp).val(data.item_quantity);
                                        var temp1 = '#rec_qty_input' + data.item_id;
                                        $(temp1).val(data.item_quantity);
                                        refresh();
                                }
                            });
                        });
                        $('.customize-minus').click(function() {
                            // alert(this.id);
                            $(this).next('input').val(function(i, oldval) {
                                        return --oldval;
                                    });
                            var id = '#custom'+this.id;
                            var sec_id = '#custombutton' + this.id;
                            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                            $.ajax({
                                /* the route pointing to the post function */
                                url: '/admin/customize',
                                type: 'POST',
                                /* send the csrf-token and the input to the controller */
                                data: {_token: CSRF_TOKEN, item_id:this.id, action:'minus'},
                                dataType: 'JSON',
                                /* remind that 'data' is the response of the AjaxController */
                                success: function (data) { 
                                    console.log(data.delete_status);
                                    $("#kitchen_total").html(data.total_items);
                                    if(data.delete_status == 'success'){
                                        console.log(this.id);
                                        $(id).hide();
                                        $(id).remove();
                                        $(sec_id).hide();
                                        $(sec_id).remove();
                                        if($.trim($("#addcustomization").html())==''){
                                            $('#customize-pop').modal('hide');
                                        }
                                    }
                                    if(data.item_quantity == 0){
                                        var temp = '#qty_input' + data.item_id;
                                        $(temp).parent('div').parent('div').children('button').show();
                                        $(temp).val(0);
                                        $(temp).parent('div').hide();
                                        var temp1 = '#rec_qty_input' + data.item_id;
                                        $(temp1).parent('div').parent('div').children('button').show();
                                        $(temp1).val(0);
                                        $(temp1).parent('div').hide();
                                    }
                                    else{
                                        var temp = '#qty_input' + data.item_id;
                                        $(temp).val(data.item_quantity);
                                        var temp1 = '#rec_qty_input' + data.item_id;
                                        $(temp1).val(data.item_quantity);
                                    }
                                    refresh();
                                }
                            });
                        });
                    });

                    $('#customize-pop').modal('show');
                }
            }); 
});

function refresh()
{
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var tbno = $('#tbno').val();
    $.ajax({
                    /* the route pointing to the post function */
                    url: "/admin/refreshsection",
                    type: 'POST',
                    /* send the csrf-token and the input to the controller */
                    data: {_token: CSRF_TOKEN,tbno:tbno},
                    /* remind that 'data' is the response of the AjaxController */
                    success: function (data) { 
                      console.log(data);
                      $('.replace').html(data)
                    }
                });  
}